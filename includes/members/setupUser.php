<?php

include_once '../functions.php';
include_once '../db_connect.php';

sec_session_start();

if (isset($_SESSION['user_id'], $_POST['graduation_year'], $_FILES['image'])) {
	$fileName = $_FILES['image']['tmp_name'];
	echo setupUser($mysqli, $_SESSION['user_id'], $fileName, $_POST['graduation_year']);
} else {
	echo "POST/FILES variables not set";
}