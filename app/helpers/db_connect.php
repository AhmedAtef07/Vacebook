<?php

$server_name     = "localhost";
$server_username = "root";
$server_password = "";
$server_dbname   = "vacebook";

$conn = new mysqli($server_name, $server_username, $server_password);

if (mysqli_connect_errno()) {
  die("Connect failed: " . mysqli_connect_error());
}

$conn->query("CREATE DATABASE IF NOT EXISTS $server_dbname");

$conn->query("USE $server_dbname");

$conn->query("CREATE TABLE IF NOT EXISTS users (
  id              INT              AUTO_INCREMENT,
  username        VARCHAR(50)      NOT NULL UNIQUE,
  first_name      VARCHAR(50)      NOT NULL,
  last_name       VARCHAR(50)      NOT NULL,
  gender          VARCHAR(10)      NOT NULL,
  birthdate       DATE             NOT NULL,
  email           VARCHAR(50)      NOT NULL UNIQUE,
  password        VARCHAR(50)      NOT NULL,
  phone_number    VARCHAR(20),
  hometown        VARCHAR(50),
  marital_status  VARCHAR(10),
  about_me        TEXT,
  profile_pic     VARCHAR(300),
  created_at      TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY(id)
  )");

$conn->query("CREATE TABLE IF NOT EXISTS posts (
  id              INT              AUTO_INCREMENT,
  user_id         INT              NOT NULL,
  caption         TEXT             NOT NULL,
  image_path      VARCHAR(300)             ,
  created_at      TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY(id),
  FOREIGN KEY(user_id)       REFERENCES users(id)
  )");

$conn->query("CREATE TABLE IF NOT EXISTS friends (
  user1_id        INT              NOT NULL,
  user2_id        INT              NOT NULL,
  state           VARCHAR(20)      NOT NULL,
  requester_id    INT              NOT NULL,
  created_at      TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY(user1_id, user2_id),
  FOREIGN KEY(user1_id)      REFERENCES users(id),
  FOREIGN KEY(user2_id)      REFERENCES users(id),
  FOREIGN KEY(requester_id)  REFERENCES users(id)
  )");

$conn->query("CREATE TABLE IF NOT EXISTS comments (
  id              INT              AUTO_INCREMENT,
  post_id         INT              NOT NULL,
  user_id         INT              NOT NULL,
  caption         TEXT             NOT NULL,
  created_at      TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY(id),
  FOREIGN KEY(post_id)       REFERENCES posts(id),
  FOREIGN KEY(user_id)       REFERENCES users(id)
  )");

$conn->query("CREATE TABLE IF NOT EXISTS likes (
  post_id         INT              NOT NULL,
  user_id         INT              NOT NULL,
  created_at      TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (post_id, user_id),
  FOREIGN KEY(post_id)       REFERENCES posts(id),
  FOREIGN KEY(user_id)       REFERENCES users(id)
  )");

$conn->query("CREATE TABLE IF NOT EXISTS following (
  follower_id     INT              NOT NULL,
  post_id         INT              NOT NULL,
  last_seen       TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  created_at      TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY(follower_id, post_id),
  FOREIGN KEY(follower_id)   REFERENCES users(id),
  FOREIGN KEY(post_id)       REFERENCES posts(id)
  )");


function conn() {
  global $conn;
  return $conn;
}
session_start();
