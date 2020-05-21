<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'index.php';

$user = $_SESSION['user'];
$pw = $_SESSION['pw'];

$dbh = new PDO('mysql:dbname=ik svalan;host=localhost', $user, $pw);

$members = [];

foreach($dbh->query("SELECT first_name FROM medlemmar") as $row){
  $members[] = $row;
}

if(isset($_POST['logout'])){
  session_destroy();
  header('location: index.php');
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
<form action="admin.php" method="post">
<input type="submit" name="logout" value="Logga Ut">
</form>
<br>
<?php
    if(isset($members)){
      foreach($members as $member){
        echo $member[0] . '<br>';
      }
    }
  ?>
</body>
</html>