<?php

include_once 'includes/functions.php';
include_once 'includes/db_connect.php';

sec_session_start();

if (!login_check($mysqli)) {
	header('Location: index.php?error=login');
}

$schools = getUsersSchools($mysqli, $_SESSION["user_id"]);
$groups = getUsersGroups($mysqli, $_SESSION["user_id"]);
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
		<script src="js/schoolAjax.js"></script>
		<script src="js/memberAjax.js"></script>
	</head>
	<body>
		<a href="includes/logout.php">Logout</a>
		<a href="user.php?id=<?php echo $_SESSION["user_id"]; ?>">Profile</a>
		<form id="school_form">
			<input id="school"
				   placeholder="School Name"
				   type="text"
				   name="school"
				   value=""
				   oninput="searchSchools(this.form.school.value, this.form.location.value)"
				   required />
			<input id="location"
				   placeholder="School Location"
				   type="text" 
				   name="location"
				   oninput="searchSchools(this.form.school.value, this.form.location.value)"
				   value="" />
			<button id="search" 
					type="button">   
				Search
			</button>
			<button id="create"
					type="button"
					onclick="createSchool(this.form.school.value, this.form.location.value);">
				Create 
			</button>
		</form>
		<ul id="search_results">

		</ul>
		<img width="250" height="250" src="<?php echo getUserAvatar($mysqli, $_SESSION['user_id']); ?>" /> 
		<p>It looks like you aren't setup yet!</p>
		<form id="user_setup_form">
			<p>Upload a profile picture</p>
			<img id="preview" width="0" height="0" />
			<input id="image"
				   name="image" 
				   type="file"
				   accept="image/*"
				   onchange="loadFile(event);"
				   />
		    <script>
		        var loadFile = function(event) {
		          console.log(event);
                  var output = document.getElementById('preview');
                  output.src = URL.createObjectURL(event.target.files[0]);
                  output.width = 100;
                  output.height = 100;
                };
		    </script>
			<p>Select your graduation year</p>
			<select id="graduation_year"
					name="graduation_year">
				<option value="2015">2015</option>
				<option value="2016">2016</option>
				<option value="2017">2017</option>
				<option value="2018">2018</option>
				<option value="2019">2019</option>
				<option value="2020">2020</option>
				<option value="2021">2021</option>
				<option value="2022">2022</option>
				<option value="2023">2023</option>
				<option value="2024">2024</option>
				<option value="2025">2025</option>
			</select>
			<button id="setup"
					type="button">
				Setup
			</button>
		</form>
		
		<p>Member is setup!</p>
		<h3>Schools</h3>
		<ul>
			<?php foreach ($schools as $school) {
				$id = $school["id"];
				$name = $school["name"];
				$location = $school["location"]; ?>
				<li>
					<a href="school.php?id=<?php echo $id; ?>"><?php echo $name; ?></a>
				</li>
			<?php } ?>
		</ul>
		<h3>Groups</h3>
		<ul>
			<?php foreach ($groups as $group) {
				$id = $group["id"];
				$name = $group["name"];
				$schoolName = $group["school"]["name"]; 
				$schoolId = $group["school"]["id"]; ?>
				<li>
					<a href="group.php?id=<?php echo $id; ?>"><?php echo $name; ?></a> - <a href="school.php?id=<?php echo $schoolId; ?>"><?php echo $schoolName; ?></a>
				</li>
			<?php } ?>
		</ul>
	</body>
</html>