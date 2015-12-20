<?php

  require 'db_controller.php';

  $response['posts'] = array();
  $response['signed'] = false;
  $response['user_id'] = 0;
  // session_start();

  if (isset($_SESSION["user_id"]) && strlen(trim($_SESSION["user_id"]))>0) {
    $user_id = $_SESSION["user_id"];
    $response['signed'] = true;
    $response['user_id'] = $user_id;
    $response['posts'] = getUserPosts($user_id);
    // $response['posts'] = getAllPosts();
  } else {
    header("Location: index.html");
    die();
  }

  echo json_encode($response);

?>
