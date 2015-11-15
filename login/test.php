<?php
session_start();

//Login

if(isset($_POST['login']) && isset($_POST['email']) && isset($_POST['password'])){
	$submit = $_POST['login'];
	$email = $_POST['email'];
	$password = $_POST['password'];
	if($submit){
		if($email && $password){
			$conn = mysql_connect("localhost","root","");
			mysql_select_db("flickr");
			
			$query = mysql_query("SELECT password FROM users WHERE email='$email'");
			$numrows = mysql_num_rows($query);
			if($numrows != 0){
				while($row = mysql_fetch_array($query))
				{
					$dbpassword = $row['password'];
				}
				if($password == $dbpassword){
					$page = "../home/";
					$_SESSION['email'] = $email;
					header('Location: '.$page);
				}
				else{
					echo "Incorrect Password!";
				}
			}
			else{
				echo "That User do not Exist!";
			}
		}
		else{
			echo "Please fill in Details.";
		}
	}
}

//Register

if(isset($_POST['register']) && isset($_POST['name']) && isset($_POST['email']) && isset($_POST['gender']) && isset($_POST['password']) && isset($_POST['repassword']) && isset($_POST['age'])){
	$submit = $_POST['register'];
	$name = $_POST['name'];
	$email = $_POST['email'];
	$gender = $_POST['gender'];
	$password = $_POST['password'];
	$repassword = $_POST['repassword'];
	$age = $_POST['age'];
	$place = $_POST['place'];
	$member = date("Y-m-d");
	
	if($submit){
		if($name && $email && $gender && $password && $repassword && $age && $place){
			if($password == $repassword){
					$conn = mysql_connect("localhost","root","");
					mysql_select_db("flickr");
					
					$query = mysql_query("
					INSERT INTO users VALUES('','$name','$gender','$age','$email','$password','../images/default.jpg','$place','$member')
					");
					$page = "index.php?msg=1";
					header('Location: '.$page);
					//echo "You have been Registered.<a href='login.php'>Login</a> Here.";
			}
			else{
				echo "Your Passwords do not match."; 
			}
		}
		else{
			echo "Please fill in <b>ALL FIELDS</b>.";
		}
	}
}
?>