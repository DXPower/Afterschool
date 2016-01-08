<?php
include_once '../functions.php';
include_once '../db_connect.php';

sec_session_start();

if (isset($_SESSION['user_id'], $_POST['body'], $_POST['message_id'])) {
	$decodedMessageId = convert_uudecode($_POST['message_id']);
	//var_dump(createBoardMessageReply($mysqli, $_SESSION['user_id'], $decodedMessageId, $_POST['body']));
	echo json_encode(getBoardMessageReply($mysqli, createBoardMessageReply($mysqli, $_SESSION['user_id'], $decodedMessageId, $_POST['body'])));
} else {
	echo "Missing POST Variables";
}
