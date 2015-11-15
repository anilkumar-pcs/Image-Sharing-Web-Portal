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

$conn = mysql_connect("localhost","root","");
mysql_select_db("flickr");
$image_id = $_POST['id'];
$title = $_POST['title'];
$update = mysql_query("UPDATE images SET title='$title' WHERE image_id = '$image_id'");
}
?>