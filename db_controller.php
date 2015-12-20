<?php
  require 'db_connect.php';

  function convertToArray(&$response) {
    $queryArray = array();
    while ($row = $response->fetch_assoc()) {
      array_push($queryArray, $row);
    }
    return $queryArray;
  }

  function getAllUsers() {
    $res = conn()->query("SELECT * FROM users");
    return convertToArray($res);
  }

  function getAllPosts() {
    $res = conn()->query("SELECT * FROM posts");
    return convertToArray($res);
  }

  function getUserPosts($userId) {
    $res = conn()->query("SELECT * FROM posts WHERE user_id='$userId'");
    return convertToArray($res);
  }

  /*
   * Returns user_id if created, or false in case of an error.
   */
  function isUserExists($username, $password) {
    $password = sha1($password);
    $res = conn()->query("SELECT * FROM users WHERE username='$username' AND password='$password'");

    if($res->num_rows == 1) return $res->fetch_assoc()['id'];
    else                    return false;
  }

  /*
   * Returns user_id if created, or false in case of an error.
   */
  function tryCreateUser($user) {
    $query = conn()->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $query->bind_param('ss', $user['username'], sha1($user['password']));
    $query->execute();
    $query_insert_id = $query->insert_id;
    $query->close();

    if ($query_insert_id != 0) return $query_insert_id;
    else                       return false;
  }

 ?>
