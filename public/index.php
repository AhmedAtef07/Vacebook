<?php 
  
require_once '../app/init.php';

$app = new App;




?>












<!DOCTYPE html>
<html lang="en" ng-app="app">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Where there is no body except us.">
    <meta name="author" content="Ahmed Atef">
    <title>Vacebook</title>

    <!-- CSS Includs -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootswatch/3.3.6/flatly/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.css">
    <link rel="stylesheet" href="assets/css/index.css">
    <link rel="stylesheet" href="./assets/css/font.css">
    <link type="text/css" rel="stylesheet" href="./assets/css/font.css">
    <link type="text/css" rel="stylesheet" href="./assets/css/post.css">
  </head>

  <!-- <body ng-app="home" ng-controller="home_controller" onload="runOnce()"> -->
  <body onload="runOnce()">
  <body>
    <div style="padding: 0; width: 100%;">
      <!-- Title -->
      <div class="row" style="background-color: #FAFAFA; color: #2C5D87; margin: 0;">
        <div class="col-xs-12 text-center">
          <h1 class="text-center" style="font-family: Pacifico; margin: 15px 0;">Vacebook</h1>
        </div>
      </div>
      <!-- ./Title -->

      <!-- TopBar -->
      <div class="row" style="background-color: #13141A; height: 100px; margin: 0;" ng-include="'partials/navbar.html'"></div>
      <!-- ./TopBar -->

      <!-- Page -->
      <div class="row" style="background-color: #FFFFFF; margin: 0;">

        <div class="left-col">
          <div ng-include="'partials/about.html'"></div>
        </div>

        <div class="middle-col">
          <div ui-view style="width: 100%;"></div>
        </div>

        <div class="right-col">
          <div ng-include="'partials/suggestions.html'"></div>
        </div>

      </div>
      <!-- ./Page -->

    </div>

    <!-- JS Includs -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <script src="./assets/js/angular.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/angular-ui-router/0.2.15/angular-ui-router.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/angular.js/1.5.0-rc.0/angular-animate.js"></script>
    <script src="./assets/js/index_atef.js"></script>
    <script src="./assets/js/app.js"></script>
  </body>
</html>
