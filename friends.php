<?php

  require 'db_controller.php';

  $users = array();
  // session_start();

  if (isset($_SESSION["user_id"]) && strlen(trim($_SESSION["user_id"]))>0) {
    $user_id = $_SESSION["user_id"];
    $users = getAllUsers();
  } else {
    header("Location: LoginForm.php");
    die();
  }

  json_encode($users);

?>
