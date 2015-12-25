<?php

  require '../../vendor/autoload.php';
  use Particle\Validator\Validator;
  
  require 'db_controller.php';

  $response['succeeded'] = false;
  $response['logined'] = false;
  $response['messages'] = array();
  $response['errors'] = array();
  // session_start();

  $user = [
    'email_or_username' => trim($_POST['email_or_username']),
    'password' => $_POST['password'],
  ];

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
        $response['logined'] = true;
        // echo $_SESSION['user_id'];
      }
    }
  } else {
    // print_r($result->getFailures());
    $response['errors'] = $result->getFailures();
    $response['succeeded'] = $result->isValid();
  }

  echo json_encode($response);

?>
