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

  public function test()
  {
    // echo "home/index and here is the name you passed: " . $name;
    // var_dump($_POST);
    $postdata = file_get_contents("php://input");
    $request = json_decode($postdata);
    // var_dump($request);
    // echo "home/test";
  }

  public function addPost() {
    $response['valid'] = false;
    $response['succeeded'] = false;

    $postdata = file_get_contents("php://input");
    $request = json_decode($postdata);

    $post = [
      'caption' => $caption = $request->caption,
      'image_path' => null
    ];

    $v = new Validator();

    $v->required('caption')->lengthBetween(1, 1000);
    // $v->optional('image_path')->lengthBetween($min, $max)->alpha();
    $result = $v->validate($post);

    $response['succeeded'] = $result->isValid();

    if ($response['valid'] = $result->isValid()) {
      addPost($_SESSION['user_id'], $post);
    } else {
      print_r($result->getFailures());
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

  public function getUserInfo() {
    $response['user'] = array();
    $response['signed'] = false;

    if (isset($_SESSION["user_id"]) && strlen(trim($_SESSION["user_id"]))>0) {
      $response['signed'] = true;
      $response['user'] = getUserInfo($_SESSION["user_id"]);
    }
    echo json_encode($response);
  }

  public function login() {
    $response['succeeded'] = false;
    $response['logined'] = false;
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
        // echo $_SESSION['user_id'];
        if ($_SESSION['user_id']) {
          $response['logined'] = true;
          setcookie("user_id", $_SESSION['user_id'], time() + (86400 * 30), '/');
        //   // echo $_SESSION['user_id'];
        }
      }
    }
    else {
      // print_r($result->getFailures());
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
      // print_r($result->getFailures());
      $response['errors'] = $result->getFailures();
    }
    echo json_encode($response);
  }

}
