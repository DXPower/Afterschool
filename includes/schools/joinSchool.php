<?php
include_once '../functions.php';
include_once '../db_connect.php';

sec_session_start();

if (isset($_SESSION['user_id'], $_POST['school_id'])) {
	echo joinSchool($mysqli, $_SESSION['user_id'], $_POST['school_id']);
} else { 
	echo "Missing POST Variable";
}