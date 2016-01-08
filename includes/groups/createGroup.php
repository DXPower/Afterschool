<?php

include_once '../functions.php';
include_once '../db_connect.php';

sec_session_start();

if (isset($_SESSION["user_id"], $_POST["name"], $_POST["school_id"])) {
  $id = createGroup($mysqli, $_SESSION["user_id"], $_POST["name"], $_POST["school_id"]);
  header('Location: ../../group.php?id=' . $id);
} else {
  echo "Missing POST Variables";
  header('Location: ../../school.php?error=Faulty Post Variables');
}
?>