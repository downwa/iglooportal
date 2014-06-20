<!-- <?php
	// --><script lang="javascript">setTimeout("document.getElementById('b').innerHTML='Server Update Error: Contact System Administrator';",1000);</script><!--
	function now() { // Output the date in m/d/Y H:i:s format
		$date = new DateTime();
		return $date->format('m/d/Y H:i:s');
	}

  global $a,$b,$private_id,$site_name,$ipaddr,$redirect,$AUTHURL;

  $site_name="Bristol Inn";
  $SESSIONS="/var/lib/iglooportal/sessions";
  $AUTHURL="https://wifi.choggiung.com/auth.php";
  //$USERS="/var/lib/innproxy/users.json";

  ini_set('display_errors',1); 
  error_reporting(E_ALL); //error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

  include "sessions.php";
  setGlobalIni();
  $overuse=$GLOBALS['innportal_OVERUSE'];
  $mblimit=floor($overuse/1000000);
  

  $redirect=input("redirect");
  $session=input("session");
  $count=input("count");
  $logout=input("logout");
  if($count == "") { $count=0; }
  $count++;
  $ipaddr=$_SERVER['REMOTE_ADDR'];
  
  $userpass = @parse_ini_file($SESSIONS."/session-".$session);
	$user=$userpass['user'];
	$pass=$userpass['pass'];
	$userinfo=parse_ini_string(file_get_contents("https://wifi.choggiung.com/status.php?user={$user}&pass={$pass}"),false,INI_SCANNER_RAW);
	$bytes=0;
	
	// Get local usage
	$usage = fopen("/var/lib/iglooportal/usage.csv","r");
	while(!feof($usage)) {
		$line=fgetcsv($usage);
		if(isset($line[1]) && $line[1] == $user) { $bytes=$line[0]; break; }
	}
	fclose($usage);
	
	$mbytes=0;
	$pct=0;
	$leave="";
	$disabled="";
	$loggedin="";
	$hhmmss="";
	if($logout=="") { // logout is blank
		if($userinfo['ipaddr'] == $ipaddr || $userinfo['ipaddr'] == "") {
			$active=$userinfo['active'];
			$stay=$userinfo['stay'];
			$leave=$userinfo['leave'];
			$disabled=$userinfo['disabled']?"Yes":"No";
			$bytes=max($bytes,$userinfo['bytes']); // Use greater of Remote bytes and local bytes
			$pct=round($bytes*100/$overuse);
			$mbytes=round($bytes/1000000);
			
// 			$datetime1 = strtotime(now());
// 			$dt2=strtotime($leave." 11:00 AM");
// 			$datetime2 = strtotime($active)+$stay; 
// 			if($dt2 < $datetime2) { $leave=""; }
//			else { $leave=$leave." at 11 am "; }
			if ($leave != "") { $leave=$leave." at 11 am "; }
			$secsleft = $userinfo['secsleft']; //($datetime2 - $datetime1);
			if($secsleft > 0) {
				$hh = floor($secsleft / 3600);
				$ss = floor($secsleft % 3600);
				$mm = floor($ss / 60);
				$ss = floor($ss % 60);
				$hhmmss = sprintf("%02d:%02d:%02d",$hh,$mm,$ss);
			}
			else { $hhmmss="00:00:00"; }
			//$hrsleft = round(($datetime2 - $datetime1) / 3600, 3);
		}
		// Check local logged-in users
		$loggedin="No";
		$users = fopen("/var/lib/iglooportal/users.txt","r");
		while(!feof($users)) {
			$line=fgetcsv($users);
			if(isset($line[3]) && $line[3] == $user) { $loggedin="Yes"; break; }
		}
		fclose($users);

		// Check remote record of logged in users		
		$loggedin=($loggedin == "Yes" && $userinfo['ipaddr'] != "")?"Yes":"No";
		if($loggedin == "Yes") { touch($SESSIONS."/session-".$session); } // Keep this session alive, if it is logged in
	}
	else { // logout is non-blank
		$auth=file_get_contents("{$AUTHURL}?user={$user}&pass={$pass}&ipaddr=&macaddr="); // NO,OK,DI(sabled),OV(eruse)
    //if ($auth == "OK") { }
		unlink($SESSIONS."/session-".$session);
		$redirect=$_GET['redirect'];
		$cmd="sudo /usr/bin/logoutnet '{$user}'";
		$result=shell_exec($cmd);
		//die("result=".$result);
		//if(strpos($result, "not authorized") !== false) { } // Success
		sleep(3);
		header("Location: https://reserve.bristolinn.com:447/?redirect=http://google.com");
		return;
	} // ENDIF logout is non-blank
	
?> 
-->
<html>
  <head><title>Session Status</title>
    <meta HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE" />
		<meta http-equiv="refresh" content="45; url=/iglooportal/status.php?session=<?=$session?>&count=<?=$count?>&redirect=<?=$redirect?>">
		<LINK rel="stylesheet" type="text/css" href="styles/style.css" />
		<script type="text/javascript">
			var logout='<?=$logout?>';
			var user='<?=$user?>';
			var count='<?=$count?>';
			// Blank/Close at logout, or after two minutes of no login
			if(logout == 'true' || (count > 4 && user == "Authenticating...")) {
				document.location='about:blank'; window.close();
			}
		</script>		
  </head> 
  <body id="b">
    <div class="base title">Session Status</div>
    
		<div style="color:blue;font-size:9pt;">
                        NOTE: This status window must remain open
                        to validate access.  Usage is measured from
                        checkout time (11 am).
    </div>
<?php if($redirect != "") { ?>    
    <a href="<?=$redirect?>" target="_blank" title="<?=$redirect?>" target="_top">Visit original site</a>
    <br />
<?php } ?>    
    
    <div class="main">
      <br />

      <div class="base box">
        <div class="join">
          <b>User</b>
        </div>
        <div class="meter">
          <b>&nbsp;<?=$user?></b>
        </div>
      </div>
      
      <div class="base box">
        <div class="join">
          <b>Disabled?</b>
        </div>
        <div class="meter">
          <b>&nbsp;<?=$disabled?></b>
        </div>
      </div>
      
      <div class="base box">
        <div class="join">
          <b>Logged in?</b>
        </div>
        <div class="meter">
          <b>&nbsp;<?=$loggedin?></b>
        </div>
      </div>
      
      <div class="base box">
        <div class="join">
          <b>Expiry</b>
        </div>
        <div class="meter">
          <b>&nbsp;<?=$leave?><span style="font-size:9pt;">(<?=$hhmmss?> left)</span></b> 
        </div>
      </div>
      
      <div class="base box">
        <div class="join">
          <b>IP Address</b>
        </div>
        <div class="meter">
          <b>&nbsp;<?=$ipaddr?></b>
        </div>
      </div>
      
      <div class="base box">
        <div class="join">
          <b>Today's usage<b>
        </div>
        <div class="meter">
					<span style="width: <?=$pct?>%;white-space: nowrap;overflow:visible;" title="<?=$mbytes?> Mb">&nbsp;<?=$mbytes?> of <?=$mblimit?> Mb</span>
        </div>
      </div>
      
    </div>
    <br />

		<div clas="base box">
			<div class="join">
<?php if($loggedin == "Yes") { ?>    
				<a href="/index.php?logout=true&session=<?=$session?>&count=<?=$count?>&redirect=<?=$redirect?>" target="_top" title="Logout session">&nbsp;Logout&nbsp;</a>
				<br />
<?php } ?>    
			</div>
		</div>
	</body>
</html>