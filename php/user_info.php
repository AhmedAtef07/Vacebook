<?php

  require 'db_controller.php';

  $response['user'] = array();
  $response['signed'] = false;
  // session_start();

  if (isset($_SESSION["user_id"]) && strlen(trim($_SESSION["user_id"]))>0) {
    $response['signed'] = true;
    $response['user_id'] = $_SESSION["user_id"];
    if (isset($_SESSION["user_id"]) && strlen(trim($_SESSION["user_id"]))>0) {
      $response['user'] = getUserInfo($_SESSION["user_id"]);
    }
  }
  // } else {
    // header("Location: index.html");
    // die();
  // }

  echo json_encode($response);

  // username
  // first_name
  // last_name
  // gender
  // birthdate
  // email
  // phone_number
  // hometown
  // marital_status
  // about_me
  // profile_pic
  // created_at
?>
