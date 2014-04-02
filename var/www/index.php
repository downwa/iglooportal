<?php

$SESSIONS="/var/lib/iglooportal/sessions/";

$ip=$_SERVER['REMOTE_ADDR'];
$session=$_GET['session'];
$redirect=$_GET['redirect'];
$file=$SESSIONS."/".$session;
$error0="";
// Give time for server response to populate user
for($x=0; $x<5; $x++) {
	if(file_exists($file)) { break; }
	sleep(1);
}
if(file_exists($file)) {
	$user=trim(file_get_contents($SESSIONS."/".$session));
	if($user != "") {
		$cmd="sudo /usr/bin/loginip '".$ip."' '' '".$user."'";
		$result=shell_exec($cmd);
		if(substr($result, 0, 14) == "/sbin/iptables") {
			sleep(3);
			header("Location: https://reserve.bristolinn.com:8444/status/index.php?session=$session&count=0&redirect=$redirect");
			return;
		}
		$error0="ERROR: cmd=".$cmd."; result=".$result;
	}
}
$error="Login failed.  Please try again.";
echo($error0."<br />".$error);
echo("<script>alert('".$error."');document.location='<?=$redirect?>'</script>");

?>
