<?php
error_reporting(0);
?>
<html>
<head>
<title>Login</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div id="header">
<?php
$msg = $_GET['msg'];
?>
<div id="msg" style="<?php if($msg == 1){echo "";}else{echo "display:none;";}?>position: absolute;top: 24px;left: 400px;width: 200px;height: 50px;background: #D8EC87;border-radius: 7px;text-align: center;">
<p>Registration Successful</p>
</div>
<div id="login">
<span style="position: absolute;left: 635px;top: 40px;font-size: 20px;">Sign In</span>
<form id="loginform" name="form1" method="POST" action="test.php" >
<table>
<tr>
<td style="color:#fff;font-size:12px;padding-left:5px">Email</td>
<td style="color:#fff;font-size:12px;padding-left:5px">Password</td>
</tr>
<tr>
<td><input type="text" name="email" autocomplete="off" ></td>
<td><input type="password" name="password"></td>
<td><input type="submit" value="Log In" name="login" id="submit"></td>
</tr>
</table>
</form>
</div>
</div>
<div id="main">
<div id="register">
<h2 style="font-weight:normal">Register</h2>
<form id="registerform" name="form2" method="POST" action="test.php" >
<table style="border-spacing:10">
<tr>
<td>Name : </td>
<td><input type='text' autocomplete='off' name='name' ></td>
</tr>
<tr>
<td>E-mail : </td>
<td><input type='text' autocomplete='off' name='email' ></td>
</tr>
<tr>
<td>Choose a Password : </td>
<td><input type='password' name='password' ></td>
</tr>
<tr>
<td>Repeat your Password : </td>
<td><input type='password' name='repassword' ></td>
</tr>
<tr>
<td>You are : </td>
<td><input type='radio' name='gender' value='male'>Male<input type='radio' name='gender' value='female'>Female</td>
</tr>
<tr>
<td>Age : </td>
<td><input type='text' autocomplete='off' name='age' ></td>
</tr>
<tr>
<td>Place : </td>
<td><input type='text' autocomplete='off' name='place' ></td>
</tr>
</table>
<p><input type="submit" value="Register" name="register" id="submit"></p>
</form>
</div>
</div>
</body>
</html>