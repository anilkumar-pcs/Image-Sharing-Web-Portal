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
	$name = $row['name'];
}
?>
<html>
<head>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.0/jquery.min.js"></script>
</head>
<body>
<div id="profile_details" style="position: relative;top: 150px;padding-left: 50px;">
<h3>Photos Liked by You</h3>
<table cellpadding="3" cellspacing="5" >
<?php
// integer starts at 0 before counting
    $i = 1;
	$query = mysql_query("SELECT * FROM images WHERE image_id IN (SELECT image_id FROM likes WHERE user_id='$user_id')");
	while($row = mysql_fetch_array($query)){
		$path = $row['path'];
		$img_id = $row['image_id'];
		$title = $row['title'];
		if($i === 1){
			echo '<tr><td>';
		}
		if($i <= 2){
			echo '<a href="../image.php?id='.$img_id.'"><img src="'.$path.'" style="width:300px;height:220px" alt="'.$title.'" title="'.$title.'" /></a>';
			echo '</td><td>';
			$i++;
		}
		else{
			echo '<a href="../image.php?id='.$img_id.'"><img src="'.$path.'" style="width:300px;height:220px" alt="'.$title.'" title="'.$title.'" /></a>';
			echo '</td></tr>';
			$i = 1;
		}
	}
	//echo "<br>Total Images : ".$numrows;
?>
</table>
</div>
</body>
</html>
<?php
}
?>