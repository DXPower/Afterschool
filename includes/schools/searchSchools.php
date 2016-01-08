<?php

include_once '../functions.php';
include_once '../db_connect.php';

sec_session_start();

if (isset($_POST['school_name'], $_POST['location'])) {
	echo json_encode(searchSchools($mysqli, $_POST['school_name'], $_POST['location']));
}