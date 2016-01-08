<?php

include_once "includes/functions.php";
include_once "includes/db_connect.php";

sec_session_start();

$userId = $_GET['id'];
$member = getMember($mysqli, $userId);
$schools = getUsersSchools($mysqli, $userId);
$groups = getUsersGroups($mysqli, $userId);
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
	</head>
	<body>
		<img src="<?php echo $member["image"]; ?>" height="150" width="150">
		<h2><?php echo $member["firstName"] . ' ' . $member["lastName"]; ?></h2>
		<em style="color: gray">Member since <?php echo $member["date"]; ?></em><br>
		<em style="color: gray">Class of <?php echo $member["graduationYear"]; ?></em>
		
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
