<?php 

class Home extends Controller
{
  
  public function index($name = '')
  {
    echo "home/index and here is the name you passed: " . $name;
  }

  public function test()
  {
    echo "home/test";
  }
}