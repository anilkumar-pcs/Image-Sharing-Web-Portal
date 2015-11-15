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

$profile_id = $_GET['id'];
$getname = mysql_query("SELECT * FROM users WHERE user_id = '$profile_id'");
while($q = mysql_fetch_array($getname)){
	$profile_image = $q['profilepic'];
	$profile_name = $q['name'];
	$email = $q['email'];
	$age = $q['age'];
	$gender = $q['gender'];
}
?>
<html>
<head>
<title><?php echo $profile_name;?></title>
<link rel="stylesheet" href="style.css">
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
	<div id="profileimage"><img src="<?php echo $profile_image;?>" class="imageclass"></div>
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
<div id="images">
<h3>Photos of <?php echo $profile_name;?></h3>
<table cellpadding="3" cellspacing="0" id="images_tot">
<?php
// integer starts at 0 before counting
    $i = 1;
	$query = mysql_query("SELECT * FROM images WHERE user_id = '$profile_id' ORDER BY time DESC");
	$numrows = mysql_num_rows($query);
	echo '<tr>';
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
</div>
</div>
</body>
</html>
<?php
}
?>