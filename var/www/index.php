<?php

 	$isSecure = false;
	if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) { $isSecure = true; }
	elseif (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' || !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on') {
		$isSecure = true;
	}
	$REQUEST_PROTOCOL = $isSecure ? 'https' : 'http';
	if(!$isSecure || $_SERVER['HTTP_HOST'] != "reserve.bristolinn.com") {
		$redirect=urlencode($REQUEST_PROTOCOL."://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		header("Location: https://reserve.bristolinn.com?redirect={$redirect}");
		exit;
	}
// 	if(isset($_GET['session'])) {
// 		include("iglooportal/status.php");
// 		exit;
// 	}

  global $a,$b,$private_id,$site_name,$redirect,$usersjson,$AUTHURL;

  $site_name="Bristol Inn";
  $SESSIONS="/var/lib/iglooportal/sessions";
  $AUTHURL="https://wifi.choggiung.com/auth.php";

  ini_set('display_errors',1); 
  error_reporting(E_ALL); //error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

  include "iglooportal/sessions.php";

  loadSession();
  setGlobalIni();
  saveSession();
  header("Connection: close");

  // HANDLE INPUTS
  $redirect=input("redirect");
  $user=input("user");
  $pass=input("pass");
	$submit=input("submit");
  $doauth=input("doauth");
  
  $authenticated = 0;
  $reason="";
  $overuse=$GLOBALS['innportal_OVERUSE'];
  $mblimit=floor($overuse/1000000);
  if($submit != "") {
		$macaddr=shell_exec("getmac ".$_SERVER['REMOTE_ADDR']);
    //$users = json_decode(file_get_contents("/var/lib/innproxy/users.json"));
    $user = preg_replace('/[^\p{L}\p{N}\s]/u', '', $user); // Replace symbols to sanitize input
    $pass = preg_replace('/[^\p{L}\p{N}\s]/u', '', $pass); // Replace symbols to sanitize input
		$auth=file_get_contents("{$AUTHURL}?user={$user}&pass={$pass}&ipaddr=".$_SERVER['REMOTE_ADDR']."&macaddr={$macaddr}"); // NO,OK,DI(sabled),OV(eruse)
    if     ($auth == "OK") {
			$authenticated=1;			
			file_put_contents($SESSIONS."/session-".$private_id,"user={$user}\npass={$pass}");
		}
    else if($auth == "DI") { $reason="This account is disabled."; }
    else if($auth == "OV") { $reason="Daily usage exceeds limit.<br />Wait until 11 am to try again."; }
    else { $reason="Invalid username or password."; }
  }
  if($doauth == 1) {
		echo $authenticated;
		return;
  }
  if($authenticated != 1) {
    include "iglooportal/login.php";
  } else {
		//include "iglooportal/grantaccess.php";
		//header("Location: https://reserve.bristolinn.com:8443/index.php?session=$private_id&redirect=$redirect");
		//header("Location: http://192.168.42.1:8080/status/?session=$private_id&count=0&redirect=$redirect");
		$ip=$_SERVER['REMOTE_ADDR'];
		$redirect=$_GET['redirect'];
		$cmd="sudo /usr/bin/loginip '".$ip."' '' '".$user."'";
		$result=shell_exec($cmd);
		if(substr($result, 0, 14) == "/sbin/iptables" || strpos($result, "already authorized") !== false) {
			sleep(3);
			//header("Location: {$redirect}");
			header("Location: https://reserve.bristolinn.com/iglooportal/grantaccess.php?session=$private_id&count=0&redirect=$redirect");
			return;
		}
		else {
			echo "result($cmd)=$result";
		}
  }

//echo $private_id; 
//	echo hash('sha256', 'g8xxlx');

?>
