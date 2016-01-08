<?php

include_once '../functions.php';
include_once '../db_connect.php';

sec_session_start();

if (isset($_SESSION['user_id'], $_POST['school_id'], $_POST['title'], $_POST['body'])) {
	createBoardMessage($mysqli, $_SESSION['user_id'], $_POST['school_id'], $_POST['title'], $_POST['body'], 'school');
	header('Location: ../../school.php?id=' . $_POST['school_id']);
} else {
	header('Location: ../../school.php?id=' . $_POST['school_id'] . '&error=' . $_SESSION['user_id']);
}
