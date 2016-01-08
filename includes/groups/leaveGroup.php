<?php
include_once '../functions.php';
include_once '../db_connect.php';

sec_session_start();

if (isset($_SESSION["user_id"], $_POST["group_id"])) {
  echo leaveGroup($mysqli, $_SESSION["user_id"], $_POST["group_id"]);
} else {
  header('Location: ../../school.php?error=Faulty Post Variables');
}
?>