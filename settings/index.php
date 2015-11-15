<html>
<head>
<title>HomePage</title>
<?php
error_reporting(0);
session_start();
$email = $_SESSION['email'];
if($email){
$conn = mysql_connect("localhost","root","");
mysql_select_db("flickr");
$getuserid = mysql_query("SELECT user_id,name,profilepic FROM users WHERE email = '$email'");
while($row = mysql_fetch_array($getuserid))
{
	$user_id = $row['user_id'];
	$username = $row['name'];
	$profilepic = $row['profilepic'];
}

$tab = $_GET['tab'];
?>
<link rel="stylesheet" href="../home/style.css" />
<script type="text/javascript" src="../home/jQuery.js"></script>
</head>
<body>
<div id="header">
<div id="logo"></div>
<div id="search">
<input type="text" placeholder="Search" class="searchbox" />
<div id="search_opt" style="display:none;" class="services">
<ul id="menu">
	<li class="photos selected" id="1"><a href='#' >Search Photos</a></li>
	<li class="users" id="2"><a href='#' >Search Users</a></li>
</ul>
</div>
</div>
<span id='name'><a href="../home/" style="color:#9aa9c8;font-weight:bold;text-decoration:none;"><?php echo $username;?></a></span>
<span id='home'><a href="../home/" style="color:#9aa9c8;font-weight:bold;text-decoration:none;">Home</a></span>
<span id='settings'><a href="../settings/" style="color:#9aa9c8;font-weight:bold;text-decoration:none;">Settings</a></span>
<span id='logout'><a href="../logout/" style="color:#9aa9c8;font-weight:bold;text-decoration:none;">Logout</a></span>
</div>
<div id="content">
<div id="sidebar">
	<div id="profileimage"><img src="<?php echo $profilepic;?>" class="imageclass">
	<form name="editpic_form" action="changepic.php" method="POST" enctype="multipart/form-data">
	<span id="editpic" style="position:absolute;z-index:300;top:85px;left:5px;font-size:12px;display:none">
	<a href="#" >Change Picture</a>
	<input type="file" name="edit_profpic" id="edit_profpic" onchange="readURL(this);">
	</span>
	</form>
	</div>
	<div id="actions">
		<ul>
		<li><a <?php if($tab == ''){echo "class=selected";}?> href="../settings/">General</a></li>
		<li><a <?php if($tab == 'security'){echo "class=selected";}?> href="../settings/?tab=security">Password</a></li>
		<li><a <?php if($tab == 'likes'){echo "class=selected";}?> href="../settings/?tab=likes">My Likes</a></li>
		<li><a <?php if($tab == 'shares'){echo "class=selected";}?> href="../settings/?tab=shares">My Shares</a></li>
		</ul>
	</div>
</div>
<div id="main">
<?php
	if($tab == ''){
		require_once( '../home/editprofile.php' );
	}
	if($tab == 'security'){
		require_once( '../settings/changepass.php' );
	}
	if($tab == 'likes'){
		require_once( '../settings/mylikes.php' );
	}
	if($tab == 'shares'){
		require_once( '../settings/myshares.php' );
	}
?>
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