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
//function for searching images...
function search_results($keywords) {
	$returned_results = array();
	$where1 = "";$where2 = "";
	
	$keywords = preg_split('/[\s]+/',$keywords);
	$total_keywords = count($keywords);
	
	foreach($keywords as $key=>$keyword){
		$where1 .= "`title` LIKE '%$keyword%'";
		$where2 .= "`tags` LIKE '%$keyword%'";
		if($key != ($total_keywords - 1)){
			$where1 .= " AND ";
			$where2 .= " AND ";
		}
	}
	
	$results = "SELECT image_id,user_id,path,title FROM images WHERE $where1 UNION SELECT image_id,user_id,path,title FROM images WHERE $where2";
	$results_num = ($results = mysql_query($results)) ? mysql_num_rows($results): 0;

	while($results_row = mysql_fetch_assoc($results)){
		$returned_results[] = array(
						'image_id' => $results_row['image_id'],
						'user_id' => $results_row['user_id'],
						'path' => $results_row['path'],
						'title' => $results_row['title']
		);
	}
	return $returned_results;
}
//get following of user
$getfollowing = mysql_query("SELECT follow_id FROM follow WHERE user_id = '$user_id'");
while($a = mysql_fetch_array($getfollowing)){
	$following[] = $a['follow_id'];
}
//keyword
$keyword = $_GET['q'];
$searchtype = $_GET['stype'];
$sort = $_GET['s'];
?>
<html>
<head>
<title>Search<?php if($searchtype==1) echo " Photos";else echo " Users";?></title>
<link rel="stylesheet" href="../home/style.css">
<script type="text/javascript" src="../home/jQuery.js"></script>
<script type="text/javascript">
$(document).ready(function(){
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
	
	$(".figure").hover(function(){
		var element = $(this);
		var I = element.attr("id");
		$("#figcaption"+I).show();
	},function(){
		var element = $(this);
		var I = element.attr("id");
		$("#figcaption"+I).hide();
	});
	//like-unlike
	$(".unlike").click(function(){
		var element = $(this);
		var I = element.attr("id");
		var info = 'image_id=' + I;
	
	$.ajax({
	   type: "POST",
	   url: "../home/likeunlike.php?action=unlike",
	   data: info,
	   success: function(data){
			//alert(data);
			var likeid = '#like_count'+I;
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
		
	$.ajax({
	   type: "POST",
	   url: "../home/likeunlike.php?action=like",
	   data: info,
	   success: function(data){
			//alert(data);
			var likeid = '#like_count'+I;
			$(likeid).html(data);
	   }
	 });
	 
		$("#like"+I).hide();
		$("#unlike"+I).show();
	return false;
	});
	//sharing...
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
		   url: "../home/share.php",
		   data: info,
		   success: function(data){
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
	window.location.assign("../search/index.php?stype="+stype+"&q="+keyword);
    $(".services").hide();
	}
}
</script>
</head>
<body>
<div id="header">
<div id="logo"></div>
<div id="search">
<input type="text" placeholder="Search" class="searchbox" value="<?php echo $keyword;?>"/>
<div id="search_opt" style="display:none;" class="services">
<ul id="menu">
	<li class="photos selected" id="1"><a href='#' >Search Photos</a></li>
	<li class="users" id="2"><a href='#' >Search Users</a></li>
</ul>
</div>
</div>
<span id='name'><a href="../home/" style="color:#9aa9c8;font-weight:bold;text-decoration:none;"><?php echo $name;?></a></span>
<span id='home'><a href="../home/" style="color:#9aa9c8;font-weight:bold;text-decoration:none;">Home</a></span>
<span id='settings'><a href="../settings/" style="color:#9aa9c8;font-weight:bold;text-decoration:none;">Settings</a></span>
<span id='logout'><a href="../logout/" style="color:#9aa9c8;font-weight:bold;text-decoration:none;">Logout</a></span>
</div>
<div id="content">
<div id="sidebar">
	<div id="profileimage"><img src="<?php echo $profilepic; ?>" class="imageclass"></div>
	<div id="actions">
		<ul>
		<li><a href="../home/">Profile</a></li>
		<li><a href="../myphotos/">Photos</a></li>
		<li><a href="../followers/">Followers</a></li>
		<li><a href="../following/">Following</a></li>
		<li><a href="#">Upload Photo</a></li>
		</ul>
	</div>
</div>
<div id="main">
<div id="search_results" >
	<h4>Search Results for "<?php echo $keyword;?>"</h4>
	<?php
	if($searchtype == 2){ // Searching Users...
		?>
		<table cellpadding="3" cellspacing="10">
		<?php
		if($sort == "date"){
			$searchq = mysql_query("SELECT * FROM users WHERE name LIKE '%$keyword%' AND user_id!='$user_id' ORDER BY member DESC");
		}
		else if($sort == "imag"){
			$searchq = mysql_query("SELECT *
									FROM `users`
									INNER JOIN (SELECT COUNT(*)AS images_tot,user_id FROM `images` GROUP BY `user_id`) AS t1
									ON users.user_id = t1.user_id
									WHERE name LIKE '%$keyword%' AND users.user_id != '$user_id'
									ORDER BY t1.images_tot DESC");
		}
		else if($sort == "foll"){
			$searchq = mysql_query("SELECT *
									FROM `users`
									INNER JOIN (SELECT COUNT(*)AS followers_tot,follow_id FROM `follow` GROUP BY `follow_id`) AS t1
									ON users.user_id = t1.follow_id
									WHERE name LIKE '%$keyword%' AND users.user_id != '$user_id'
									ORDER BY t1.followers_tot DESC");
		}
		else{
			$searchq = mysql_query("SELECT * FROM users WHERE name LIKE '%$keyword%' AND user_id!='$user_id'");
		}
		$num1 = mysql_num_rows($searchq);
		if($num1 > 0){
			echo "Your search for <b>".$keyword."</b> returned ".$num1." results.<br><br>";
			echo "
			Sort : ";
			if($sort == ""){
				echo "<span id='sort1' style='color:#777;font-weight:bold'>Name</span>";
			}else{
				echo "<span id='sort1'><a href='../search/index.php?q=".$keyword."&stype=2'>Name</a></span>";
			}
			echo "<span class='divider' style='border-left:2px solid #d8d8d8;margin-left:10px'></span>";
			if($sort == "imag"){
				echo "<span id='sort2' style='position:absolute;margin-left:15px;color:#777;font-weight:bold'>Number of Images</span>";
			}else{
				echo "<span id='sort2' style='position:absolute;margin-left:15px;'><a href='../search/index.php?q=".$keyword."&s=imag&stype=2'>Number of Images</a></span>";
			}
			echo "<span class='divider' style='border-left:2px solid #d8d8d8;margin-left:190px'></span>";
			if($sort == "foll"){
				echo "<span id='sort3' style='position:absolute;margin-left:15px;color:#777;font-weight:bold'>Number of Followers</span>";
			}else{
				echo "<span id='sort3' style='position:absolute;margin-left:15px'><a href='../search/index.php?q=".$keyword."&s=foll&stype=2'>Number of Followers</a></span>";
			}
			echo "<span class='divider' style='border-left:2px solid #d8d8d8;margin-left:210px'></span>";
			if($sort == "date"){
				echo "<span id='sort4' style='position:absolute;margin-left:15px;color:#777;font-weight:bold'>Date Joined</span><br>";
			}else{
				echo "<span id='sort4' style='position:absolute;margin-left:15px'><a href='../search/index.php?q=".$keyword."&s=date&stype=2'>Date Joined</a></span><br>";
			}
			
			while($row1 = mysql_fetch_array($searchq)){
			
				$res_id = $row1['user_id'];
				$res_name = $row1['name'];
				$res_mail = $row1['email'];
				$res_profpic = $row1['profilepic'];
					
				//get num of images
				$getimages = mysql_query("SELECT COUNT(user_id) AS img_cnt FROM images WHERE user_id='$res_id'");
				$data1 = mysql_fetch_assoc($getimages);
				$img_count = $data1['img_cnt'];
				//get num of followers
				$getfollowers = mysql_query("SELECT COUNT(user_id) AS follower_cnt FROM follow WHERE follow_id='$res_id'");
				$data2 = mysql_fetch_assoc($getfollowers);
				$follower_count = $data2['follower_cnt'];
				echo "
				<tr class='record'>
				<td width='70'><center><img src=".$res_profpic." class='imageclass' style='width:50px;height:50px'></center></td>
				<td width='276' class='content'>
				<div><a href='../others/profile.php?id=".$res_id."'>".$res_name."</a><br>".$res_mail."</div></td>
				<td width='176' class='content'>
				<div>".$img_count." <a href='../others/photos.php?id=".$res_id."'> images </a></div>
				<div>".$follower_count." <a href='../others/followers.php?id=".$res_id."'> Followers </a></div></td>
				<td width='176' class='content'></td>
				</tr>
				";
			}
		}
		else{
			echo "<tr>We couldn't find anyone matching your search</tr>";
			echo "<br><br>A few suggestions : <br><br>
			<ul>
				<li>Check your spelling.</li>
				<li>Try more general words.</li>
				<li>Try different words that mean the same thing.</li>
			</ul>	
			";
		}
		?>
		</table>
		<?php
		}
	else{ // Searching Photos...
		?>
		<table style="position:relative;top:20px">
		<?php
		//get images liked by user
		$getlikes = mysql_query("SELECT image_id FROM likes WHERE user_id = '$user_id'");
		while($b = mysql_fetch_array($getlikes)){
			$likes[] = $b['image_id'];
		}
		
		$results = search_results($keyword);
		$results_num = count($results);
		$i = 1;$limit = 2;$counter = 1;
		if($results_num > 0){
			echo "Your search for <b>".$keyword."</b> returned ".$results_num." results.<br><br>";
			echo "
			Sort : ";
			if($sort == ""){
				echo "<span id='sort1' style='color:#777;font-weight:bold'>Relevence</span>";
			}else{
				echo "<span id='sort1'><a href='../search/index.php?q=".$keyword."&stype=1'>Relevence</a></span>";
			}
			echo "<span class='divider' style='border-left:2px solid #d8d8d8;margin-left:10px'></span>";
			if($sort == "pop"){
				echo "<span id='sort2' style='position:absolute;margin-left:15px;color:#777;font-weight:bold'>Popularity</span>";
			}else{
				echo "<span id='sort2' style='position:absolute;margin-left:15px;'><a href='../search/index.php?q=".$keyword."&s=pop&stype=1'>Popularity</a></span>";
			}
			
			foreach($results as $result){
				$img_id = $result['image_id'];
				$uploader_id = $result['user_id'];
				$title = $result['title'];
				$path = $result['path'];
				
				if($i === 1){
					echo '<tr><td>';
				}
				//get uploader details
				$getuploadername = mysql_query("SELECT * FROM users WHERE user_id = '$uploader_id'");
				while($row2 = mysql_fetch_array($getuploadername))
				{
					$uploadername = $row2['name'];
				}
				//get likes of image
				$getlikenum = mysql_query("SELECT COUNT(user_id) AS likes_cnt FROM likes WHERE image_id='$img_id'");
				$data = mysql_fetch_assoc($getlikenum);
				$like_count = $data['likes_cnt'];
				//get num of shares
				$getsharenum = mysql_query("SELECT COUNT(user_id) AS share_cnt FROM share WHERE image_id='$img_id'");
				$data1 = mysql_fetch_assoc($getsharenum);
				$share_count = $data1['share_cnt'];
				if($i < $limit){
					echo "
					<div id='".$img_id."' class='figure'>
					<a href='../image.php?id=".$img_id."'><img src=".$path."  style='width:400px;height:250px'></a>
					<div id='figcaption".$img_id."' class='figcaption'>
					<div style='float:left'>Uploaded by : <b><a href='../others/profile.php?id=".$uploader_id."'>".$uploadername."</a></b><br>Title : <b><a href='../image.php?id=".$img_id."'>".$title."</a></b></div>";
					if(in_array($img_id,$likes)){
						echo "
						<div id='like".$img_id."' style='display:none;width: 50px;position: relative;top: -26;left: 240px;height: 10px;'><a href='#' class='like' id='".$img_id."'><span class='follow_b'> Like </span></a></div>
						<div id='unlike".$img_id."' style='width: 50px;position: relative;top: -26;left: 240px;height: 10px;'><a href='' class='unlike' id='".$img_id."'><span class='remove_b'> Unlike </span></a></div>
					    ";
					}
					else{
						echo "
						<div id='like".$img_id."' style='width: 50px;position: relative;top: -26;left: 240px;height: 10px;'><a href='#' class='like' id='".$img_id."'><span class='follow_b'> Like </span></a></div>
						<div id='unlike".$img_id."' style='display:none;width: 50px;position: relative;top: -26;left: 240px;height: 10px;'><a href='' class='unlike' id='".$img_id."'><span class='remove_b'> Unlike </span></a></div>
					    ";
					}
					//share
					echo "
					<div id='share".$img_id."' style='width: 50px;height: 10px;position: relative;top: -10;left: 238px;'>
					<span class='share' id='".$img_id."'><span class='share_b'>Share</span></span>
					<div id='sharebox".$img_id."' class='share_box' style='display:none'>
					<input type='text' placeholder='Say Something...' autocomplete='off' class='title' id='sh_title".$img_id."'/>
					<div id='thumbnail' style='padding:10px'>
					<img src='".$path."' class='imageclass' style='width:120px;height:85px'>
					<div id='title_sh' style='position: absolute;top: 60px;left: 160px;font-size: 15px;'>Title: ".$title."</div></div>
					<div style='position: relative;' style='padding-left:10px'>
					<button id='".$img_id."' class='sh_confirm'>Share</button>
					<button id='".$img_id."' class='sh_cancel'>Cancel</button>
					<span style='float:right;font-size:12px;font-weight:bold'>
					Visisble to : <select id='visibility".$img_id."' name='visibleto".$img_id."'>
					<option value='1'>Public</option>
					<option value='2'>Followers</option>
					<option value='3'>Only Me</option>
					</select></span>
					</div>
					</div>
					</div>";
					echo "
					<div style='float:right;position:relative;top:-16px'>
					Likes : <b><span id='like_count".$img_id."'><b>".$like_count."</span></b><br>
					Shares : <b><span id='share_count".$img_id."'><b>".$share_count."</span></b></div>
					</div>
					</div>
					";
					echo '</td><td>';
					$i++;
				}
				else{
					echo "
					<div id='".$img_id."' class='figure'>
					<a href='../image.php?id=".$img_id."'><img src=".$path."  style='width:400px;height:250px'></a>
					<div id='figcaption".$img_id."' class='figcaption'>
					<div style='float:left'>Uploaded by : <b><a href='../others/profile.php?id=".$uploader_id."'>".$uploadername."</a></b><br>Title : <b><a href='../image.php?id=".$img_id."'>".$title."</a></b></div>";
					if(in_array($img_id,$likes)){
						echo "
						<div id='like".$img_id."' style='display:none;width: 50px;position: relative;top: -26;left: 240px;height: 10px;'><a href='#' class='like' id='".$img_id."'><span class='follow_b'> Like </span></a></div>
						<div id='unlike".$img_id."' style='width: 50px;position: relative;top: -26;left: 240px;height: 10px;'><a href='' class='unlike' id='".$img_id."'><span class='remove_b'> Unlike </span></a></div>
					    ";
					}
					else{
						echo "
						<div id='like".$img_id."' style='width: 50px;position: relative;top: -26;left: 240px;height: 10px;'><a href='#' class='like' id='".$img_id."'><span class='follow_b'> Like </span></a></div>
						<div id='unlike".$img_id."' style='display:none;width: 50px;position: relative;top: -26;left: 240px;height: 10px;'><a href='' class='unlike' id='".$img_id."'><span class='remove_b'> Unlike </span></a></div>
					    ";
					}
					//share
					echo "
					<div id='share".$img_id."' style='width: 50px;height: 10px;position: relative;top: -10;left: 238px;'>
					<span class='share' id='".$img_id."'><span class='share_b'>Share</span></span>
					<div id='sharebox".$img_id."' class='share_box' style='display:none'>
					<input type='text' placeholder='Say Something...' autocomplete='off' class='title' id='sh_title".$img_id."'/>
					<div id='thumbnail' style='padding:10px'>
					<img src='".$path."' class='imageclass' style='width:120px;height:85px'>
					<div id='title_sh' style='position: absolute;top: 60px;left: 160px;font-size: 15px;'>Title: ".$title."</div></div>
					<div style='position: relative;' style='padding-left:10px'>
					<button id='".$img_id."' class='sh_confirm'>Share</button>
					<button id='".$img_id."' class='sh_cancel'>Cancel</button>
					<span style='float:right;font-size:12px;font-weight:bold'>
					Visisble to : <select id='visibility".$img_id."' name='visibleto".$img_id."'>
					<option value='1'>Public</option>
					<option value='2'>Followers</option>
					<option value='3'>Only Me</option>
					</select></span>
					</div>
					</div>
					</div>";
					echo "
					<div style='float:right;position:relative;top:-16px'>
					Likes : <b><span id='like_count".$img_id."'>".$like_count."</span></b>
					<br>
					Shares : <b><span id='share_count".$img_id."'>".$share_count."</span></b></div>
					</div>
					</div>
					";
					echo '</td></tr>';
					$i = 1;
				}
			}
		}
		else{
			echo "<tr>We couldn't find anyone matching your search</tr>";
			echo "<br><br>A few suggestions : <br><br>
			<ul>
				<li>Check your spelling.</li>
				<li>Try more general words.</li>
				<li>Try different words that mean the same thing.</li>
			</ul>	
			";
		}
		?>
		</table>
	<?php	
		}
	?>
</div>
</div>
</div>
</body>
</html>
<?php
}
?>