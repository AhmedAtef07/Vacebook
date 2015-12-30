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

function addFriend($user1Id, $user2Id, $relation, $requester_id) {
  $query = conn()->prepare("INSERT INTO friends (user1_id, user2_id, relation, requester_id)
        VALUES (?, ?, ?, ?)");
  $query->bind_param('iisi',
      min($user1Id, $user2Id),
      max($user1Id, $user2Id),
      $relation,
      $requester_id);

  $query->execute();
  $query_errors = count($query->error_list);
  $query->close();

  if ($query_errors == 0) {
    $userId = $requester_id == $user1Id ? $user1Id : $user2Id;
    $user = getUsername($userId);
    $data['action'] = 'sent you a friend request';
    $data['user_id'] = $userId;
    $data['username'] = $user['username'];
    $data['full_name'] = $user['full_name'];
    pusher()->trigger((string)$requester_id == $user1Id ? $user2Id : $user1Id, 'new_friend', $data);
    return true;
  }
  else {
    return false;
  }
}

function addPost($userId, $post) {
  $query = conn()->prepare("INSERT INTO posts (user_id, caption, image_path) VALUES (?, ?, ?)");
  // var_dump($query);
  $query->bind_param('iss',
    $userId,
    $post['caption'],
    $post['image_path']);

  $query->execute();
  $query_insert_id = $query->insert_id;
  $query->close();
  $following = followPost($userId, $query_insert_id);
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
    trigPostFollowers($comment['post_id'], $userId, 'commented');
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
    trigPostFollowers($postId, $userId, 'liked');
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
  if ($userId == $_SESSION["user_id"]) {
    $res = conn()->query("SELECT n.*, u.username FROM
      (notifications n JOIN users u ON user_id=id)
      WHERE follower_id='$userId'
      ORDER BY created_at DESC, n.post_id");
  }
  if ($res)       return convertToArray($res);
  else            return false;
}

function getUserInfo($userId) {
  $res = conn()->query("SELECT * FROM users WHERE id='$userId'");
    $user = convertToArray($res)[0];
    if ($userId == $_SESSION["user_id"]) {
      return $user;
    } else {
      $user1Id = min($userId, $_SESSION["user_id"]);
      $user2Id = max($userId, $_SESSION["user_id"]);
      $res2 = conn()->query("SELECT relation, requester_id FROM friends
        WHERE user1_id='$user1Id' AND user2_id='$user2Id'");
      $relation = convertToArray($res2);
      if (count($relation) > 0) {
        $relation = $relation[0];
        if ($relation['relation'] == 'request') {
          $user['birthdate'] = NULL;
          $user['about_me'] = NULL;
          if ($relation['requester_id'] == $userId) {
            $user['relation'] = 'requested';
          } else {
            $user['relation'] = 'waitingAccept';
          }
        } else if ($relation['relation'] == 'friend') {
          $user['relation'] = 'friend';
        }
      } else {
        $user['relation'] = 'none';
        $user['birthdate'] = NULL;
        $user['about_me'] = NULL;
      }

      return $user;
    }
}

function getUsername($userId) {
  $res = conn()->query("SELECT username, first_name, last_name FROM users WHERE id='$userId'");
    $result = convertToArray($res)[0];
    $user['username'] = $result['username'];
    $user['full_name'] = $result['first_name'] . ' ' . $result['last_name'];
    return $user;
}

function getAllUsers() {
  $res = conn()->query("SELECT * FROM users");
  return convertToArray($res);
}

function getUserFriends($userId) {
  $res = conn()->query("SELECT * FROM
        (SELECT u.* FROM friends JOIN users u ON user2_id=id
              WHERE user1_id='$userId' AND relation='friend') a
        UNION
        (SELECT u.* FROM friends JOIN users u ON user1_id=id
              WHERE user2_id='$userId' AND relation='friend')");
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

function getPendingRequests($userId) {
  $res = conn()->query("SELECT * FROM
        (SELECT u.id, u.username, u.gender, u.profile_pic, u.first_name, u.last_name, u.hometown
         FROM friends JOIN users u ON user2_id=id
              WHERE requester_id='$userId' AND user1_id='$userId' AND relation='request') a
        UNION
        (SELECT u.id, u.username, u.gender, u.profile_pic, u.first_name, u.last_name, u.hometown
         FROM friends JOIN users u ON user1_id=id
              WHERE requester_id='$userId' AND user2_id='$userId' AND relation='request')");
  return convertToArray($res);
}

function getAwaitedRequests($userId) {
  $res = conn()->query("SELECT * FROM
        (SELECT u.id, u.username, u.gender, u.profile_pic, u.first_name, u.last_name, u.hometown
         FROM friends JOIN users u ON user2_id=id
              WHERE requester_id!='$userId' AND user1_id='$userId' AND relation='request') a
        UNION
        (SELECT u.id, u.username, u.gender, u.profile_pic, u.first_name, u.last_name, u.hometown
         FROM friends JOIN users u ON user1_id=id
              WHERE requester_id!='$userId' AND user2_id='$userId' AND relation='request')");
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
  else            return false;
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
    $res = conn()->query("INSERT INTO friends (user1_id, user2_id, relation, requester_id)
        VALUES ('$user1Id', '$user2Id', 'friend', '$userId')");
    if ($res) {
      $user = getUsername($_SESSION["user_id"]);
      $data['action'] = 'accepted your friend request';
      $data['user_id'] = $_SESSION["user_id"];
      $data['username'] = $user['username'];
      $data['full_name'] = $user['full_name'];
      pusher()->trigger((string)$userId, 'new_friend', $data);
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
  $query = conn()->prepare("UPDATE notifications SET is_seen=1
    WHERE  follower_id=? AND post_id=?");
  if (true || $userId == $_SESSION["user_id"]) {
    $query->bind_param('ii',
        $userId,
        $postId);
    $query->execute();
    $query_errors = count($query->error_list);
    $query->close();
  }

  if ($query_errors == 0)    return true;
  else                       return false;
}

function trigPostFollowers($postId, $userId, $action_type = '') {
  $res = conn()->query("SELECT follower_id FROM following
    WHERE post_id='$postId' AND follower_id!='$userId'");
  $followers = convertToArray($res);
  $user = getUsername($userId);
  if ($action_type) {
    $action_id = $action_id[0]['id'];
    $data['action'] = $action_type;
    $data['user_id'] = $userId;
    $data['post_id'] = $postId;
    $data['username'] = $user['username'];
    $data['full_name'] = $user['full_name'];
    $query = conn()->prepare("DELETE FROM notifications
        WHERE post_id=? AND user_id=? AND action_type=?");
    $query->bind_param('iii',
        $postId,
        $userId,
        $action_type);
    $query->execute();
    $query->close();

    foreach ($followers as $ind => $follower) {
      $query = conn()->prepare("INSERT INTO notifications (follower_id, post_id, user_id, action_type) VALUES (?, ?, ?, ?)");
      $query->bind_param('iiis',
          $follower['follower_id'],
          $postId,
          $userId,
          $action_type);
      $query->execute();
      $query_errors = count($query->error_list);
      $query->close();
      pusher()->trigger((string)$follower['follower_id'], 'new_notification', $data);
    }
  }
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
                        WHERE f1.relation = 'friends'
                        AND f1.user1_id = f2.user2_id
                        AND f2.user1_id ='$my_id'
                        AND f2.relation ='friends')
                        ");
  return convertToArray($res);
}

function newsFeed($my_id) {
  $res = conn()->query("SELECT * FROM posts WHERE user_id = '$my_id'
                        UNION
                        SELECT * FROM posts WHERE user_id IN (SELECT user2_id FROM friends
                                                              WHERE user1_id ='$my_id' AND relation ='friends')
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
