<?php
error_reporting(0);
session_start();
$conn = mysql_connect("localhost","root","");
mysql_select_db("flickr");
$email = $_SESSION['email'];
$pass = $_GET['pass'];
mysql_query("UPDATE users SET password='$pass' WHERE email='$email'");
?>