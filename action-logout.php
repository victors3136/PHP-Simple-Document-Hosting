<?php
	session_start();
	$_SESSION = array();
	session_destroy();
	header("Location: form-login.php");
	exit();
?>
