<?php

	session_start();


	if(empty($_SESSION['dono'])) {
		$_SESSION['dono'] = md5($_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']);
	}

	$token =  md5($_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']);
	
	if($_SESSION['dono'] != $token) {
		exit;
	}

	require_once "../vendor/autoload.php";

	$route = new \App\Route;
	
?>