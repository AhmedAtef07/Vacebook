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
    user_id             INT          AUTO_INCREMENT  PRIMARY Key,
    username       VARCHAR(50)  NOT NULL UNIQUE,
    first_name     VARCHAR(50)  NOT NULL,
    last_name      VARCHAR(50)  NOT NULL,
    gender         VARCHAR(1)   NOT NULL,
    b_date         TIMESTAMP    NOT NULL,
    e_mail         VARCHAR(50)  NOT NULL,
    password       VARCHAR(50)  NOT NULL,
    phone_number   VARCHAR(20)  NOT NULL,
    hometown       VARCHAR(50)          ,
    marital_status VARCHAR(10)          ,
    about_me       TEXT                 ,
    profile_pic    BLOB                 ,
    join_date      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP 
    )");

  $conn->query("CREATE TABLE IF NOT EXISTS posts (
    post_id    INT              AUTO_INCREMENT PRIMARY KEY,
    user_id    INT              NOT NULL,
    caption    TEXT             NOT NULL,
    image      BLOB                     ,
    posted_at  TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY(user_id) REFERENCES users(user_id) 
    )");

  $conn->query("CREATE TABLE IF NOT EXISTS friends (
    user_id        INT              ,
    friend_id      INT              ,
    friends_since  TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (user_id,friend_id) ,
    FOREIGN KEY(friend_id) REFERENCES users(user_id), 
    FOREIGN KEY(user_id) REFERENCES users(user_id) 
    )");
  
  $conn->query("CREATE TABLE IF NOT EXISTS comments (
    comment_id    INT         AUTO_INCREMENT PRIMARY KEY ,
    post_id       INT         NOT NULL ,
    user_id       INT         NOT NULL ,
    comment       TEXT        NOT NULL ,
    commented_at  TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY(post_id) REFERENCES posts(post_id), 
    FOREIGN KEY(user_id) REFERENCES users(user_id) 
    )");

  $conn->query("CREATE TABLE IF NOT EXISTS likes (
    post_id       INT         NOT NULL ,
    user_id       INT         NOT NULL ,
    like_at       TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
    
    PRIMARY KEY (post_id,user_id) ,
    FOREIGN KEY(post_id) REFERENCES posts(post_id), 
    FOREIGN KEY(user_id) REFERENCES users(user_id) 
    )");



  function conn() {
    global $conn;
    return $conn;
  }
  session_start();
?>
