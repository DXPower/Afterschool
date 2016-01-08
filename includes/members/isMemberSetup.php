<?php

include_once '../db_connect.php';
include_once '../functions.php';

sec_session_start(); 

if (isset($_SESSION['user_id'])) {
	echo isMemberSetup($mysqli, $_SESSION['user_id']);
}
