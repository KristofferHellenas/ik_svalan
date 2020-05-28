<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if(isset($_POST['submitlogin'])){

  $user = $_POST['username'];
  $pw = $_POST['password'];

  $_SESSION['user'] = $user;
  $_SESSION['pw'] = $pw;

  $dbh = new PDO('mysql:dbname=ik svalan;host=localhost', $user, $pw);

  header('location: admin.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.8.2/css/bulma.min.css">
  <script defer src="https://use.fontawesome.com/releases/v5.3.1/js/all.js"></script>
  <link rel="stylesheet" href="style.css">
  <title>IK Svalan</title>
</head>
<body class="startpage">
  <div class="loginContainer">
    <div class="leftSection"></div>
      <div class="rightSection">
        <h1>IK Svalan</h1>
        <p class="infoText">Välkommen till idrottsföreningen IK Svalan!</p>
        <p>Logga in som administratör:</p>

        <form class="loginForm" action="" method="post">
          <input class="input" type="text" name="username" placeholder="Användarnamn">
          <input class="input" type="password" name="password" placeholder="Lösenord">
          <input class="button is-info" type="submit" name="submitlogin" value="Logga in">
        </form>
      </div>
  </div>
</body>
</html>
