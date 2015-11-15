<?php
	session_start();
	session_destroy();
	$page = '../login/';
	header('Location: '.$page);
?>