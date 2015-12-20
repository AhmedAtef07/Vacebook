<?php
  $server_name     = "localhost";
  $server_username = "root";
  $server_password = "wordpass";
  $server_dbname   = "vacebook";

  $conn = new mysqli($server_name, $server_username, $server_password);

  if (mysqli_connect_errno()) {
    die("Connect failed: " . mysqli_connect_error());
  }

  $conn->query("CREATE DATABASE IF NOT EXISTS $server_dbname");

  $conn->query("USE $server_dbname");

  $conn->query("CREATE TABLE IF NOT EXISTS users (
    id         INT                NOT NULL AUTO_INCREMENT,
    username   VARCHAR(70) UNIQUE NOT NULL,
    password   VARCHAR(50)        NOT NULL,
    created_at TIMESTAMP          NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY(id)
    )
  ");

  $conn->query("CREATE TABLE IF NOT EXISTS posts (
    id         INT              NOT NULL AUTO_INCREMENT,
    user_id    INT              NOT NULL,
    body       TEXT             NOT NULL,
    created_at TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY(id),
    FOREIGN KEY(user_id) REFERENCES users(id)
    )
  ");

  function conn() {
    global $conn;
    return $conn;
  }
  session_start();
?>
