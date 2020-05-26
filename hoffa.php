<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$user = 'zooJanitor';
$pw = 'Hejsan123';

$dbh = new PDO('mysql:dbname=ik svalan;host=localhost', $user, $pw);

$members = [];

foreach($dbh->query("SELECT * FROM medlemmar") as $row){
  $members[] = $row;
}


// echo '<pre>';
// var_dump($members);
// echo '</pre>';

// Ändra medlem
if(isset($_POST['changemember'])){

  $cMember = $_POST['chosenmember'];

  $newmail = $members[$cMember - 1]['e_mail'];
  $newfirstname = $members[$cMember - 1]['first_name'];
  $newlastname = $members[$cMember - 1]['last_name'];


  if($_POST['membernewfirstname'] != null){
    $newfirstname = $_POST['membernewfirstname'];
  }
  if($_POST['membernewlastname'] != null){
    $newlastname = $_POST['membernewlastname'];
  }
  if($_POST['newemail'] != null ){
    $newmail = $_POST['newemail'];
  }

  $pdoQuery = "UPDATE medlemmar SET first_name = :first_name, last_name = :last_name, e_mail = :e_mail WHERE id = :id";
  $sth = $dbh->prepare($pdoQuery);
  $sth->execute([':first_name' => $newfirstname, ':last_name' => $newlastname, ':e_mail' => $newmail, ':id' => $cMember]);

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
  <form action="hoffa.php" method="post">
    <select name="chosenmember" id="">
    <?php
      foreach($members as $member){
        echo '<option value="' . $member['id'] . '">' . $member['first_name'] . ' ' . $member['last_name'] . '</option>';
      }
    ?>
    </select>
    <label for="membernewfirstname">Nytt förnamn:</label>
    <input type="text" name="membernewfirstname">
    <label for="membernewlastname">Nytt efternamn:</label>
    <input type="text" name="membernewlastname">     
    <label for="newemail">Ny Mail:</label>
    <input type="text" name="newemail">    
    <input type="submit" value="Ändra medlem" name="changemember">
  </form>
  <table>
  <tr>
  <th>First name</th>
  <th>Last name</th>
  <th>E-mail</th>
  </tr>
  <?php
      foreach($members as $member){
        echo '<tr><td>' . $member['first_name'] . '</td><td>' . $member['last_name'] . '</td><td>' . $member['e_mail'] . '</td></tr>';
      }
  ?>
  </table>
</body>
</html>