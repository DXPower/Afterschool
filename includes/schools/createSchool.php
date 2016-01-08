<?php

include_once '../functions.php';
include_once '../db_connect.php';

sec_session_start();

if (isset($_POST['school_name'], $_POST['location'])) {
	echo createSchool($mysqli, $_POST['school_name'], $_POST['location']);
} else {
	echo "error";
	header("HTTP/1.0 500 Internal Server Error");
	exit();
}