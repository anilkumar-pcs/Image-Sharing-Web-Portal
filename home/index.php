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
//create a folder for the user
mkdir("../images/".$user_id."");
function getExtension($str)
{
         $i = strrpos($str,".");
         if (!$i) { return ""; }
         $l = strlen($str) - $i;
         $ext = substr($str,$i+1,$l);
         return $ext;
}
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

$valid_formats = array("jpg", "png", "gif", "bmp","jpeg");
if($_POST['submit'] && isset($_POST['submit'])){
	$title = $_POST['title'];
	if($title == ''){
		$title = "Untitled";
	}
	$tags = $_POST['tags'];
	$visibleto = $_POST['visibleto'];
	$uploaddir = "../images/".$user_id."/";
	if(!empty($_FILES['files'])){
		foreach ($_FILES['files']['name'] as $name => $value)
		{
			$filename = stripslashes($_FILES['files']['name'][$name]);
			$size=filesize($_FILES['files']['tmp_name'][$name]);
			$ext = getExtension($filename);
			$ext = strtolower($ext);
			if(in_array($ext,$valid_formats))
			{
				$time = time();
				$newname=$uploaddir.$filename;
				if (move_uploaded_file($_FILES['files']['tmp_name'][$name], $newname)) 
				{
				$time=time();
				mysql_query("INSERT INTO images(`image_id`, `user_id`, `title`, `path`, `time`, `visible`, `tags`) VALUES('','$user_id','$title','$newname','$time','$visibleto','$tags')");
				//mysql_query("UPDATE `images` SET `user_id`='$user_id',`title`='$title',`path`='$newname',`time`='$time',`visible`='$visibleto',`tags`='$tags' ORDER BY `image_id` DESC LIMIT 1");
				$upload_stat = 1;
				}
			}
			else{
				echo "Not a Valid Extension."; 
			}
		}
	}
	else{
		$upload_stat = 2;
	}
}
?>
<link rel="stylesheet" href="style.css" />
<script type="text/javascript" src="jQuery.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	
	$('.customInputFile a').click(function() {
		$(this).next('input[type="file"]').click();
	});
	$('#editpic a').click(function() {
		$(this).next('input[type="file"]').click();
	});
	$('#cancel').click(function() {
		$("#list").html("");
		$("#preview").hide();
	});
	$('#profileimage').hover(function() {
		$("#editpic").show();
	},function(){
		$("#editpic").hide();
	});
	if($("#status").html != ""){
		$("#status").fadeOut("slow");
	}
	document.getElementById('files').addEventListener('change', handleFileSelect, false);
});
function handleFileSelect(evt) {
    var files = evt.target.files; // FileList object

    // Loop through the FileList and render image files as thumbnails.
    for (var i = 0, f; f = files[i]; i++) {

      // Only process image files.
      if (!f.type.match('image.*')) {
        continue;
      }

      var reader = new FileReader();

      // Closure to capture the file information.
      reader.onload = (function(theFile) {
        return function(e) {
          // Render thumbnail.
          var span = document.createElement('span');
          span.innerHTML = ['<img class="thumb" src="', e.target.result,
                            '" title="', escape(theFile.name), '"/>'].join('');
          document.getElementById('list').insertBefore(span, null);
        };
      })(f);

      // Read in the image file as a data URL.
      reader.readAsDataURL(f);
    }
	$("#preview").show();
	$("#submit").show();
}
</script>
<script type="text/javascript">
$(document).ready(function(){
	//Unlike...
	$(".unlike").click(function(){
		var element = $(this);
		var I = element.attr("id");
		var info = 'image_id=' + I;
	
	$("#loading"+I).show();
	
	$.ajax({
	   type: "POST",
	   url: "likeunlike.php?action=unlike",
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
	   url: "likeunlike.php?action=like",
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
		   url: "share.php",
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
	
	//Searching.....
	
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
	
	$("#choose_opt").click(function(){
		if ($(".feed_type").css("display") == "none") {
			$(".feed_type").show();
		} else {
			$(".feed_type").hide();
		}
	});
});
function search() {
	var keyword = $(".searchbox").val();
    var stype = $(".selected").attr("id");
	if(keyword != ""){
		window.location.assign("../search/index.php?stype="+stype+"&q="+keyword);
		$(".services").hide();
	}
}
//Profile pic Upload...
/*function readURL(input) {
	if (input.files && input.files[0]) {
		var reader = new FileReader();

		reader.onload = function (e) {
			alert(e.target.result);
		};

		reader.readAsDataURL(input.files[0]);
	}
}*/
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
		<li><a href="../home">Profile</a></li>
		<li><a href="../myphotos">Photos</a></li>
		<li><a href="../followers">Followers</a></li>
		<li><a href="../following">Following</a></li>
		<li><a href="#">Upload Photo</a></li>
		</ul>
	</div>
</div>
<div id="main">
	<div id="topmost">
		<p id="status"><?php if($upload_stat==1) echo "Uploaded ...!";
				 if($upload_stat==2) echo "Nothing Uploaded ...!";?></p>
		<form name="img_upload" action=" " method="POST" enctype="multipart/form-data" onkeypress="return event.keyCode != 13;">
			<span class="customInputFile">
			<a href="javascript:void(0)" style="text-decoration:none">Upload Photo</a>
			<input type="file" name="files[]" multiple="multiple" id="files"/>
			</span>
			<div id="preview">
			<input type="text" name="title" placeholder="Say Something..." class="title" autocomplete="off"/>
			<input type="text" name="tags" placeholder="Add Tags...Separate them by space" class="title" autocomplete="off"/>
			<output id="list"></output>
			<p><input type="submit" value="Upload" id="submit" name="submit" style="display:none;"/>
			<input type="button" id="cancel" value="Cancel" />
			<span style="float:right;font-size:12px;font-weight:bold">
			Visisble to : <select id="visibility" name="visibleto">
			<option value="1">Public</option>
			<option value="2">Followers</option>
			<option value="3">Only Me</option>
			</select></span>
			</p>
			</div>
		</form>
		<div id="feed_option">
		<span>Show in Feed <img src="down_arrow.png" id="choose_opt" style="cursor:pointer;position: relative;top: 3px;"></span>
		<ul class="feed_type" style="display:none;list-style-type:none;">
		<li><a href="../home/">Uploaded Photos</a></li>
		<li><a href="../home/?sk=share">Shared Photos</a></li>
		</ul>
		</div>
	</div>
	<div id="feed">
	<?php
		$getlikes = mysql_query("SELECT image_id FROM likes WHERE user_id = '$user_id'");
		while($a = mysql_fetch_array($getlikes)){
			$likes[] = $a['image_id'];
		}
		$sk = $_GET['sk'];
		if($sk == "share"){
			$getimages = mysql_query("SELECT * FROM share WHERE user_id IN(SELECT follow_id FROM follow WHERE user_id = '$user_id') ORDER BY time DESC");
			while($row1 = mysql_fetch_array($getimages)){
				$image_id = $row1['image_id'];
				$share_user = $row1['user_id'];
				$time = $row1['time'];
				$getimage = mysql_query("SELECT * FROM images WHERE image_id='$image_id'");
				while($row2 = mysql_fetch_array($getimage)){
					$other_userid = $row2['user_id'];
					$image = $row2['path'];
					$title = $row2['title'];
				}
				//get uploader details
				$newname = mysql_query("SELECT name,profilepic FROM users WHERE user_id = '$share_user'");
				while($x = mysql_fetch_array($newname)){
					$share_username = $x['name'];
					$share_profilepic = $x['profilepic'];
				}
				$newname1 = mysql_query("SELECT name,profilepic FROM users WHERE user_id = '$other_userid'");
				while($y = mysql_fetch_array($newname1)){
					$other_username = $y['name'];
				}
				//get num of likes
				$getlikenum = mysql_query("SELECT COUNT(user_id) AS likes_cnt FROM likes WHERE image_id='$image_id'");
				$data = mysql_fetch_assoc($getlikenum);
				$like_count = $data['likes_cnt'];
				//get num of shares
				$getsharenum = mysql_query("SELECT COUNT(user_id) AS share_cnt FROM share WHERE image_id='$image_id'");
				$data1 = mysql_fetch_assoc($getsharenum);
				$share_count = $data1['share_cnt'];
				
				echo "
				<div id='item'>
				<div id='prof_pic'>
				<div id='pic'><img src='".$share_profilepic."' class='imageclass' style='width:50px;height:50px' ></div>
				<div id='picdetails'><span><a href='../others/profile.php?id=".$share_user."'>".$share_username."</a> Shared <a href='../others/profile.php?id=".$other_userid."'>".$other_username."</a>'s photo.</span><br />
				<span>Title : ".$title."</span><br /></div>
				</div>
				<div id='feedimage'>
				<a href='../image.php?id=".$image_id."' ><img src='".$image."' class='imageclass' style='width:450px;height:300px' ></a><br /><br />";
				
				if(in_array($image_id,$likes)){
					echo "
					<div id='like".$image_id."' style='display:none'><a href='#' class='like' id='".$image_id."'><span class='follow_b'> Like </span></a></div>
					<div id='unlike".$image_id."'><a href='' class='unlike' id='".$image_id."'><span class='remove_b'> Unlike </span></a></div>
					<span id='like_count".$image_id."' style='position:relative;top:-18px;left:52px'>(".$like_count.")
					<div id='loading".$image_id."' style='display:none'><img src='loading.gif'></div>
					</span>
					";
				}
				else{
					echo"
					<div id='like".$image_id."'><a href='#' class='like' id='".$image_id."'><span class='follow_b'> Like </span></a></div>
					<div id='unlike".$image_id."' style='display:none'><a href='' class='unlike' id='".$image_id."'><span class='remove_b'> Unlike </span></a></div>
					<span id='like_count".$image_id."' style='position:relative;top:-18px;left:52px'>(".$like_count.")
					<div id='loading".$image_id."' style='display:none'><img src='loading.gif'></div>
					</span>
					";
				}
				
				echo"
				<div id='share".$image_id."' style='position: relative;top: -36px;left: 100px;'>
				<span class='share' id='".$image_id."'><span class='share_b'>Share</span></span>
				<span id='share_count".$image_id."'>(".$share_count.")</span>
				<div id='sharebox".$image_id."' class='share_box' style='display:none'>
				<input type='text' placeholder='Say Something...' autocomplete='off' class='title' id='sh_title".$image_id."'/>
				<div id='thumbnail' style='padding:10px'>
				<img src='".$image."' class='imageclass' style='width:120px;height:85px'>
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
				<div style='position: relative;top: -53px;left: 210px;'>uploaded : ".facebook_style_date_time($time)."</div>
				</div>
				</div>
				";
			}
		} 
		else{
			$getimages = mysql_query("SELECT * FROM images WHERE user_id != '$user_id' AND visible = '1' ORDER BY time DESC");
			//$getsharedimages = mysql_query("SELECT * FROM share WHERE user_id IN (SELECT follow_id FROM follow WHERE user_id = '$user_id') ORDER BY time DESC");
			while($row1 = mysql_fetch_array($getimages)){
			$other_userid = $row1['user_id'];
			$image = $row1['path'];
			$image_id = $row1['image_id'];
			$title = $row1['title'];
			$time = $row1['time'];
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
			echo "
			<div id='item'>
			<div id='prof_pic'>
			<div id='pic'><img src='".$other_profilepic."' class='imageclass' style='width:50px;height:50px' ></div>
			<div id='picdetails'><span><a href='../others/profile.php?id=".$other_userid."'>".$other_username."</a> uploaded a photo.</span><br />
			<span>Title : ".$title."</span><br /></div>
			</div>
			<div id='feedimage'>
			<a href='../image.php?id=".$image_id."' ><img src='".$image."' class='imageclass' style='width:450px;height:300px' ></a><br /><br />";
			
			if(in_array($image_id,$likes)){
				echo "
				<div id='like".$image_id."' style='display:none'><a href='#' class='like' id='".$image_id."'><span class='follow_b'> Like </span></a></div>
				<div id='unlike".$image_id."'><a href='' class='unlike' id='".$image_id."'><span class='remove_b'> Unlike </span></a></div>
				<span id='like_count".$image_id."' style='position:relative;top:-18px;left:52px'>(".$like_count.")
				<div id='loading".$image_id."' style='display:none'><img src='loading.gif'></div>
				</span>
				";
			}
			else{
				echo"
				<div id='like".$image_id."'><a href='#' class='like' id='".$image_id."'><span class='follow_b'> Like </span></a></div>
				<div id='unlike".$image_id."' style='display:none'><a href='' class='unlike' id='".$image_id."'><span class='remove_b'> Unlike </span></a></div>
				<span id='like_count".$image_id."' style='position:relative;top:-18px;left:52px'>(".$like_count.")
				<div id='loading".$image_id."' style='display:none'><img src='loading.gif'></div>
				</span>
				";
			}
			
			echo"
			<div id='share".$image_id."' style='position: relative;top: -36px;left: 100px;'>
			<span class='share' id='".$image_id."'><span class='share_b'>Share</span></span>
			<span id='share_count".$image_id."'>(".$share_count.")</span>
			<div id='sharebox".$image_id."' class='share_box' style='display:none'>
			<input type='text' placeholder='Say Something...' autocomplete='off' class='title' id='sh_title".$image_id."'/>
			<div id='thumbnail' style='padding:10px'>
			<img src='".$image."' class='imageclass' style='width:120px;height:85px'>
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
			<div style='position: relative;top: -53px;left: 210px;'>uploaded : ".facebook_style_date_time($time)."</div>
			</div>
			</div>
			";
			}
		}
	?>	
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