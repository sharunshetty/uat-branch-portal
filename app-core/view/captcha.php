<?php

/**
 * @copyright   : (c) 2020 Copyright by LCode Technologies
 * @author      : Shivananda Shenoy (Madhukar)
 **/

/** Application Core */
require_once(dirname(__FILE__) . '/../app_auto_load.php');

/** Captcha Image */
require_once( DIRPATH . '/class/class_mod_captcha.php'); // Text & Captcha


// IF Invalid Request
if( !isset($_GET['data']) || !isset($_SESSION['LOGIN_TOKEN']) || $main_app->strsafe_input($_GET['data']) !== $_SESSION['LOGIN_TOKEN'] ) {
	exit('');
}

$textObj = new TextClass();
$textObj->phpcaptcha('#7C0A02','#fff',120,40,0,0);

/*
<img src="captcha.php?rand=<?php echo rand();?>" id='captcha-img'>
Can't read the image? click <a href='javascript: refreshCaptcha();'>here</a> to refresh.

<script type='text/javascript'>
function refreshCaptcha(){
	var img = document.images['captcha-img'];
	img.src = img.src.substring(0,img.src.lastIndexOf("?"))+"?rand="+Math.random()*1000;
}
</script>
*/
