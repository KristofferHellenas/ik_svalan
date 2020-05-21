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
  <title>Document</title>
</head>
<body>
  <form action="" method="post">
  <label for="username">Användarnamn:</label>
  <input type="text" name="username">
  <label for="password">Lösenord</label>
  <input type="password" name="password">
  <input type="submit" name="submitlogin" value="Logga in">
  </form>
</body>
</html>
