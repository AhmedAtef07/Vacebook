<?php

use Particle\Validator\Validator;


class Search extends Controller
{

  public function index($name = '')
  {
    $this->view('index.html');
  }


  public function searchByText($text) {
    $response['hints'] = array();
    $response['signed'] = false;

    if (isset($_SESSION["user_id"]) && strlen(trim($_SESSION["user_id"]))>0) {
      $user_id = $_SESSION["user_id"];
      $response['signed'] = true;
      $response['hints'] = searchByPartOfName($text);
      $Email = searchByEmail($text);
      $Phone = searchByPhone($text);
      $Hometown = searchByHometown($text);
      $Caption = searchByCaption($text);
      foreach ($Email as $ind => $result) {
        array_push($response['hints'], $result);
      }
      foreach ($Phone as $ind => $result) {
        array_push($response['hints'], $result);
      }
      foreach ($Hometown as $ind => $result) {
        array_push($response['hints'], $result);
      }
      foreach ($Caption as $ind => $result) {
        array_push($response['hints'], $result);
      }

    }
    echo json_encode($response);
  }

}
