<?php

  require 'db_controller.php';

  $response['friends'] = array();
  $response['signed'] = false;
  $response['user_id'] = 0;
  // session_start();

  if (isset($_SESSION["user_id"]) && strlen(trim($_SESSION["user_id"]))>0) {
    $response['signed'] = true;
    $response['user_id'] = $_SESSION["user_id"];
    $response['friends'] = getAllUsers();
  } else {
    header("Location: index.html");
    die();
  }

  echo json_encode($response);

?>
