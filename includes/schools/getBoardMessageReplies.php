<?php

include_once '../functions.php';
include_once '../db_connect.php';

if (isset($_POST["message_id"])) {
	echo json_encode(getBoardMessageReplies($mysqli, convert_uudecode($_POST["message_id"])));
} else {
	echo json_encode("Missing POST Variables");
}
