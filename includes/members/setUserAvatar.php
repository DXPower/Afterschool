<?php

include_once '../functions.php';
include_once '../db_connect.php';

sec_session_start();

if (isset($_POST['user_id'], $_FILES['image'])) {
	$fileName = $_FILES['image']['tmp_name'];
	echo setUserAvatar($mysqli, $_POST['user_id'], $fileName);
} else {
	echo "POST/FILES variables not set";
}