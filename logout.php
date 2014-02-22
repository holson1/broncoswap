<?php
	session_start();

	//destroy the session variables
	unset($_SESSION['uid']);
	unset($_SESSION['email']);
	unset($_SESSION['fname']);

	//redirect to the home page
	header("Location: index.php");
?>
