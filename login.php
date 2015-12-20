<?php

  require __DIR__.'/vendor/autoload.php';
  use Particle\Validator\Validator;
  require 'db_controller.php';

  $response['succeeded'] = false;
  $response['logined'] = false;
  $response['messages'] = array();
  $response['errors'] = array();
  // session_start();

  $user = [
    'email_username' => trim($_POST['email_username']),
    'password' => $_POST['password'],
  ];

  $v = new Validator();

  $min = 3;
  $max = 70;
  $username_regex = '/^[\w.-]*$/';

  $v->required('email_username')->lengthBetween($min, $max)->regex($username_regex);
  // $v->optional('email')->email();
  $v->required('password');
  $result = $v->validate($user);

  if ($result->isValid()) {
    $response['succeeded'] = true;
    if ($response['succeeded']) {
      $_SESSION['user_id'] = isUserExists($user['email_username'], $user['password']);
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
