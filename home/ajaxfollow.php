<?php
error_reporting(0);
session_start();
$email = $_SESSION['email'];
if($email){
$conn = mysql_connect("localhost","root","");
mysql_select_db("flickr");
$getuserid = mysql_query("SELECT user_id,name FROM users WHERE email = '$email'");
while($row = mysql_fetch_array($getuserid))
{
	$user_id = $row['user_id'];
}
$type = $_GET['action'];
$conn = mysql_connect("localhost","root","");
mysql_select_db("flickr");
if($type == remove){
	$id = $_POST['id'];
	$query = mysql_query("DELETE from follow WHERE user_id = '$user_id' AND follow_id = '$id'");
}
if($type == follow){
	$id = $_POST['id'];
	$query = mysql_query("INSERT into follow VALUES('$user_id','$id')");
}
}
?>