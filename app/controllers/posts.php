<?php

use Particle\Validator\Validator;


class Posts extends Controller
{

  public function index($name = '')
  {
    $this->view('index.html');
  }


  public function addNewPost() {
    $response['signed'] = false;
    $response['valid'] = false;
    $response['succeeded'] = false;

    $postdata = file_get_contents("php://input");
    $request = json_decode($postdata);

    $post = [
      'caption' => $caption = $request->caption,
      'image_path' => "",
      'is_private' => $request->is_private
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
      seePost($_SESSION["user_id"], $postId);
      // $response['posts'] = getUserPosts($user_id);
      // $response['posts'] = getAllPosts();
    }
    if (count($response['post']) > 0) {
      $response['post'] = $response['post'][0];
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
}
