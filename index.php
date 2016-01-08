<?php
include_once 'includes/functions.php';
include_once 'includes/db_connect.php';

sec_session_start();

if (login_check($mysqli)) { 
	header('Location: landing.php');
}
?>

<!DOCTYPE html>
<html>
	<head>
		<title>After School</title>
        <meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="css/main.css" />
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        <script type="text/JavaScript" src="js/sha512.js"></script> 
        <script type="text/JavaScript" src="js/forms.js"></script> 
	</head>
	<body>
		<div id="login">
			<form id="login_form" action="includes/process_login.php" method="post" name="login_form">
				<input id="username"
					   placeholder="Email"
					   type="text"
					   name="email"
					   autocomplete="on"
					   value=""
					   required />
				<input id="password"
					   placeholder="Password"
					   type="password"
					   name="password"
					   autocomplete="on"
					   value=""
					   required />
				<button id="loginButton"
						type="submit" 
						onclick="formhash(this.form, this.form.password);">
					Sign In</button> 
			</form>
		</div>
		<div id="register">
			<form id="register_form" action="includes/register.inc.php" method="post" name="register_form">
				<input id="username"
					   placeholder="Username"
					   type="text"
					   name="username" 
					   required />
				<input id="first_name"
					   placeholder="First Name"
					   type="text"
					   name="first"
					   required />
				<input id="last_name"
					   placeholder="Last Name"
					   type="text"
					   name="last"
					   required />
				<input id="email"
					   placeholder="Email"
					   type="text"
					   name="email"
					   required />
				<input id="password"
					   placeholder="Password"
					   type="password"
					   name="password"
					   required />
				<input id="confirmpwd"
					   placeholder="Retype Password"
					   type="password"
					   name="confirmpwd"
					   required />
				<button id="register_button" 
						type="submit" 
						onclick="return regformhash(this.form, this.form.username, this.form.email, this.form.password, this.form.confirmpwd);">
					Register</button>
			</form>
		</div>
		
	</body>
</html>