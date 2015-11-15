<?php
error_reporting(0);
session_start();
$email = $_SESSION['email'];
if($email){
$conn = mysql_connect("localhost","root","");
mysql_select_db("flickr");
$image_id = $_POST['image_id'];
$user_id = $_POST['user_id'];
$visibleto = $_POST['visibleto'];
$time = time();
$check = mysql_query("SELECT * FROM share WHERE image_id='$image_id' AND user_id='$user_id'");
$numrows = mysql_num_rows($check);
if($numrows == 0){
$query = mysql_query("
					INSERT INTO share VALUES('','$image_id','$user_id','$time','$visibleto')
					");
}
else{
$query1 = mysql_query("UPDATE share SET visibleto='$visibleto' WHERE image_id='$image_id' AND user_id='$user_id'");
$query2 = mysql_query("UPDATE share SET time='$time' WHERE image_id='$image_id' AND user_id='$user_id'");
}
$getsharenum = mysql_query("SELECT COUNT(user_id) AS share_cnt FROM share WHERE image_id='$image_id'");
$data1 = mysql_fetch_assoc($getsharenum);
$share_count = $data1['share_cnt'];
echo $share_count;
}
?>