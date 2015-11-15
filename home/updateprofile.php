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
if(!empty($_POST['name'])){
	$newname = $_POST['name'];
	$update = mysql_query("UPDATE users SET name = '$newname' WHERE user_id = '$user_id'");
}
if(!empty($_POST['mail'])){
	$newmail = $_POST['mail'];
	$update = mysql_query("UPDATE users SET email = '$newmail' WHERE user_id = '$user_id'");
}
if(!empty($_POST['gen'])){
	$newgen = $_POST['gen'];
	$update = mysql_query("UPDATE users SET gender = '$newgen' WHERE user_id = '$user_id'");
}
if(!empty($_POST['age'])){
	$newage = $_POST['age'];
	$update = mysql_query("UPDATE users SET age = '$newage' WHERE user_id = '$user_id'");
}
if(!empty($_POST['place'])){
	$newplace = $_POST['place'];
	$update = mysql_query("UPDATE users SET place = '$newplace' WHERE user_id = '$user_id'");
}
}
?>