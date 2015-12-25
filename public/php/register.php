<?php

  require '../../vendor/autoload.php';
  use \Particle\Validator\Validator;
  
  require 'db_controller.php';

  $response['succeeded'] = false;
  $response['registered'] = false;
  $response['messages'] = array();
  $response['errors'] = array();
  // session_start();

  $user = [
    'first_name' => trim($_POST['first_name']),
    'last_name' => trim($_POST['last_name']),
    'username' => trim($_POST['username']),
    'email' => trim($_POST['email']),
    'password' => $_POST['password'],
    'gender' => trim($_POST['gender']),
    'birthdate' => trim($_POST['birthdate']),
    'phone_number' => trim($_POST['phone_number']),
    'profile_picture' => trim($_POST['profile_picture_path']),
    'hometown' => trim($_POST['hometown']),
    'maritalStatus' => trim($_POST['maritalStatus']),
    'about' => trim($_POST['about'])
  ];

  $v = new Validator();

  $min = 3;
  $max = 50;
  $username_regex = '/^[\w.-]*$/';
  $genders = array("Male", "Female");
  $marital_status = array("Single", "Engaged", "Married");

  $v->required('first_name')->lengthBetween($min, $max)->alpha();
  $v->required('last_name')->lengthBetween($min, $max)->alpha();
  $v->required('username')->lengthBetween($min, $max)->regex($username_regex);
  $v->required('email')->email();
  $v->required('password');
  $v->required('gender')->inArray($genders);
  $v->required('birthdate')->datetime('Y-m-d');
  $v->optional('phone_number')->lengthBetween(9, 20)->digits();
  $v->optional('profile_picture');
  $v->optional('hometown')->lengthBetween($min, $max);
  $v->optional('maritalStatus')->inArray($marital_status);
  $v->optional('about');
  $result = $v->validate($user);

  $response['succeeded'] = $result->isValid();
  if ($result->isValid()) {
    $_SESSION['user_id'] = tryCreateUser($user);
    if ($_SESSION['user_id']) {
      $response['registered'] = true;
      // echo $_SESSION['user_id'];
    }
  } else {
    // print_r($result->getFailures());
    $response['errors'] = $result->getFailures();
  }

  echo json_encode($response);

?>
