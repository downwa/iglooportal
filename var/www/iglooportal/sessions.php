<?php

// NOTE: use loadSession() at start of page and saveSession() at end
// beware of changing the global context!
function loadSession() {
    global $private_id,$a,$b;
    // Get the private context
    session_name('Private');
    if (!isset($_SESSION)) { session_start(); }
    $private_id = session_id();
    if(isset($_SESSION['pr_key'])) $b = $_SESSION['pr_key'];
    else $b="";
    session_write_close();
   
    // Get the global context
    session_name('Global');
    session_id('TEST');
    session_start();
   
    if(isset($_SESSION['key'])) $a = $_SESSION['key'];
    else $a="";
    session_write_close();
}

function saveSession() {
    global $private_id,$a,$b;

    // Store it back
    session_name('Private');
    session_id($private_id);
    if (!isset($_SESSION)) { session_start(); }
    $_SESSION['pr_key'] = $b;
    session_write_close();

    session_name('Global');
    session_id('TEST');
    session_start();
    $_SESSION['key']=$a;
    session_write_close();
}

function input($key) {
  $value="";
  if(isset($_GET[$key]))       { $value=$_GET[$key];  }
  else if(isset($_POST[$key])) { $value=$_POST[$key]; }
  return $value;
}

function setGlobalIni() {
	$ini_array = @parse_ini_file("/etc/innportal.ini"); // Avoid # deprecated warning
	foreach($ini_array as $key => $val) { $GLOBALS["innportal_".$key]=$val; }
  //print_r($GLOBALS);
}

?>
