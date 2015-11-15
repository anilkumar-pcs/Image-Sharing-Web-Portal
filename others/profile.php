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
$getfollowing = mysql_query("SELECT follow_id FROM follow WHERE user_id = '$user_id'");
while($a = mysql_fetch_array($getfollowing)){
	$following[] = $a['follow_id'];
}
$profile_id = $_GET['id'];
$getname = mysql_query("SELECT * FROM users WHERE user_id = '$profile_id'");
while($q = mysql_fetch_array($getname)){
	$profile_image = $q['profilepic'];
	$profile_name = $q['name'];
	$email = $q['email'];
	$age = $q['age'];
	$gender = $q['gender'];
	$place = $q['place'];
	$member = $q['member'];
}
?>
<html>
<head>
<title><?php echo $profile_name;?></title>
<link rel="stylesheet" href="style.css">
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.0/jquery.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	$(".remove").click(function(){
		var element = $(this);
		var I = element.attr("id");
		var info = 'id=' + I;
		
	$.ajax({
	   type: "POST",
	   url: "ajaxfollow.php?action=remove",
	   data: info,
	   success: function(){}
	 });
	 
		$("#remove"+I).hide();
		$("#follow"+I).show();
	return false;
	});
	
	$(".follow").click(function(){
		var element = $(this);
		var I = element.attr("id");
		var info = 'id=' + I;
		
	$.ajax({
	   type: "POST",
	   url: "ajaxfollow.php?action=follow",
	   data: info,
	   success: function(){}
	 });
	 
		$("#follow"+I).hide();
		$("#remove"+I).show();
	return false;
	});
});
</script>
</head>
<body>
<div id="header">
<div id="logo"></div>
<div id="search">
<input type="text" placeholder="Search for Photos" class="searchbox" />
</div>
<span><a href="../home/" style="color:#9aa9c8;font-weight:bold;text-decoration:none;position: relative;left: 120px;top: -14px;"><?php echo $name;?></a></span>
<span><a href="../home/" style="color:#9aa9c8;font-weight:bold;text-decoration:none;position: relative;left: 700px;top: -14px;">Home</a></span>
<span><a href="../settings/" style="color:#9aa9c8;font-weight:bold;text-decoration:none;position: relative;left: 710px;top: -14px;">Settings</a></span>
<span><a href="../logout/" style="color:#9aa9c8;font-weight:bold;text-decoration:none;position: relative;left: 720px;top: -14px;">Logout</a></span>
</div>
<div id="content">
<div id="sidebar">
	<div id="profileimage"><img src="<?php echo $profile_image; ?>" class="imageclass"></div>
	<div id="actions">
		<ul>
		<li><a href="profile.php?id=<?php echo $profile_id;?>">Profile</a></li>
		<li><a href="photos.php?id=<?php echo $profile_id;?>">Photos</a></li>
		<li><a href="followers.php?id=<?php echo $profile_id;?>">Followers</a></li>
		<li><a href="following.php?id=<?php echo $profile_id;?>">Following</a></li>
		</ul>
	</div>
</div>
<div id="main">
<div id="relation" style="position: relative;top: 110px;padding-left: 100px;">
<?php
if($profile_id != $user_id){
	if(in_array($profile_id,$following)){
		echo "
		<div id='follow".$profile_id."' style='display:none'>You are not following him.<a href='#' class='follow' id='".$profile_id."'><span class='follow_b'>Follow</span></a></div>
		<div id='remove".$profile_id."'><span class='youfollowing_b'> You Following </span>
		<a href='' class='remove' id='".$profile_id."'><span class='remove_b'>remove</span></a></div>
		";
	}
	else{
		echo "
		<div id='follow".$profile_id."'>You are not following him.<a href='#' class='follow' id='".$profile_id."'><span class='follow_b'>Follow</span></a></div>
		<div id='remove".$profile_id."' style='display:none'><span class='youfollowing_b'> You Following </span>
		<a href='' class='remove' id='".$profile_id."'><span class='remove_b'>remove</span></a></div>
		";
	}
}
?>
</div>
<div id="profile_details">
<h3>About <?php echo $profile_name;?></h3>
<table style="border-spacing:10px">
	<tr>
	<td>Full Name : </td><td><?php echo $profile_name;?></td>
	</tr>
	<tr>
	<td>E-mail : </td><td><?php echo $email;?></td>
	</tr>
	<tr>
	<td>Gender : </td><td><?php echo $gender;?></td>
	</tr>
	<tr>
	<td>Age : </td><td><?php echo $age;?></td>
	</tr>
	<tr>
	<td>Place : </td><td><?php echo $place;?></td>
	</tr>
	<tr>
	<td>Member Since : </td><td><?php echo $member;?></td>
	</tr>
</table>
</div>
</div>
</div>
</body>
</html>
<?php
}
?>