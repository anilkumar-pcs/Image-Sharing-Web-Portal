<html>
<?php
error_reporting(0);
session_start();
$email = $_SESSION['email'];

function facebook_style_date_time($timestamp){
$difference = time() - $timestamp;
$periods = array("sec", "min", "hour", "day", "week", "month", "year", "decade");
$lengths = array("60","60","24","7","4.35","12","10");

if ($difference > 0) { // this was in the past time
$ending = "ago";
} else { // this was in the future time
$difference = -$difference;
$ending = "to go";
}
for($j = 0; $difference >= $lengths[$j]; $j++) $difference /= $lengths[$j];
$difference = round($difference);
if($difference != 1) $periods[$j].= "s";
$text = "$difference $periods[$j] $ending";
return $text;
}

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
?>
<head>
<link rel="stylesheet" href="home/style.css" />
<script type="text/javascript" src="home/jQuery.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	$(".searchbox").focus(function(){
		$("#search_opt").show();
		var key = $(".searchbox").val();
		$(document).keydown(function(e){
			if (e.keyCode == 38) { //up 
				var selected = $(".selected");
				$(".services li").removeClass("selected");
				if (selected.prev().length == 0) {
					selected.siblings().last().addClass("selected");
				} else {
					selected.prev().addClass("selected");
				}
			}
			if (e.keyCode == 40) { //down 
			    var selected = $(".selected");
				$(".services li").removeClass("selected");
				if (selected.next().length == 0) {
					selected.siblings().first().addClass("selected");
				} else {
					selected.next().addClass("selected");
				}
			}
			 if (e.keyCode == 13) { // enter
				if ($(".services").is(":visible")) {
					search();
				} else {
					$(".services").show();
				}
				menuOpen = !menuOpen;
			}
		});
	});
	$(".searchbox").focusout(function(){
		$("#search_opt").hide();
	});
	//Unlike...
	$(".unlike").click(function(){
		var element = $(this);
		var I = element.attr("id");
		var info = 'image_id=' + I;
	
	$("#loading"+I).show();
	
	$.ajax({
	   type: "POST",
	   url: "home/likeunlike.php?action=unlike",
	   data: info,
	   success: function(data){
			data = "("+data+")";
			//alert(data);
			var likeid = '#like_count'+I;
			$("#loading"+I).hide();
			$(likeid).html(data);
	   }
	 });
	 
		$("#unlike"+I).hide();
		$("#like"+I).show();
	return false;
	});
	//Like...
	$(".like").click(function(){
		var element = $(this);
		var I = element.attr("id");
		var info = 'image_id=' + I;
		
	$("#loading"+I).show();	
		
	$.ajax({
	   type: "POST",
	   url: "home/likeunlike.php?action=like",
	   data: info,
	   success: function(data){
			data = "("+data+")";
			//alert(data);
			var likeid = '#like_count'+I;
			$("#loading"+I).hide();
			$(likeid).html(data);
	   }
	 });
	 
		$("#like"+I).hide();
		$("#unlike"+I).show();
	return false;
	});
	
	//Sharing...
	$(".share").click(function(){
		var element = $(this);
		var I = element.attr("id");
		$("#sharebox"+I).show();
	});
	$(".sh_confirm").click(function(){
		var element = $(this);
		var I = element.attr("id");
		var imginfo = 'image_id=' + I;
		//var title = 'title='+$("#sh_title").val();
		//alert(title);
		var userinfo = 'user_id='+<?php echo $user_id;?>;
		var visibleto = 'visibleto='+$("#visibility"+I).val();
		var info = imginfo+'&'+userinfo+'&'+visibleto;
		$.ajax({
		   type: "POST",
		   url: "home/share.php",
		   data: info,
		   success: function(data){
				data = "("+data+")";
				var shareid = '#share_count'+I;
				$(shareid).html(data);
				$("#sharebox"+I).html("<center>This Image has been successfully shared...</center>");
				$("#sharebox"+I).fadeOut(1000);
		   }
		 });
	});
	$(".sh_cancel").click(function(){
		var element = $(this);
		var I = element.attr("id");
		$("#sharebox"+I).hide();
	});
});
function search() {
	var keyword = $(".searchbox").val();
    var stype = $(".selected").attr("id");
	if(keyword != ""){
		window.location.assign("search/index.php?stype="+stype+"&q="+keyword);
		$(".services").hide();
	}
}
</script>
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
<span id='name'><a href="home/" style="color:#9aa9c8;font-weight:bold;text-decoration:none;"><?php echo $username;?></a></span>
<span id='home'><a href="home/" style="color:#9aa9c8;font-weight:bold;text-decoration:none;">Home</a></span>
<span id='settings'><a href="settings/" style="color:#9aa9c8;font-weight:bold;text-decoration:none;">Settings</a></span>
<span id='logout'><a href="logout/" style="color:#9aa9c8;font-weight:bold;text-decoration:none;">Logout</a></span>
</div>	
<?php
	$image_id = $_GET['id'];
	$prev_id = $image_id - 1;
	$next_id = $image_id + 1;
	$getlikes = mysql_query("SELECT image_id FROM likes WHERE user_id = '$user_id'");
	while($a = mysql_fetch_array($getlikes)){
		$likes[] = $a['image_id'];
	}
	$getimage = mysql_query("SELECT * FROM images WHERE image_id = '$image_id'");
	while($row1 = mysql_fetch_array($getimage)){
		$other_userid = $row1['user_id'];
		$image = $row1['path'];
		$title = $row1['title'];
		$time = $row1['time'];
		$visible = $row1['visible'];
		if($visible == 1)
			$share_str = "Public";
		else if($visible == 2)
			$share_str = "Followers";
		else
			$share_str = "Only Me";
		//get uploader details
		$newname = mysql_query("SELECT name,profilepic FROM users WHERE user_id = '$other_userid'");
		while($x = mysql_fetch_array($newname)){
			$other_username = $x['name'];
			$other_profilepic = $x['profilepic'];
		}
		//get num of likes
		$getlikenum = mysql_query("SELECT COUNT(user_id) AS likes_cnt FROM likes WHERE image_id='$image_id'");
		$data = mysql_fetch_assoc($getlikenum);
		$like_count = $data['likes_cnt'];
		//get num of shares
		$getsharenum = mysql_query("SELECT COUNT(user_id) AS share_cnt FROM share WHERE image_id='$image_id'");
		$data1 = mysql_fetch_assoc($getsharenum);
		$share_count = $data1['share_cnt'];
		//modify the path of image
		$new_img = explode("/",$image);
		$new_profile_pic = explode("/",$other_profilepic);
		echo "
		<div id='maincontent' style='position:absolute;top:100px;left:110px'>
		<h3 style='font-weight:normal'>".$title."</h3>
		<div id='links' style='padding-bottom:20px;font-size:13px;'>
			<span><b>Uploader</b> : ".$other_username."</span><br>
			<span><a href='home/'>Return to Home</a></span>
			<span><a href='others/photos.php?id=".$other_userid."'>".$other_username."'s Photos</a></span>
			<span><a href='others/profile.php?id=".$other_userid."'>".$other_username."'s Profile</a></span>
			<span style='float:right'>
			<span><a href='image.php?id=".$prev_id."'>Previous</a></span>
			<span><a href='image.php?id=".$next_id."'>Next</a></span>
			</span>
		</div>
		<div id='image'>
			<img src='".$new_img[1]."/".$new_img[2]."/".$new_img[3]."' style='width:800px;height:570px' >
		</div>
		<div id='uploader_details'>
		<div style='padding:15px;'><img src='".$new_profile_pic[1]."/".$new_profile_pic[2]."' style='width:50px;height:50px' class='imageclass'></div>
		<div style='position: relative;top: -35;left: 80;'><a href='others/profile.php?id=".$other_userid."'>".$other_username."</a></div>
		</div>
		<div id='sideone'>
		";
		if(in_array($image_id,$likes)){
			echo "
			<div id='like".$image_id."' style='display:none'><a href='#' class='like' id='".$image_id."'><span class='follow_b'> Like </span></a></div>
			<div id='unlike".$image_id."'><a href='' class='unlike' id='".$image_id."'><span class='remove_b'> Unlike </span></a></div>
			<span id='like_count".$image_id."' style='position:relative;top:-18px;left:52px'>(".$like_count.")
			<div id='loading".$image_id."' style='display:none'><img src='home/loading.gif'></div>
			</span>
			";
		}
		else{
			echo"
			<div id='like".$image_id."'><a href='#' class='like' id='".$image_id."'><span class='follow_b'> Like </span></a></div>
			<div id='unlike".$image_id."' style='display:none'><a href='' class='unlike' id='".$image_id."'><span class='remove_b'> Unlike </span></a></div>
			<span id='like_count".$image_id."' style='position:relative;top:-18px;left:52px'>(".$like_count.")
			<div id='loading".$image_id."' style='display:none'><img src='home/loading.gif'></div>
			</span>
			";
		}
		echo "
		<div id='share".$image_id."' style='position: relative;top: -36px;left: 100px;'>
		<span class='share' id='".$image_id."'><span class='share_b'>Share</span></span>
		<span id='share_count".$image_id."'>(".$share_count.")</span>
		<div id='sharebox".$image_id."' class='share_box' style='display:none'>
		<input type='text' placeholder='Say Something...' autocomplete='off' class='title' id='sh_title".$image_id."'/>
		<div id='thumbnail' style='padding:10px'>
		<img src='".$new_img[1]."/".$new_img[2]."/".$new_img[3]."' class='imageclass' style='width:120px;height:85px'>
		<div id='title_sh' style='position: absolute;top: 60px;left: 160px;font-size: 15px;'>Title: ".$title."</div></div>
		<div style='position: relative;' style='padding-left:10px'>
		<button id='".$image_id."' class='sh_confirm'>Share</button>
		<button id='".$image_id."' class='sh_cancel'>Cancel</button>
		<span style='float:right;font-size:12px;font-weight:bold'>
		Visisble to : <select id='visibility".$image_id."' name='visibleto".$image_id."'>
		<option value='1'>Public</option>
		<option value='2'>Followers</option>
		<option value='3'>Only Me</option>
		</select></span>
		</div>
		</div>
		</div>
		<div style='position:relative;top:-30'>Uploaded : ".facebook_style_date_time($time)."</div>
		<div style='position:relative;top:-26'>Shared with : ".$share_str."</div>
		</div>
		</div>
		";
	}
}
else{
	echo "Login to see this page.";
}
?>
</body>
</html>