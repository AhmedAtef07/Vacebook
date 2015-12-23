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

  function getUserFriends($userId) {
    $res = conn()->query("SELECT * FROM friends WHERE user_id='$userId'");
    return convertToArray($res);
  }

  function searchByEmail($email) {
    $res = conn()->query("SELECT * FROM users WHERE e_mail='$email'");
    return convertToArray($res);
  }

  function searchByName($firstName, $lastName) {
    $res = conn()->query("SELECT * FROM users WHERE first_name='$firstName' AND last_name ='$lastName'");
    return convertToArray($res);
  }

  function searchByPhone($phoneNumber) {
    $res = conn()->query("SELECT * FROM users WHERE phone_number='$phoneNumber'");
    return convertToArray($res);
  }

  function searchByCaption($text) {
    $res = conn()->query("SELECT * FROM users JOIN posts ON users.id = posts.user_id
        WHERE caption LIKE '%'$text'%'");
    return convertToArray($res);
  }

  /*
   * Returns user_id if created, or false in case of an error.
   */
  function isUserExists($username_or_email, $password) {
    $password = sha1($password);
    if (strpos($username_or_email, '@') !== false) {
      $res = conn()->query("SELECT * FROM users WHERE
        email='$username_or_email' AND password='$password'");
    } else {
      $res = conn()->query("SELECT * FROM users WHERE
        username='$username_or_email' AND password='$password'");
    }

    if($res->num_rows == 1) return $res->fetch_assoc()['id'];
    else                    return false;
  }

  /*
   * Returns user_id if created, or false in case of an error.
   */
  function tryCreateUser($user) {
    $query = conn()->prepare("INSERT INTO users
        (username, first_name, last_name, gender, birthdate, email, password, phone_number,
          hometown, marital_status, about_me, profile_pic)
          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $query->bind_param('ssssssssssss',
      $user['username'],
      $user['first_name'],
      $user['last_name'],
      $user['gender'],
      $user['birthdate'],
      $user['email'],
      sha1($user['password']),
      $user['phone_number'],
      $user['hometown'],
      $user['marital_status'],
      $user['about_me'],
      $user['profile_pic']);
    $query->execute();
    $query_insert_id = $query->insert_id;
    $query->close();

    if ($query_insert_id != 0) return $query_insert_id;
    else                       return false;
  }

 ?>
