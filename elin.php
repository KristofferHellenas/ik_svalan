<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'secret.php';

$connection = new mysqli($dbServer, $dbUserName, $dbPassword, $dbName);

// Om kontakten med databasen inte funkar får vi ett meddelande
if($connection->connect_errno){
  exit("Database Connection Failed. Reason: " . $connection->connect_error);
}

if(isset($_POST['submitAddMember'])){

  $first_name = $_POST['first_name'];
  $last_name = $_POST['last_name'];
  $email = $_POST['email'];
  $membership = $_POST['membership'];


  $_SESSION['first_name'] = $first_name;
  $_SESSION['last_name'] = $last_name;
  $_SESSION['email'] = $email;
  $_SESSION['membership'] = $membership;


  $query = "INSERT INTO medlemmar (first_name, last_name, email) VALUES (?, ?, ?, ?)";
  $statementObj = $connection->prepare($query);

  $statementObj->bind_param("sssb", $first_name, $last_name, $email, $membership);
  $statementObj->execute();

}

// $query = "SELECT name FROM medlemmar";
// $resultObj = $connection->query($query);


// if($resultObj->num_rows > 0){
//   while($singleRowFromQuery = $resultObj->fetch_assoc()){
//     echo "Djur: " . $singleRowFromQuery['name'] .PHP_EOL;
//     echo "</br>";
//   }
// }
// $resultObj->close();

// write query for all
$sql = "SELECT * FROM medlemmar";

// make query and get the result set (set of rows)
$result = mysqli_query($connection, $sql);

// fetch the resulting rows as an array
$medlemmar = mysqli_fetch_all($result, MYSQLI_ASSOC);

// free the $result from memory (good practise)
mysqli_free_result($result);

// close connection
mysqli_close($connection);

// $connection->close();

?>


<!DOCTYPE html>
<html dir="ltr" lang="sv">
  <head>
    <meta charset="utf-8">
    <title>Admin - Labb2</title>
    <meta content="initial-scale=1, width=device-width" name="viewport">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.8.2/css/bulma.min.css">
    <script defer src="https://use.fontawesome.com/releases/v5.3.1/js/all.js"></script>
    <link rel="stylesheet" href="style.css">
  </head>
  <body>
    <div class="adminContainer">

      <h1>IK Svalan</h1>

      <form action="admin.php" method="POST" class="addMemberForm">
      <div class="addMember">
        <div class="field">
          <label class="label">Förnamn</label>
          <div class="control">
            <input class="input" type="text" name="first_name" placeholder="e.g Alex">
          </div>
        </div>

        <div class="field">
          <label class="label">Efternamn</label>
          <div class="control">
            <input class="input" type="text" name="last_name" placeholder="e.g. Smith ">
          </div>
        </div>

        <div class="field">
          <label class="label">E-post</label>
          <div class="control">
            <input class="input" type="email" name="email" placeholder="e.g alexsmith@gmail.com">
          </div>
        </div>

        <input type="checkbox" name="membership">

        <!-- <div class="select">
          <select>
            <option>Idrott & grupp</option>
            <option>Fotboll - F08</option>
            <option>Fotboll - F09</option>
            <option>Fotboll - P08</option>
            <option>Fotboll - P09</option>
            <option>-------------</option>
            <option>Gymnastik - Dam</option>
            <option>Gymnastik - Herr</option>
            <option>Gymnastik - Junior</option>
            <option>-------------</option>
            <option>Skidor - Motion</option>
            <option>Skidor - Elit Dam</option>
            <option>Skidor - Elit Herr</option>
          </select>
        </div> -->

        
          <input class="button is-primary" type="submit" name="submitAddMember" value="Lägg till medlem">
        </form>
      </div>

      <!-- Dropdown -->
      <!-- <div class="select">
        <select>
          <option>Sortera på</option>
          <option>Medlemmar</option>
          <option>Sport</option>
          <option>Lag/Grupp</option>
        </select>
      </div> -->

      <table class="table is-striped is-bordered">
        <thead>
          <tr>
            <th>Förnamn</th>
            <th>Efternan</th>
            <th>E-post</th>
            <th>Medlemsavgift</th>
          </tr>
        </thead>
        <tbody>

          <?php foreach($medlemmar as $medlem){ ?>
            
            <tr>
              <td><?php echo htmlspecialchars($medlem['first_name']); ?></td>
              <td><?php echo htmlspecialchars($medlem['last_name']); ?></td>
              <td><?php echo htmlspecialchars($medlem['e_mail']); ?></td>
              <td><?php echo htmlspecialchars($medlem['membership']); ?></td>
              <td>
                <span class="icon is-small has-text-danger">
                  <i class="fas fa-times-circle"></i>
                </span>
              </td>
            </tr>

          <?php } ?>

        </tbody>
      </table>

    </div>
  </body>
</html>