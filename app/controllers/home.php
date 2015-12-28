<?php

require '../vendor/autoload.php';
use Particle\Validator\Validator;

// require_once '../app/helpers/db_controller.php';

class Home extends Controller
{

  public function index($name = '')
  {
    $this->view('index.html');
  }


  public function addNewFriend() {
    $response['signed'] = false;
    $response['valid'] = false;
    $response['succeeded'] = false;

    $postdata = file_get_contents("php://input");
    $request = json_decode($postdata);

    $user = [
      'requester_id' => $requester_id = $request->requester_id,
      'user_id' => $user_id = $request->user_id
    ];

    $v = new Validator();

    $v->required('requester_id')->digits();
    $v->required('user_id')->digits();
    $result = $v->validate($user);
    $response['valid'] = $result->isValid();

    if (isset($_SESSION["user_id"]) && strlen(trim($_SESSION["user_id"]))>0) {
      $response['signed'] = true;
      if ($response['valid'] && $user['requester_id']==$_SESSION["user_id"]) {
        $response['succeeded'] = addFriend($user['requester_id'], $user['user_id'],
          'request', $user['requester_id']);
      } else {
        print_r($result->getFailures());
      }
    }

    echo json_encode($response);
  }


  public function addNewPost() {
    $response['signed'] = false;
    $response['valid'] = false;
    $response['succeeded'] = false;

    $postdata = file_get_contents("php://input");
    $request = json_decode($postdata);

    $post = [
      'caption' => $caption = $request->caption,
      'image_path' => ""
    ];

    $v = new Validator();

    $v->required('caption')->lengthBetween(1, 1000);
    // $v->optional('image_path')->lengthBetween($min, $max)->alpha();
    $result = $v->validate($post);
    $response['valid'] = $result->isValid();

    if (isset($_SESSION["user_id"]) && strlen(trim($_SESSION["user_id"]))>0) {
      $response['signed'] = true;
      if ($response['valid']) {
        addPost($_SESSION['user_id'], $post);
        $response['succeeded'] = true;
      } else {
        print_r($result->getFailures());
      }
    }

    echo json_encode($response);
  }


  public function addNewComment() {
    $response['signed'] = false;
    $response['valid'] = false;
    $response['succeeded'] = false;
    $response['comment'] = array();

    $postdata = file_get_contents("php://input");
    $request = json_decode($postdata);

    $comment = [
      'post_id' => $comment_id = $request->post_id,
      'caption' => $caption = $request->caption
    ];

    $v = new Validator();

    $v->required('post_id')->digits();
    $v->required('caption')->lengthBetween(1, 1000);
    $result = $v->validate($comment);
    $response['valid'] = $result->isValid();

    if (isset($_SESSION["user_id"]) && strlen(trim($_SESSION["user_id"]))>0) {
      $response['signed'] = true;
      if ($response['valid']) {
        $response['comment'] = addComment($_SESSION['user_id'], $comment);
        $response['succeeded'] = true;

        // $pusher = new Pusher('f17087409b6bc1746d6e', '137778da510cdcd4fce3', '163351');
        // // trigger on my_channel' an event called 'my_event' with this payload:
        // $data['comment'] = json_encode($comment);
        // $pusher->trigger('notifications', 'new_notification', $data);
      } else {
        print_r($result->getFailures());
      }
    }

    echo json_encode($response);
  }

  public function addLike($postId = -1) {
    $response['signed'] = false;
    $response['succeeded'] = false;
    $response['like'] = array();

    if (isset($_SESSION["user_id"]) && strlen(trim($_SESSION["user_id"]))>0
        && $postId != -1) {
      $response['signed'] = true;
      $response['like'] = addLike($_SESSION['user_id'], $postId);
      $response['succeeded'] = true;

      // $pusher = new Pusher('f17087409b6bc1746d6e', '137778da510cdcd4fce3', '163351');
      // trigger on my_channel' an event called 'my_event' with this payload:
      // $data['like'] = json_encode($comment);
      // $pusher->trigger('notifications', 'new_notification', $data);
    }

    echo json_encode($response);
  }

  public function deleteComment($commentId = -1) {
    $response['signed'] = false;
    $response['succeeded'] = false;

    if (isset($_SESSION["user_id"]) && strlen(trim($_SESSION["user_id"]))>0
      && $commentId != -1) {
      $response['signed'] = true;
      deleteComment($commentId);
      $response['succeeded'] = true;
    }

    echo json_encode($response);
  }

  public function deleteLike($postId = -1) {
    $response['signed'] = false;
    $response['succeeded'] = false;

    if (isset($_SESSION["user_id"]) && strlen(trim($_SESSION["user_id"]))>0
        && $postId != -1) {
      $response['signed'] = true;
      deleteLike($_SESSION['user_id'], $postId);
      $response['succeeded'] = true;
    }

    echo json_encode($response);
  }

  public function getFriends() {
    $response['friends'] = array();
    $response['signed'] = false;

    if (isset($_SESSION["user_id"]) && strlen(trim($_SESSION["user_id"]))>0) {
      $response['signed'] = true;
      $response['user_id'] = $_SESSION["user_id"];
      $response['friends'] = getAllUsers();
    }
    echo json_encode($response);
  }


  public function getPosts() {
    $response['posts'] = array();
    $response['signed'] = false;
    $response['user_id'] = 0;

    if (isset($_SESSION["user_id"]) && strlen(trim($_SESSION["user_id"]))>0) {
      $user_id = $_SESSION["user_id"];
      $response['signed'] = true;
      $response['posts'] = getAllPostswithComments();
      // $response['posts'] = getUserPosts($user_id);
      // $response['posts'] = getAllPosts();
    }
    echo json_encode($response);
  }

  public function getPost($postId = -1) {
    $response['post'] = array();
    $response['signed'] = false;
    $response['user_id'] = 0;

    if (isset($_SESSION["user_id"]) && strlen(trim($_SESSION["user_id"]))>0 && $postId != -1) {
      $user_id = $_SESSION["user_id"];
      $response['signed'] = true;
      $response['post'] = getPostWithComments($postId);
      // $response['posts'] = getUserPosts($user_id);
      // $response['posts'] = getAllPosts();
    }
    if (count($response['post']) > 0) {
      $response['post'] = $response['post'][0];
    }
    echo json_encode($response);
  }


  public function getUserInfo($userId = -1) {
    // var_dump(getUserInfo($userId));
    $response['user'] = array();
    $response['signed'] = false;

    if(isset($_SESSION["user_id"]) && strlen(trim($_SESSION["user_id"])) > 0) {
      if ($userId == -1) $userId = $_SESSION["user_id"];
      $response['signed'] = true;
      if(isUserIdExists($userId)) {
        $response['user'] = getUserInfo($userId);
      } else {
        $response['errors'] = ["message" => "user_id does not exists."];
      }
    }
    echo json_encode($response);
  }

  public function getNotifications() {
    $response['notifications'] = array();
    $response['signed'] = false;
    $response['succeeded'] = false;

    if(isset($_SESSION["user_id"]) && strlen(trim($_SESSION["user_id"])) > 0) {
      $response['signed'] = true;
      $response['notifications'] = getNotifications($_SESSION["user_id"]);
      $response['succeeded'] = true;
    }
    echo json_encode($response);
  }

  public function updateLastSeen($postId = -1) {
    $response['signed'] = false;
    $response['succeeded'] = false;

    if(isset($_SESSION["user_id"]) && strlen(trim($_SESSION["user_id"])) > 0) {
      $response['signed'] = true;
      seePost($_SESSION["user_id"], $postId);
      $response['succeeded'] = true;
    }
    echo json_encode($response);
  }

  public function acceptFriendRequest($userId = -1) {
    // var_dump(getUserInfo($userId));
    $response['user'] = array();
    $response['signed'] = false;

    if(isset($_SESSION["user_id"]) && strlen(trim($_SESSION["user_id"])) > 0
        && $userId != -1) {
      $response['signed'] = true;
      if(isUserIdExists($userId)) {
        $response['succeeded'] = acceptFriendRequest($userId);
      } else {
        $response['errors'] = ["message" => "user_id does not exists."];
      }
    }
    echo json_encode($response);
  }

  public function removeRelation($userId = -1) {
    // var_dump(getUserInfo($userId));
    $response['user'] = array();
    $response['signed'] = false;

    if(isset($_SESSION["user_id"]) && strlen(trim($_SESSION["user_id"])) > 0
        && $userId != -1) {
      $response['signed'] = true;
      if(isUserIdExists($userId)) {
        $response['succeeded'] = deleteRelation($userId);
      } else {
        $response['errors'] = ["message" => "user_id does not exists."];
      }
    }
    echo json_encode($response);
  }

  public function getUserPostswithComments($userId) {
    $response['posts'] = array();
    $response['signed'] = false;

    if (isset($_SESSION["user_id"]) && strlen(trim($_SESSION["user_id"])) > 0) {
      $response['signed'] = true;
      $response['posts'] = getUserPostswithComments($userId);
    }
    echo json_encode($response);
  }

  public function login() {
    $response['succeeded'] = false;
    $response['signed'] = false;
    $postdata = file_get_contents("php://input");
    $request = json_decode($postdata);

    $user = [
      'email_or_username' => trim($request->email_or_username),
      'password' => $request->password,
    ];
    $response['user'] = $user;

    $v = new Validator();
    $min = 3;
    $max = 50;
    $username_email_regex = '/^[\w.-]*$|\S+@\S+\.\S+/';

    $v->required('email_or_username')->lengthBetween($min, $max)->regex($username_email_regex);
    $v->required('password');
    $result = $v->validate($user);

    if ($result->isValid()) {
      $response['succeeded'] = true;
      if ($response['succeeded']) {
        $_SESSION['user_id'] = isUserExists($user['email_or_username'], $user['password']);
        if ($_SESSION['user_id']) {
          $response['signed'] = true;
          setcookie("user_id", $_SESSION['user_id'], time() + (86400 * 30), '/');
        }
      }
    }
    else {
      print_r($result->getFailures());
      $response['errors'] = $result->getFailures();
      $response['succeeded'] = $result->isValid();
    }
    echo json_encode($response);
  }


  public function register() {
    $response['succeeded'] = false;
    $response['registered'] = false;

    $postdata = file_get_contents("php://input");
    $request = json_decode($postdata);

    $user = [
      'first_name' => trim($request->first_name),
      'last_name' => trim($request->last_name),
      'username' => trim($request->username),
      'email' => trim($request->email),
      'password' => $request->password,
      'gender' => trim($request->gender),
      'birthdate' => trim($request->birthdate),
      'phone_number' => trim($request->phone_number),
      'profile_picture' => trim($request->profile_picture_path),
      'hometown' => trim($request->hometown),
      'maritalStatus' => trim($request->maritalStatus),
      'about' => trim($request->about)
    ];
    $response['user'] = $user;

    $v = new Validator();
    $min = 3;
    $max = 50;
    $max_about = 200;
    $username_regex = '/^[\w.-]*$/';
    $genders = array("Male", "Female");
    $marital_status = array("Single", "Engaged", "Married");

    $v->required('first_name')->lengthBetween($min, $max)->alpha();
    $v->required('last_name')->lengthBetween($min, $max)->alpha();
    $v->required('username')->lengthBetween($min, $max)->regex($username_regex);
    $v->required('email')->email();
    $v->required('password');
    $v->required('gender')->inArray($genders);
    // $v->required('birthdate')->datetime('Y-m-d');
    $v->optional('phone_number')->lengthBetween(9, 20)->digits();
    $v->optional('profile_picture');
    $v->optional('hometown')->lengthBetween($min, $max);
    $v->optional('maritalStatus')->inArray($marital_status);
    $v->optional('about')->lengthBetween($min, $max_about);
    $result = $v->validate($user);

    $response['succeeded'] = $result->isValid();
    if ($result->isValid()) {
      $_SESSION['user_id'] = tryCreateUser($user);
      if ($_SESSION['user_id']) {
        $response['registered'] = true;
        setcookie("user_id", $_SESSION['user_id'], time() + (86400 * 30), '/');
      }
    } else {
      print_r($result->getFailures());
      $response['errors'] = $result->getFailures();
    }
    echo json_encode($response);
  }

  public function addMockData() {
    $response['succeeded'] = false;
    $response['signed'] = false;

    $password = sha1('12345');
    $query = conn()->prepare("INSERT INTO users (username, first_name, last_name, gender, birthdate, email, password, phone_number, hometown, marital_status, about_me)
      VALUES ('ahmedAtef',
        'Ahmed',
        'Atef',
        'Male',
        '1994-05-25',
        'ahmedatef07@gmail.com',
        ?,
        '01111722255',
        'Alexandria, egypt',
        'Single',
        'blatter Natrix uneath recrease mulk trimyristin outland cobblery marshbuck Hondurean overdilute inhaler hyperflexion sparable undefensed inspectorate protocoleopterous conditioned Confervales penning eremitic superaccrue hydrocephalic barbeiro');");
    $query->bind_param('s', $password);
    $query->execute();
    $query_insert_id = $query->insert_id;
    $query->close();
    if ($query_insert_id == 0) {
        echo 'Error';
    }
    $query = conn()->prepare("INSERT INTO users (username, first_name, last_name, gender, birthdate, email, password, phone_number, hometown, marital_status, about_me)
      VALUES ('mahmoudHammam',
        'Mahmoud',
        'Hammam',
        'Male',
        '1995-05-25',
        'mahmoudHammam@gmail.com',
        ?,
        '01111722255',
        'Els3ed, egypt',
        'Single',
        'autotypic neurophysiology sourjack brazenface overterrible Negritize roadless heater galleass endemiological neuroparalysis retentor should Slavophobe stimulator pseudoacademical goatherdess nonopposition coupled sawmilling primatal spokesmanship Fuchsian guruship');");

    $query->bind_param('s', $password);
    $query->execute();
    $query_insert_id = $query->insert_id;
    $query->close();
    if ($query_insert_id == 0) {
        echo 'Error';
    }
    $query = conn()->prepare("INSERT INTO users (username, first_name, last_name, gender, birthdate, email, password, phone_number, hometown, marital_status, about_me)
        VALUES ('ahmedEmad',
          'Ahmed',
          'Emad',
          'Male',
          '1994-11-10',
          'ahmedEmad94@gmail.com',
          ?,
          '01010899181',
          'Alexandria, egypt',
          'Single',
          'blatter Natrix uneath recrease mulk trimyristin outland cobblery marshbuck Hondurean overdilute inhaler hyperflexion sparable undefensed inspectorate protocoleopterous conditioned Confervales penning eremitic superaccrue hydrocephalic barbeiro');");

    $query->bind_param('s', $password);
    $query->execute();
    $query_insert_id = $query->insert_id;
    $query->close();
    if ($query_insert_id == 0) {
        echo 'Error';
    } else {
      $response['succeeded'] = true;
      $response['signed'] = true;
      $_SESSION['user_id'] = $query_insert_id;
    }
    $query = conn()->prepare("INSERT INTO users (username, first_name, last_name, gender, birthdate, email, password, phone_number, hometown, marital_status, about_me)
          VALUES ('ahmedMagdy',
            'Ahmed',
            'Magdy',
            'Male',
            '1993-05-25',
            'ahmedMagdy@gmail.com',
            ?,
            '01212251067',
            'Cairo, egypt',
            'Single',
            'react imperation haptotropic pearceite bycoket chirpiness solarization surfrappe dewclaw hemogenic Coccothraustes Podophthalmia revelry colletic Cladodontidae diactinism peregrin meteorite excurvate uncropped spermophyte Ods dalmatic enchantingly');");
    $query->bind_param('s', $password);
    $query->execute();
    $query_insert_id = $query->insert_id;
    $query->close();
    if ($query_insert_id == 0) {
        echo 'Error';
    }
    $query = conn()->prepare("INSERT INTO users (username, first_name, last_name, gender, birthdate, email, password, hometown, marital_status, about_me)
            VALUES ('Shrein',
                    'Shrein',
                    'Shrein',
                    'Female',
                    '1996-10-20',
                    'Shrein@gmail.com',
                    ?,
                    'Alexandria, egypt',
                    'Single',
                    'fibrinose schillerization doughnut sobralite prison clownery pupoid deutoxide Rhapis acetophenetide medicotopographic northfieldite wickedish unpeace pregnantness stipendiary preconceived remotive wardable nephrocolopexy subordinationism spalpeen underhanded elfland');");

    $query->bind_param('s', $password);
    $query->execute();
    $query_insert_id = $query->insert_id;
    $query->close();
    if ($query_insert_id == 0) {
        echo 'Error';
    }

    $query = conn()->prepare("INSERT INTO posts (user_id, caption)
              VALUES (1,
                      'Post 1 From User 1');");
    $query->execute();
    $query_insert_id = $query->insert_id;
    $query->close();
    if ($query_insert_id == 0) {
        echo 'Error';
    }

    $query = conn()->prepare("INSERT INTO posts (user_id, caption)
                VALUES (2,
                        'Post 1 From User 2');");
    $query->execute();
    $query_insert_id = $query->insert_id;
    $query->close();
    if ($query_insert_id == 0) {
        echo 'Error';
    }
    $query = conn()->prepare("INSERT INTO posts (user_id, caption)
                VALUES (3,
                        'Post From User 3');");
    $query->execute();
    $query_insert_id = $query->insert_id;
    $query->close();
    if ($query_insert_id == 0) {
        echo 'Error';
    }
    $query = conn()->prepare("INSERT INTO posts (user_id, caption)
                VALUES (5,
                        'Post 1 From User 5');");
    $query->execute();
    $query_insert_id = $query->insert_id;
    $query->close();
    if ($query_insert_id == 0) {
        echo 'Error';
    }

    $query = conn()->prepare("INSERT INTO comments (post_id, user_id, caption)
                VALUES (1,
                        2,
                        'user 2 wrote comment 1 on post from user 1');");
    $query->execute();
    $query_insert_id = $query->insert_id;
    $query->close();
    if ($query_insert_id == 0) {
        echo 'Error';
    }
    $query = conn()->prepare("INSERT INTO comments (post_id, user_id, caption)
                VALUES (1,
                        3,
                        'user 3 wrote comment 2 on post from user 1');");
    $query->execute();
    $query_insert_id = $query->insert_id;
    $query->close();
    if ($query_insert_id == 0) {
        echo 'Error';
    }
    $query = conn()->prepare("INSERT INTO comments (post_id, user_id, caption)
                VALUES (1,
                        1,
                        'user 1 wrote comment 3 on post from user 1');");
    $query->execute();
    $query_insert_id = $query->insert_id;
    $query->close();
    if ($query_insert_id == 0) {
        echo 'Error';
    }
    $query = conn()->prepare("INSERT INTO comments (post_id, user_id, caption)
                VALUES (2,
                        2,
                        'user 2 wrote comment 1 on post from user 2');");
    $query->execute();
    $query_insert_id = $query->insert_id;
    $query->close();
    if ($query_insert_id == 0) {
        echo 'Error';
    }
    $query = conn()->prepare("INSERT INTO comments (post_id, user_id, caption)
                VALUES (2,
                        4,
                        'user 4 wrote comment 2 on post from user 2');");
    $query->execute();
    $query_insert_id = $query->insert_id;
    $query->close();
    if ($query_insert_id == 0) {
        echo 'Error';
    }
    $query = conn()->prepare("INSERT INTO comments (post_id, user_id, caption)
                VALUES (3,
                        3,
                        'user 3 wrote comment 1 on post from user 5');");
    $query->execute();
    $query_insert_id = $query->insert_id;
    $query->close();
    if ($query_insert_id == 0) {
        echo 'Error';
    }
    $query = conn()->prepare("INSERT INTO comments (post_id, user_id, caption)
                VALUES (3,
                        4,
                        'user 4 wrote comment 1 on post from user 5');");
    $query->execute();
    $query_insert_id = $query->insert_id;
    $query->close();
    if ($query_insert_id == 0) {
        echo 'Error';
    }
    $query = conn()->prepare("INSERT INTO comments (post_id, user_id, caption)
                VALUES (3,
                        5,
                        'user 5 wrote comment 1 on post from user 5');");
    $query->execute();
    $query_insert_id = $query->insert_id;
    $query->close();
    if ($query_insert_id == 0) {
        echo 'Error';
    }
    echo json_encode($response);
  }
}
