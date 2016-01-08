<?php

include_once '../functions.php';
include_once '../db_connect.php';

sec_session_start();

if (isset($_SESSION['user_id'])) {
	echo getUserAvatar($mysqli, $_SESSION['user_id']);
} else {
	echo "Missing POST Variables";
} 