<html>
  <head><title><?=$site_name?> Internet portal</title>
    <META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE" />
    <LINK rel="stylesheet" type="text/css" href="iglooportal/styles/public.css" />
		<script src="iglooportal/scripts/capsLock.js" type="text/javascript">//</script>
		<script src="iglooportal/scripts/jquery-1.9.1.min.js" type="text/javascript">//</script>
		<script type="text/javascript"><!--
			var passChecked=false;
			function doRedirect() {
				//alert(passChecked);
				if(passChecked) {
					window.open('https://reserve.bristolinn.com/iglooportal/redirect.php?redirect=<?=$redirect?>','_blank'); // New Tab/Window
				}
				return true;
			}
			function checkPass() {
				try {
					var userField=document.getElementById('user');
					var passField=document.getElementById('pass');
					var user=userField.value;
					var pass=passField.value;
					if(user == "" || pass == "") { return; } // Don't check until both are non-blank
					var url="https://reserve.bristolinn.com:443/?doauth=1&user="+user+"&pass="+pass+"&submit=true";
					if (typeof $ !== 'undefined') {
						passChecked=($.ajax({type: "GET", url: url, async: false}).responseText) == 1;
					}
					else { passChecked=true; }
				}
				catch(e) { alert('checkPass: '+e.message); }
			}
			//-->
		</script>
  </head>
  
  <body onload="document.getElementById('user').focus();">
  <!-- NOTE: DO NOT REMOVE THIS NOTICE (used by redirect.php): IGLOOPORTAL LOGIN PAGE -->
  <center>
    <form name='login' method='post'>
      <div class="warning" id="capsWarning" style="display: none">
        Warning: Caps Lock is enabled
      </div>
        <table id="login" border=0 style="width:800px">
          <tr>
            <td rowspan="4" id="logotd">
              <img src="iglooportal/images/hotelsunset.jpg" alt="<?=$site_name?> Logo" id="logo" />
            </td>
            <td colspan="2">
                <center><h2 style="color: #333"><?=$site_name?><br />Internet portal</h2></center>
                <span style="color:red;font-size:8pt;">
                        Internet usage is monitored
                        and limited to <?=$mblimit?> Mb/day.
                        Illegal activities, pornography, etc.
                        may cause account suspension.
                        <b>(Less than 10 minutes of YouTube will use
                        it up for the day).</b>
                </span>
                <span style="color:blue;font-size:8pt;">
                        NOTE: The status window must remain open
                        to validate access.  Click <a href="https://reserve.bristolinn.com/index.php?session=<?=$private_id?>&count=0" target="_blank">here</a>
                        to display the status in a new tab
                        or window.
                </span>
                <br />
            <!-- div class="warning" id="capsWarning" style="display: none">
                Warning: Caps Lock is enabled
            </div-->
<?php       if(strlen($reason) > 0) { ?>
              <div id='reason' class="warning"><?=$reason?></div>
<?php       } ?>
            </tr>
            <tr>
              <td class="labeltd">Username</td>
              <td><input class='inputTextLogin' type='text' name='user' id='user' value="<?=$user?>" onblur="checkPass();" /></td>
            </tr>
            <tr>
              <td class="labeltd">Password</td>
              <td><input class='inputTextLogin' type='password' name='pass' id='pass' value="<?=$pass?>" onblur="checkPass();" />
								(Not case sensitive)
              </td>
            </tr>
            <tr>
              <td></td>
              <td><input class='inputButton' type='submit' name="submit" id='loginButton' value="Enter" title="Login and redirect to original site" onclick="doRedirect();"/></td>
            </tr>
          </table>
          <input type="hidden" name="redirect" value="<?=$redirect?>" />
        </form>
      </div>
    </center>
  </body>
</html>