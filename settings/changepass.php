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
	$dbpassword = $q['password'];
}
?>
<html>
<head>
<title><?php echo $profile_name;?></title>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.0/jquery.min.js"></script>
<script type="text/javascript" >
$(document).ready(function(){
	$("#submit").click(function(){
		var oldpass = $("#oldpass").val();
		var newpass = $("#newpass").val();
		var renewpass = $("#renewpass").val();
		
		var dbpass = "<?php echo $dbpassword; ?>";
		if(oldpass == dbpass){
			if(newpass.length >= 6 ){
				if(newpass == renewpass){
					var info = "pass="+newpass;
					$.ajax({
					   type: "POST",
					   url: "updatepass.php",
					   data: info,
					   success: function(){
							alert("Updated Successfully!");
					   }
					});
				}
				else{
					alert("New Passwords do not match!");
				}
			}
			else{
				alert("Enter a Valid New Password!");
			}
		}
		else{
			alert("Old Password do not match!");
		}
	});
});
</script>
</head>
<body>
<div id="profile_details" style="position: relative;top: 150px;padding-left: 50px;">
<h3>Change Password</h3>
<table style="border-spacing:10px">
	<tr><td>Current</td><td><input type="password" name="oldpass" id="oldpass"></td></tr>
	<tr><td>New</td><td><input type="password" name="newpass" id="newpass"></td></tr>
	<tr><td>ReType New</td><td><input type="password" name="renewpass" id="renewpass"></td></tr>
</table>
<p><input type="button" id="submit" value="Submit"></p>
</div>
</body>
</html>
<?php
}
?>