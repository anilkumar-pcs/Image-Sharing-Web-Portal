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
if($type == unlike){
	$image_id = $_POST['image_id'];
	$query = mysql_query("DELETE from likes WHERE user_id = '$user_id' AND image_id = '$image_id'");
}
if($type == like){
	$image_id = $_POST['image_id'];
	$query = mysql_query("INSERT into likes VALUES('$image_id','$user_id')");
}
$getlikenum = mysql_query("SELECT COUNT(user_id) AS likes_cnt FROM likes WHERE image_id='$image_id'");
$data = mysql_fetch_assoc($getlikenum);
$like_count = $data['likes_cnt'];
echo $like_count;
}
?>