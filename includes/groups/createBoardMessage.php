<?php

include_once '../functions.php';
include_once '../db_connect.php';

sec_session_start();

if (isset($_SESSION['user_id'], $_POST['group_id'], $_POST['title'], $_POST['body'])) {
	echo createBoardMessage($mysqli, $_SESSION['user_id'], $_POST['group_id'], $_POST['title'], $_POST['body'], 'group');
	//header('Location: ../../group.php?id=' . $_POST['group_id']);
} else {
	header('Location: ../../group.php?id=' . $_POST['group_id'] . '&error=' . $_SESSION['user_id']);
}
