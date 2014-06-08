<?php
  global $site_name,$redirect,$private_id;

  $site_name="Bristol Inn";

  ini_set('display_errors',1); 
  error_reporting(E_ALL); //error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

  include "sessions.php";
  header("Connection: close");

  /** HANDLE INPUTS **/
  $redirect=input("redirect");
?>
<html>
  <head><title>Welcome to <?=$site_name?></title>
    <META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">
    <!-- NOTE: not using meta refresh since browser seems to keep alive connection to router instead of real target -->
    <!-- meta http-equiv="refresh" content="5; url=<?=$redirect?>" -->
    <LINK rel="stylesheet" type="text/css" href="iglooportal/styles/public.css" />
    <script type="text/javascript"><!-- 
			var redirect='<?=$redirect?>';
			var loggedIn=false;
			var frame2Ready=false;
			var tries=0;
			var startTime=new Date();
			function testLogin() { // FIRST try accessing an internal page
				try {
					startTime=new Date();
					var testlogin=document.getElementById('testlogin');
					testlogin.src='https://reserve.bristolinn.com:447/iqreservations/asp/IQHome.asp';
					//window.open('http://192.168.42.1:8080/status/');
				}
				catch(e) { alert('testLogin error: '+e.message); }
			}
			function frameReady(frameObject) { // Internal page or login page loaded?
				try {
					updateDisplay();
					var ih='';
					try { ih=frameObject.contentDocument.body.innerHTML; } catch(e) { ih="ACCESS DENIED"; }
					//alert('ih='+ih);
					if(ih.indexOf('IGLOOPORTAL LOGIN PAGE') > -1) { setTimeout("testLogin()",3000); return; } // Try redirecting again...
					loggedIn=true;
					frameSetup2();
				}
				catch(e) { alert('frameReady error: '+e.message); }
			}
			function frameSetup2() { // If External page IFRAME loaded or timeout, redirect to external page
				try {
					updateDisplay();
					var testlogin=document.getElementById('testlogin2');
					testlogin.src=redirect;
					//alert('frame2Ready='+frame2Ready+',tries='+tries);
					if(true || frame2Ready || tries>1) { document.location=redirect; } // Redirect to external page
					tries++;
					setTimeout("frameSetup2()",5000);
				}
				catch(e) { alert('frameSetup2 error: '+e.message); }
			}
			function frameReady2(frameObject) { // External page loaded in IFRAME (unsupported on some browsers due to security)
					//alert('frame2Ready: loggedIn='+loggedIn);
				if(!loggedIn) { return; }
				updateDisplay();
				frame2Ready=true;
				try { document.location=redirect; } // Redirect to external page
				catch(e) { alert('frameReady2 error: '+e.message); }
			}
			function updateDisplay() {
				try {
					var endTime=new Date();
					var timeDiff=Math.round((endTime-startTime) / 1000);
					var remainingTime=15-timeDiff;
					//alert('remainingTime='+remainingTime+',timeDiff='+timeDiff+',endTime='+endTime+',startTime='+startTime);
					if(remainingTime<0) { remainingTime=0; }
					var sec=document.getElementById('sec');
					sec.innerHTML=remainingTime;
				}
				catch(e) { alert('updateDisplay error: '+e.message); }
			}
			//-->
    </script>
  </head>

  <body>
    <center>
			<br />
      <h2>Redirecting to original location</h2>
      If browser does not redirect after <span id="sec">15</span> seconds, click:<br /><br />
      &nbsp;&nbsp;<a href="<?=$redirect?>" target="_blank"><?=$redirect?></a>
    </center>
    <iframe id="testlogin" src="https://reserve.bristolinn.com:447/iqreservations/asp/IQHome.asp" onload="frameReady(this);" width="0" height="0" style="display:none">
    </iframe>
    <iframe id="testlogin2" onload="frameReady2(this);" width="0" height="0" style="display:none">
			<a href="<?=$redirect?>">Click to proceed to original destination</a>
    </iframe>
  </body>
</html>