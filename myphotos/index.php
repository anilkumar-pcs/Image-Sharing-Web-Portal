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
<title>Your Photos</title>
<link rel="stylesheet" href="style.css" />
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.0/jquery.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	$("h4").click(function() 
	{
		var titleid = $(this).attr("id");
		var sid=titleid.split("title"); // Splitting eg: title21 to 21
		var id=sid[1];
		$(this).hide();
		$("#formbox"+id).show();
		$(".content").focus();
		return false;
	});
	// Save Button
	$(".save").click(function() 
	{
		var A=$(this).parent().parent();
		var X=A.attr('id');
		var d=X.split("formbox"); // Splitting  Eg: formbox21 to 21
		var id=d[1];
		var Z=$("#"+X+" input.content").val();
		var dataString = 'id='+ id +'&title='+Z ;
		$.ajax({
			type: "POST",
			url: "change_title.php",
			data: dataString,
			cache: false,
			success: function(data)
			{
				A.hide(); 
				$("#title"+id).html(Z); 
				$("#title"+id).show(); 
			}	
		});
		return false;
	});
	// Cancel Button
	$(".cancel").click(function() 
	{
		var A=$(this).parent().parent();
		var X= A.attr("id");
		var d=X.split("formbox");
		var id=d[1];
		$("#title"+id).show();
		A.hide();
		return false;
	});
});
</script>
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
<div id="images_tot">
<h3>Photos of You</h3>
<table cellpadding="3" cellspacing="5" >
<?php
// integer starts at 0 before counting
    $i = 1;
	$query = mysql_query("SELECT * FROM images WHERE user_id = '$user_id' ORDER BY time DESC");
	$numrows = mysql_num_rows($query);
	if($numrows == 0){
		echo "You have no photos";
	}
	while($row = mysql_fetch_array($query)){
		$path = $row['path'];
		$img_id = $row['image_id'];
		$title = $row['title'];
		if($i === 1){
			echo '<tr><td>';
		}
		echo '
		<div id="formbox'.$img_id.'" style="display:none">
		<form method="post" name="form'.$img_id.'">
		<input type="text" value="'.$title.'" name="content" class="content"/><br />
		<input type="submit" value=" SAVE " class="save" />
		or
		<input type="button" value=" Cancel " class="cancel"/>
		</form>
		</div>
		';
		echo '<h4 id="title'.$img_id.'" style="font-weight:normal;margin:0px;margin-left:5px;">'.$title.'</h4>';
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
else{
	echo "You must be logged in!!!";
}
?>