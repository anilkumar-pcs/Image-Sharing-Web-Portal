<?php
error_reporting(0);
session_start();
$email = $_SESSION['email'];
if($email){
$conn = mysql_connect("localhost","root","");
mysql_select_db("flickr");
$getuserid = mysql_query("SELECT * FROM users WHERE email = '$email'");
while($row = mysql_fetch_array($getuserid))
{
	$user_id = $row['user_id'];
	$name = $row['name'];
	$profilepic = $row['profilepic'];
}
?>
<html>
<head>
<title>People You are Following</title>
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
	<div id="profileimage"><img src="<?php echo $profilepic;?>" class="imageclass"></div>
	<div id="actions">
		<ul>
		<li><a href="../home">Profile</a></li>
		<li><a href="../myphotos">Photos</a></li>
		<li><a href="../followers">Followers</a></li>
		<li><a href="../following">Following</a></li>
		<li><a href="#">Upload Photo</a></li>
		</ul>
	</div>
</div>
<div id="main">
<?php
//fetching data of users from table to display
$query = mysql_query("SELECT * from users");
while($d=mysql_fetch_array($query)){
	$all_users[] = $d['user_id'];
}
?>
<div class="box">
<div class="userdata">
	<div class="followbox">
	<table style='margin-top:10px'>
	<?php

	//checking whether current user follows them or not
	$follow_check="select * from follow WHERE user_id='$user_id' ";
	$user_sql=mysql_query($follow_check);
	$count=mysql_num_rows($user_sql);
	if($count > 0){
	echo "You are Following : ".$count."<br><br>";
	while($row = mysql_fetch_array($user_sql)){
		$id = $row['follow_id'];
		$cmd=mysql_query("SELECT * from users WHERE user_id = '$id'");
		while($data1=mysql_fetch_array($cmd))
		{
			$name_f = $data1['name'];
			$profpic_f = $data1['profilepic'];
		}
		echo "
		<tr class='record'>
			<td width='100'><center><img src='".$profpic_f."' class='imageclass' style='width:50px;height:50px'></center></td>
			<td width='376' class='content'>
			<strong ><a href='../others/profile.php?id=".$id."' style='color:#006699;'>View Profile</a>/</strong>".$name_f." <br />
			<div id='follow".$id."' style='display:none'><a href='#' class='follow' id='".$id."'><span class='follow_b'> Follow </span></a></div>
			<div id='remove".$id."'><span class='youfollowing_b'> You Following </span>
			<a href='' class='remove' id='".$id."'><span class='remove_b'> remove </span></a></div></td>
		</tr>
		";
	}
	}
	else{
		echo "You are not following any one<br><br><b>People You may Know</b><br>";
		$query1 = mysql_query("SELECT * FROM users WHERE user_id != '$user_id'");
		while($data2 = mysql_fetch_array($query1)){
			$id_f = $data2['user_id'];
			$name_f = $data2['name'];
			$profpic_f = $data2['profilepic'];
		
		echo "
		<tr class='record'>
			<td width='100'><center><img src='".$profpic_f."' class='imageclass' style='width:50px;height:50px'></center></td>
			<td width='376' class='content'>
			<strong ><a href='../others/profile.php?id=".$id_f."' style='color:#006699;'>View Profile</a>/</strong>".$name_f." <br />
			<div id='follow".$id_f."'><a href='#' class='follow' id='".$id_f."'><span class='follow_b'> Follow </span></a></div>
			<div id='remove".$id_f."' style='display:none'><span class='youfollowing_b'> You Following </span>
			<a href='' class='remove' id='".$id_f."'><span class='remove_b'> remove </span></a></div></td>
		</tr>
		";
		}
	}
	?>
	</table>
	</div>
</div>
</div>
</div>
</div>
</body>
</html>
<?php
}
else{
	echo "You must be logged in!!!";
}
?>