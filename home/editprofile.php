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

$getname = mysql_query("SELECT * FROM users WHERE user_id = '$user_id'");
while($q = mysql_fetch_array($getname)){
	$profile_image = $q['profilepic'];
	$name = $q['name'];
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
<script type="text/javascript" >
$(document).ready(function(){
	$(".edit").click(function(){
		var element = $(this);
		var I = element.attr("id");
		var display = "#"+I+"field";
		$(display).show();
		var cancel = "#cancel"+I;
		$(cancel).show();
		$(display).focus();
	});
	$(".cancel").click(function(){
		var element = $(this);
		var I = element.attr("id");
		var newI = I.replace("cancel","");
		//alert(newI);
		$("#"+newI+"field").hide();
		$(element).hide();
		
	});
	$(".editfield").change(function(){
		var element = $(this);
		var I = element.attr("id");
		var val = $("#"+I).val();
		//alert(val);
		if(I == "edit_namefield"){
			var info = "name = "+val;
		}
		if(I == "edit_mailfield"){
			var info = "mail = "+val;
		}
		if(I == "edit_genfield"){
			var info = "gen = "+val;
		}
		if(I == "edit_agefield"){
			var info = "age = "+val;
		}
		if(I == "edit_placefield"){
			var info = "place = "+val;
		}

		$.ajax({
		   type: "POST",
		   url: "updateprofile.php",
		   data: info,
		   success: function(){}
		 });
		
		//Reload the page
		window.location.assign("../home/editprofile.php");
	});
});
</script>
</head>
<body>
<div id="profile_details" style="position: relative;top: 150px;padding-left: 50px;">
<h3>About Yourself</h3>
<table style="border-spacing:10px">
	<tr>
	<td>Full Name : </td><td><?php echo $name;?></td>
	<td><center><a href="#" class="edit" id="edit_name">Edit</a></center></td>
	<td><input type="text" value="<?php echo $name;?>" autocomplete="off" id="edit_namefield" class="editfield" style="display:none"/></td>
	<td><a class="cancel" id="canceledit_name" style="background:none;text-decoration:underline;color:blue;display:none" href="#" >Cancel</a></td>
	</tr>
	<tr>
	<td>E-mail : </td><td><?php echo $email;?></td>
	<td><center><a href="#" class="edit" id="edit_mail">Edit</a></center></td>
	<td><input type="text" value="<?php echo $email;?>" autocomplete="off" id="edit_mailfield" class="editfield" style="display:none"/></td>
	<td><a class="cancel" id="canceledit_mail" style="background:none;text-decoration:underline;color:blue;display:none" href="#" >Cancel</a></td>
	</tr>
	<tr>
	<td>Gender : </td><td><?php echo $gender;?></td>
	<td><center><a href="#" class="edit" id="edit_gen">Edit</a></center></td>
	<td><input type="text" value="<?php echo $gender;?>" autocomplete="off" id="edit_genfield" class="editfield" style="display:none"/></td>
	<td><a class="cancel" id="canceledit_gen" style="background:none;text-decoration:underline;color:blue;display:none" href="#" >Cancel</a></td>
	</tr>
	<tr>
	<td>Age : </td><td><?php echo $age;?></td>
	<td><center><a href="#" class="edit" id="edit_age">Edit</a></center></td>
	<td><input type="text" value="<?php echo $age;?>" autocomplete="off" id="edit_agefield" class="editfield" style="display:none"/></td>
	<td><a class="cancel" id="canceledit_age" style="background:none;text-decoration:underline;color:blue;display:none" href="#" >Cancel</a></td>
	</tr>
	<tr>
	<td>Place : </td><td><?php echo $place;?></td>
	<td><center><a href="#" class="edit" id="edit_place">Edit</a></center></td>
	<td><input type="text" value="<?php echo $place;?>" autocomplete="off" id="edit_placefield" class="editfield" style="display:none"/></td>
	<td><a class="cancel" id="canceledit_place" style="background:none;text-decoration:underline;color:blue;display:none" href="#" >Cancel</a></td>
	</tr>
	<tr>
	<td>Member Since : </td><td><?php echo $member;?></td>
	</tr>
</table>
</div>
</body>
</html>
<?php
}
?>