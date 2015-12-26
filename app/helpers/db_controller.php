<?php

require '../app/helpers/db_connect.php';

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

function getUserInfo($userId) {
  $res = conn()->query("SELECT id, username, first_name, last_name, gender, birthdate, email,
    phone_number, hometown, marital_status, about_me, profile_pic FROM users WHERE id='$userId'");
    $user = convertToArray($res)[0];
    if ($userId == $_SESSION["user_id"]) {
      return $user;
    } else {
      $user1Id = min($userId, $_SESSION["user_id"]);
      $user2Id = max($userId, $_SESSION["user_id"]);
      $res2 = conn()->query("SELECT state, requester_id FROM friends
        WHERE user1_id='$user1Id' AND user2_id='$user2Id'");
      $relation = convertToArray($res2)[0];
      // print_r($relation);
      if ($relation['state'] == 'request') {
        if ($relation['requester_id'] == $userId) {
          $user['state'] = 'requested';
        } else {
          $user['state'] = 'waitingAccept';
        }
      } else {
        $user['state'] = $relation['state'];
      }
      return $user;
    }
}

function acceptFriendRequest($userId) {
  $user1Id = min($userId, $_SESSION["user_id"]);
  $user2Id = max($userId, $_SESSION["user_id"]);

  if (removeRelation($userId)) {
    $res = conn()->query("INSERT INTO friends (user1_id, user2_id, state, requester_id)
        VALUES ('$user1Id', '$user2Id', 'friend', '$userId')");
    if ($res) {
      return true;
    } else {
      return false;
    }
  } else {
    return false;
  }
}

function removeRelation($userId) {
  $user1Id = min($userId, $_SESSION["user_id"]);
  $user2Id = max($userId, $_SESSION["user_id"]);
  $res = conn()->query("DELETE FROM friends
      WHERE user1_id='$user1Id' AND user2_id='$user2Id'");
  if ($res) {
    return true;
  } else {
    return false;
  }
}

function getAllPosts() {
  $res = conn()->query("SELECT * FROM posts");
  return convertToArray($res);
}

function getAllPostswithComments() {
  $res = conn()->query("SELECT posts.*, users.username FROM posts INNER JOIN users ON (users.id = posts.user_id)");
  $posts = convertToArray($res);
  foreach ($posts as $ind => $post) {
    // print_r($post);
    // echo $post['id'];
    $posts[$ind]['comments'] = getPostComments($post['id']);
    // $post['comments'] = getPostComments($post['id']);
    // foreach ($post as $key => $value) {
    //   echo $key;
    //   echo $value + "\n";
    // }
  }
  // print_r($posts);
  return $posts;
}

function getAllUserPostswithComments($userId) {
  $res = conn()->query(
    "SELECT posts.*, users.username
     FROM posts INNER JOIN users ON (users.id = posts.user_id)
     HAVING user_id ='$userId'
    ");

  $posts = convertToArray($res);
  foreach ($posts as $ind => $post) {
    $posts[$ind]['comments'] = getPostComments($post['id']);
  }
  return $posts;
}

function getPostComments($postId) {
  $res = conn()->query("SELECT * FROM comments WHERE post_id='$postId'");
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
/*using auto-complete*/
function searchByPartOfName($name)
{
  $res = conn()->query("SELECT * FROM users WHERE first_name LIKE '%'$firstName'%' OR last_name LIKE '%'$lastName'%' ");
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

function addPost($userId, $post) {
  $query = conn()->prepare("INSERT INTO posts (user_id, caption, image_path) VALUES (?, ?, ?)");
  // var_dump($query);
  $query->bind_param('iss',
    $userId,
    $post['caption'],
    $post['image_path']);

  $query->execute();
  $query->close();
}

function deleteComment($commentId) {
  $res = conn()->query("DELETE FROM comments WHERE id='$commentId'");
}

function addComment($userId, $comment) {
  $query = conn()->prepare("INSERT INTO comments (user_id, post_id, caption) VALUES (?, ?, ?)");
  $query->bind_param('iis',
    $userId,
    $comment['post_id'],
    $comment['caption']);

  $query->execute();
  // var_dump($query);
  $query->close();
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
  $rows = $res->num_rows;

  if($rows == 1) return $res->fetch_assoc()['id'];
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

function suggestedPeople($my_id) {
  $res = conn()->query("SELECT * FROM users WHERE id
                        IN (SELECT f1.user2_id FROM friends f1,friends f2
                        WHERE f1.state = 'friends'
                        AND f1.user1_id = f2.user2_id
                        AND f2.user1_id ='$my_id'
                        AND f2.state ='friends')
                        ");
  return convertToArray($res);
}

function newsFeed($my_id) {
  $res = conn()->query("SELECT * FROM posts WHERE user_id = '$my_id'
                        UNION
                        SELECT * FROM posts WHERE user_id IN (SELECT user2_id FROM friends
                                                              WHERE user1_id ='$my_id' AND state ='friends')
                        ORDER BY created_at
                      ");
  return convertToArray($res);
}

function commentNotifications($my_id) {
  $res = conn()->query("SELECT c1.id , c1.post_id , c1.user_id , c1.caption , c1.created_at FROM comments c1,comments c2
                        WHERE c1.post_id = c2.post_id
                        AND c2.user_id ='$my_id'
                        AND c1.created_at > c2.created_at

                        UNION

                        SELECT * FROM comments
                        WHERE post_id IN (SELECT id FROM posts WHERE user_id ='$my_id')

                        ORDER BY created_at
                        ");
  return convertToArray($res);
}
function likeNotifications($my_id) {
  $res = conn()->query("SELECT * FROM likes
                        WHERE post_id IN (SELECT id FROM posts WHERE user_id ='$my_id')
                        ORDER BY created_at
                        ");
  return convertToArray($res);
}


function isUserIdExists($userId) {
  $res = conn()->query("SELECT id FROM users WHERE id = '$userId'");

  $rows = $res->num_rows;

  if($rows == 1) return true;
  else           return false;
}

function addFriend($user1Id, $user2Id, $state, $requester_id) {
  $query = conn()->prepare("INSERT INTO friends (user1_id, user2_id, state, requester_id)
        VALUES (?, ?, ?, ?)");
  $query->bind_param('iisi',
      min($user1Id, $user2Id),
      max($user1Id, $user2Id),
      $state,
      $requester_id);

  $query->execute();
  $query_errors = count($query->error_list);
  $query->close();

  if ($query_errors == 0)    return true;
  else                       return false;

}
