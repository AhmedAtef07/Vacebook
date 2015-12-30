<?php

use Particle\Validator\Validator;


class Users extends Controller
{

  public function index($name = '')
  {
    $this->view('index.html');
  }


  public function getUserInfo($userId = -1) {
    // var_dump(getUserInfo($userId));
    $response['user'] = array();
    $response['signed'] = false;

    if(isset($_SESSION["user_id"]) && strlen(trim($_SESSION["user_id"])) > 0) {
      if ($userId == -1) $userId = $_SESSION["user_id"];
      if(isUserIdExists($userId)) {
        $response['signed'] = true;
        $response['user'] = getUserInfo($userId);
      } else {
        $response['errors'] = ["message" => "user_id does not exists."];
      }
    }
    echo json_encode($response);
  }

  public function getFriends() {
    $response['friends'] = array();
    $response['signed'] = false;

    if (isset($_SESSION["user_id"]) && strlen(trim($_SESSION["user_id"]))>0) {
      $response['signed'] = true;
      $response['user_id'] = $_SESSION["user_id"];
      $response['friends'] = getUserFriends($_SESSION["user_id"]);
    }
    echo json_encode($response);
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
      'first_name' => strtolower(trim($request->first_name)),
      'last_name' => strtolower(trim($request->last_name)),
      'username' => strtolower(trim($request->username)),
      'email' => strtolower(trim($request->email)),
      'password' => $request->password,
      'gender' => strtolower(trim($request->gender)),
      'birthdate' => trim($request->birthdate),
      'phone_number' => trim($request->phone_number),
      'profile_picture' => trim($request->profile_picture_path),
      'hometown' => strtolower(trim($request->hometown)),
      'maritalStatus' => strtolower(trim($request->maritalStatus)),
      'about' => trim($request->about)
    ];
    $response['user'] = $user;

    $v = new Validator();
    $min = 3;
    $max = 50;
    $max_about = 200;
    $username_regex = '/^[\w.-]*$/';
    $genders = array("male", "female");
    $marital_status = array("single", "engaged", "married");

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

  public function pendingRequestsForCurrentUser() {
    $response['requests'] = array();
    $response['signed'] = false;
    $response['succeeded'] = false;

    if(isset($_SESSION["user_id"]) && strlen(trim($_SESSION["user_id"])) > 0) {
      $response['signed'] = true;
      $response['requests'] = getPendingRequests($_SESSION["user_id"]);
      $response['succeeded'] = true;
    }
    echo json_encode($response);
  }

  public function awaitedRequestsForCurrentUser() {
    $response['requests'] = array();
    $response['signed'] = false;
    $response['succeeded'] = false;

    if(true || isset($_SESSION["user_id"]) && strlen(trim($_SESSION["user_id"])) > 0) {
      $response['signed'] = true;
      $response['requests'] = getAwaitedRequests(1);
      $response['succeeded'] = true;
    }
    echo json_encode($response);
  } 

}
