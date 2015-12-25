<?php 

require '../vendor/autoload.php';
use Particle\Validator\Validator;


require '../app/helpers/db_controller.php';

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

  public function add()
  {

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
}