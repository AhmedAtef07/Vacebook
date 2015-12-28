<?php

require '../app/helpers/db_connect.php';

function convertToArray(&$response) {
  $queryArray = array();
  while ($row = $response->fetch_assoc()) {
    array_push($queryArray, $row);
  }
  return $queryArray;
}


////////////////////////////////////////////////////////////////////////////
///////////////////////////////// Adds ///////////////////////////////
////////////////////////////////////////////////////////////////////////////

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

function addComment($userId, $comment) {
  $query = conn()->prepare("INSERT INTO comments (user_id, post_id, caption) VALUES (?, ?, ?)");
  $query->bind_param('iis',
    $userId,
    $comment['post_id'],
    $comment['caption']);

  $query->execute();
  $query_errors = count($query->error_list);
  $query_insert_id = $query->insert_id;
  $query->close();
  $res = conn()->query("SELECT c.*, u.username
      FROM comments c JOIN users u ON c.user_id=u.id WHERE c.id='$query_insert_id'");
  if ($query_errors == 0) {
    $following = followPost($userId, $comment['post_id']);
    return convertToArray($res)[0];
  }
  return false;
}

function addLike($userId, $postId) {
  $query = conn()->prepare("INSERT INTO likes (user_id, post_id) VALUES (?, ?)");
  $query->bind_param('ii',
    $userId,
    $postId);

  $query->execute();
  $query_errors = count($query->error_list);
  $query->close();
  $res = conn()->query("SELECT * FROM likes
          WHERE post_id='$postId' AND user_id='$userId'");
  if ($query_errors == 0) {
    $following = followPost($userId, $postId);
    return convertToArray($res)[0];
  }
  return false;
}


////////////////////////////////////////////////////////////////////////////
///////////////////////////////// Deletes ///////////////////////////////
////////////////////////////////////////////////////////////////////////////

function deleteComment($commentId) {
  $res = conn()->query("DELETE FROM comments WHERE id='$commentId'");
}

function deleteLike($userId, $postId) {
  $res = conn()->query("DELETE FROM likes
          WHERE user_id='$userId' AND post_id='$postId'");
}

function deleteRelation($userId) {
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


////////////////////////////////////////////////////////////////////////////
///////////////////////////////// Gets ///////////////////////////////
////////////////////////////////////////////////////////////////////////////

function getNotifications($userId) {
  if (true || $userId == $_SESSION["user_id"]) {
    $res = conn()->query("SELECT u.*, us.username FROM
      ((SELECT follower_id, post_id, last_seen FROM following) n JOIN
      ((SELECT user_id, post_id, created_at, 'commented' as state  FROM comments c
        WHERE created_at > all
        (SELECT last_seen FROM following f WHERE f.post_id=c.post_id AND follower_id='$userId'))
      UNION
      (SELECT user_id, post_id, created_at, 'liked' AS state  FROM likes l
        WHERE created_at > all
        (SELECT last_seen FROM following f WHERE f.post_id=l.post_id AND follower_id='$userId')))
        AS u ON n.post_id=u.post_id) JOIN users us ON u.user_id=us.id
      WHERE follower_id='$userId' AND follower_id!=user_id
      ORDER BY u.created_at DESC, state ASC, u.post_id");
  }
  if ($res)       return convertToArray($res);
  else            return false;
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
      // error_reporting(0);
      // print_r(convertToArray($res2));
      $relation = convertToArray($res2);
      if (count($relation) > 0) {
        $relation = $relation[0];
        if ($relation['state'] == 'request') {
          if ($relation['requester_id'] == $userId) {
            $user['state'] = 'requested';
          } else {
            $user['state'] = 'waitingAccept';
          }
        } else if ($relation['state'] == 'friend') {
          $user['state'] = 'friend';
        }
      } else {
        $user['state'] = 'none';
      }

      return $user;
    }
}

function getAllUsers() {
  $res = conn()->query("SELECT * FROM users");
  return convertToArray($res);
}

function getAllPostswithComments() {
  $res = conn()->query("SELECT posts.*, users.username FROM posts INNER JOIN users ON (users.id = posts.user_id)");
  $posts = convertToArray($res);
  foreach ($posts as $ind => $post) {
    $posts[$ind]['comments'] = getPostComments($post['id']);
    $posts[$ind]['liked'] = isLiked($_SESSION["user_id"], $post['id']);
    $posts[$ind]['likes'] = getPostLikes($post['id']);
  }
  return $posts;
}

function getUserPostswithComments($userId) {
  $res = conn()->query(
    "SELECT posts.*, users.username
     FROM posts INNER JOIN users ON (users.id = posts.user_id)
     HAVING user_id ='$userId'
    ");
  $posts = convertToArray($res);
  foreach ($posts as $ind => $post) {
    $posts[$ind]['comments'] = getPostComments($post['id']);
    $posts[$ind]['liked'] = isLiked($_SESSION["user_id"], $post['id']);
    $posts[$ind]['likes'] = getPostLikes($post['id']);
  }
  return $posts;
}

function getPostWithComments($postId) {
  $res = conn()->query("SELECT p.*, users.username FROM (SELECT * FROM posts WHERE id='$postId') p
    INNER JOIN users ON (users.id = p.user_id);");
  $posts = convertToArray($res);
  foreach ($posts as $ind => $post) {
    $posts[$ind]['comments'] = getPostComments($post['id']);
    $posts[$ind]['liked'] = isLiked($_SESSION["user_id"], $post['id']);
    $posts[$ind]['likes'] = getPostLikes($post['id']);
  }
  return $posts;
}

function getPostComments($postId) {
  $res = conn()->query("SELECT c.*, u.username
      FROM comments c JOIN users u ON c.user_id=u.id WHERE post_id='$postId'");
  return convertToArray($res);
}

function getPostLikes($postId) {
  $res = conn()->query("SELECT * FROM likes
          WHERE post_id='$postId'");
  return convertToArray($res);
}

function getUserFriends($userId) {
  $res = conn()->query("SELECT * FROM friends WHERE user_id='$userId'");
  return convertToArray($res);
}


////////////////////////////////////////////////////////////////////////////
///////////////////////////////// Checks ///////////////////////////////
////////////////////////////////////////////////////////////////////////////

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

function isUserIdExists($userId) {
  $res = conn()->query("SELECT id FROM users WHERE id = '$userId'");

  $rows = $res->num_rows;

  if($rows == 1) return true;
  else           return false;
}

function isLiked($userId, $postId) {
  $res = conn()->query("SELECT * FROM likes
          WHERE user_id='$userId' AND post_id='$postId'");
  $rows = $res->num_rows;
  if($rows == 1)     return true;
  else               return false;
}

function isFollowing($userId, $postId) {
  $res = conn()->query("SELECT * FROM following
          WHERE follower_id='$userId' AND post_id='$postId'");
  $rows = $res->num_rows;
  if($rows == 1)     return true;
  else               return false;
}


////////////////////////////////////////////////////////////////////////////
///////////////////////////////// Actions ///////////////////////////////
////////////////////////////////////////////////////////////////////////////

function acceptFriendRequest($userId) {
  $user1Id = min($userId, $_SESSION["user_id"]);
  $user2Id = max($userId, $_SESSION["user_id"]);

  if (deleteRelation($userId)) {
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

function followPost($userId, $postId) {
  if (!isFollowing($userId, $postId)) {
    $query = conn()->prepare("INSERT INTO following (follower_id, post_id)
          VALUES (?, ?)");
    if (true || $userId == $_SESSION["user_id"]) {
      $query->bind_param('ii',
          $userId,
          $postId);
    }
    $query->execute();
    $query_errors = count($query->error_list);
    $query->close();

    if ($query_errors == 0)    return true;
    else                       return false;
  }
  return true;
}

function seePost($userId, $postId) {
  $query = conn()->prepare("UPDATE following SET last_seen=CURRENT_TIMESTAMP
    WHERE  follower_id=? AND post_id=?");
  if (true || $userId == $_SESSION["user_id"]) {
    $query->bind_param('ii',
        $userId,
        $postId);
  }
  $query->execute();
  $query_errors = count($query->error_list);
  $query->close();

  if ($query_errors == 0)    return true;
  else                       return false;
}


////////////////////////////////////////////////////////////////////////////
///////////////////////////////// Searches ///////////////////////////////
////////////////////////////////////////////////////////////////////////////


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
