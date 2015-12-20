<?php

  session_start();

  $_SESSION["user_id"] = NULL;

  header("Location: index.html");
  die();

?>
