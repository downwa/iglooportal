<?php
// Allow access for the new session
// NOTE: IFRAME should load new location immediately.
// NOTE: IF that doesn't load within five seconds, BODY onload handler will try again.
// NOTE: IF that doesn't load within 30 seconds, grantAccess will have rescheduled itself to try again.
// NOTE: This should repeat until success, trying again every ten seconds.
// NOTE: ON SUCCESS, frameReady handler should redirect main page to status page.
// NOTE: THIS page is a replacement for the earlier simple Location header below, which would sometimes time out.
// NOTE: With this method, if a time out happens, the browser will retry indefinitely until it succeeds.
//header("Location: https://reserve.bristolinn.com:8443/index.php?session=$private_id&redirect=$redirect");
?>
<html>
	<head><title>Session Status</title>
		<LINK rel="stylesheet" type="text/css" href="styles/style.css" />
    <script type="text/javascript"><!-- 
			var accessGranted=false;
			function grantAccess() { // Retry the page
				try {
					if(!accessGranted) {
						var grantaccess=document.getElementById('grantaccess');
						grantaccess.src="https://reserve.bristolinn.com:8443/index.php?session=<?=$private_id?>&redirect=<?=$redirect?>";
						setTimeout("grantAccess();",30000); // If this doesn't load, try again
					}
				}
				catch(e) { alert('grantAccess error: '+e.message); }
			}
			function statusKeepAlive() {
				try {
					var grantaccess=document.getElementById('grantaccess');
					grantaccess.src="https://reserve.bristolinn.com:8444/status/index.php?session=<?=$private_id?>&count=9999&redirect=<?=$redirect?>";
					setTimeout("statusKeepAlive();",30000);
				}
				catch(e) { alert('statusKeepAlive error: '+e.message); }
			}
			function frameReady(frameObject) { // Grant access page loaded...
				try {
					//document.location="https://reserve.bristolinn.com:8444/status/index.php?session=<?=$private_id?>&count=0&redirect=<?=$redirect?>";
					if(!accessGranted) { accessGranted=true; setTimeout("statusKeepAlive();",30000); }
				}
				catch(e) { alert('frameReady error: '+e.message); }
			}
			// -->
		</script>
	</head>
	<body onload="setTimeout('grantAccess()',5000);">
		<iframe id="grantaccess" src="https://reserve.bristolinn.com:8443/index.php?session=<?=$private_id?>&redirect=<?=$redirect?>" onload="frameReady(this);" width="640" height="320" scrolling="no" frameBorder="0"><!-- style="display:none" -->
			<a href="https://reserve.bristolinn.com:8443/index.php?session=<?=$private_id?>&redirect=<?=$redirect?>">Click to access session</a>
		</iframe>
	</body>
</html>