<?php

  require 'db_controller.php';

  $response['signed'] = false;
  // session_start();

  if (isset($_SESSION["user_id"]) && strlen(trim($_SESSION["user_id"]))>0) {
    $response['signed'] = true;
  }

  echo json_encode($response);

?>
