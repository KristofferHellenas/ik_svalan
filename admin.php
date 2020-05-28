<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'secret.php';

$dbh = new PDO('mysql:dbname=ik svalan;host=localhost', $user, $pw);

$members = [];

// Databas
foreach ($dbh->query("SELECT * FROM medlemmar") as $row) {
  $members[] = $row;
}

// Hämta alla fotbolls lag
$fotballteams = [];
foreach ($dbh->query("SELECT * FROM fotboll") as $row) {
  $fotballteams[] = $row;
}


// Hämta alla fotbollsspelare
$fotballmembers = [];

foreach ($dbh->query("SELECT * FROM `medlemmar`
JOIN fotboll_medlemmar
ON fotboll_medlemmar.medlem_id = medlemmar.id
JOIN fotboll
ON fotboll.id = fotboll_medlemmar.fotboll_id
") as $row) {
  $fotballmembers[] = $row;
}

// Hämta alla gymnastik grupper
$gymgroups = [];
foreach ($dbh->query("SELECT * FROM gymnastik") as $row) {
  $gymgroups[] = $row;
}

// Hämta alla gymnaster
$gymmembers = [];

foreach ($dbh->query("SELECT * FROM `medlemmar`
JOIN gymnastik_medlemmar
ON gymnastik_medlemmar.medlem_id = medlemmar.id
JOIN gymnastik
ON gymnastik.id = gymnastik_medlemmar.gymnastik_id
") as $row) {
  $gymmembers[] = $row;
}

// Hämta alla skidgrupper
$skigroups = [];
foreach ($dbh->query("SELECT * FROM skidor") as $row) {
  $skigroups[] = $row;
}

// Hämta alla skidåkare
$skimembers = [];
foreach ($dbh->query("SELECT * FROM `medlemmar`
JOIN skidor_medlemmar
ON skidor_medlemmar.medlem_id = medlemmar.id
JOIN skidor
ON skidor.id = skidor_medlemmar.skidor_id
") as $row) {
  $skimembers[] = $row;
}


// echo '<pre>';
// var_dump($fotballmembers);
// echo '</pre>';

// Ändra medlem
if (isset($_POST['changemember'])) {

  $cMember = $_POST['chosenmember'];

  $newmail = $members[$cMember - 1]['e_mail'];
  $newfirstname = $members[$cMember - 1]['first_name'];
  $newlastname = $members[$cMember - 1]['last_name'];


  if ($_POST['membernewfirstname'] != null) {
    $newfirstname = $_POST['membernewfirstname'];
  }
  if ($_POST['membernewlastname'] != null) {
    $newlastname = $_POST['membernewlastname'];
  }
  if ($_POST['newemail'] != null) {
    $newmail = $_POST['newemail'];
  }

  $pdoQuery = "UPDATE medlemmar SET first_name = :first_name, last_name = :last_name, e_mail = :e_mail WHERE id = :id";
  $sth = $dbh->prepare($pdoQuery);
  $sth->execute([':first_name' => $newfirstname, ':last_name' => $newlastname, ':e_mail' => $newmail, ':id' => $cMember]);
}

// Ta bort medlem
if (isset($_POST['deletemember'])) {
  $id = $_POST['chosenmember'];

  $pdoQuery = "DELETE FROM medlemmar WHERE id = :id";
  $sth = $dbh->prepare($pdoQuery);
  $sth->execute([':id' => $id]);
}

if (isset($_POST['logout'])) {
  session_destroy();
  header('location: index.php');
}

//Ta bort lag
if (isset($_POST['deleteteam'])) {
  $id = $_POST['chosenteam'];

  $pdoQuery = "DELETE FROM fotboll WHERE id = :id";
  $sth = $dbh->prepare($pdoQuery);
  $sth->execute([':id' => $id]);

  $pdoQuery = "DELETE FROM skidor WHERE id = :id";
  $sth = $dbh->prepare($pdoQuery);
  $sth->execute([':id' => $id]);

  $pdoQuery = "DELETE FROM gymnastik WHERE id = :id";
  $sth = $dbh->prepare($pdoQuery);
  $sth->execute([':id' => $id]);
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
  <h1>IK Svalan</h1>
  <form action="admin.php" method="post">
    <input type="submit" value="Logga ut" name="logout">
  </form>
  <form action="hoffa.php" method="post">
    <select name="chosenmember" id="">
      <?php
      foreach ($members as $member) {
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
    <input type="submit" value="Ta bort medlem" name="deletemember">
  </form>
  <table>
    <tr>
      <th>First name</th>
      <th>Last name</th>
      <th>E-mail</th>
      <th>Membership</th>
    </tr>
    <?php
    foreach ($members as $member) {
      echo '<tr><td>' . $member['first_name'] . '</td><td>' . $member['last_name'] . '</td><td>' . $member['e_mail'] . '</td><td>' . $member['membership'] . ' <input type="submit" name="' . $member['id'] . '" value="Ändra medlemskap"></td></tr>';
    }
    ?>
  </table>
  <hr>
  <section>
    <h1>Fotboll</h1>
    <?php
    echo 'Antal: ' . count($fotballmembers);
    foreach ($fotballteams as $team) {
      $i = 0;
      foreach ($fotballmembers as $fotballmember) {
        if ($fotballmember['grupp'] == $team['grupp']) {
          $i++;
        }
      }
      echo '<table>';
      echo '<h2>' . $team['grupp'] . '</h2>';
      echo '<p>Antal: ' . $i .  '</p>';
      echo '<tr><th>Förnamn</th><th>Efternamn</th></tr>';

      foreach ($fotballmembers as $fotballmember) {
        if ($fotballmember['grupp'] == $team['grupp']) {
          echo '<tr><td>' . $fotballmember['first_name'] . '</td><td>' . $fotballmember['last_name'] . '</td></tr>';
        }
      }
      echo '</table>';
    }
    ?>
  </section>
  <hr>
  <section>
    <h1>Gymnastik</h1>
    <?php
    echo 'Antal: ' . count($gymmembers);
    foreach ($gymgroups as $gymgroup) {
      $i = 0;
      foreach ($gymmembers as $gymmember) {
        if ($gymmember['grupp'] == $gymgroup['grupp']) {
          $i++;
        }
      }
      echo '<table>';
      echo '<h2>' . $gymgroup['grupp'] . '</h2>';
      echo '<p>Antal: ' . $i . '</p>';
      echo '<tr><th>Förnamn</th><th>Efternamn</th></tr>';
      foreach ($gymmembers as $gymmember) {
        if ($gymmember['grupp'] == $gymgroup['grupp']) {
          echo '<tr><td>' . $gymmember['first_name'] . '</td><td>' . $gymmember['last_name'] . '</td></tr>';
        }
      }
      echo '</table>';
    }
    ?>
  </section>
  <hr>
  <section>
    <h1>Skidor</h1>
    <?php
    echo 'Antal: ' . count($skimembers);
    foreach ($skigroups as $skigroup) {
      $i = 0;
      foreach ($skimembers as $skimember) {
        if ($skimember['grupp'] == $skigroup['grupp']) {
          $i++;
        }
      }
      echo '<table>';
      echo '<h2>' . $skigroup['grupp'] . '</h2>';
      echo '<p>Antal: ' . $i . '</p>';
      echo '<tr><th>Förnamn</th><th>Efternamn</th></tr>';
      foreach ($skimembers as $skimember) {
        if ($skimember['grupp'] == $skigroup['grupp']) {
          echo '<tr><td>' . $skimember['first_name'] . '</td><td>' . $skimember['last_name'] . '</td></tr>';
        }
      }
      echo '</table>';
    }
    ?>
  </section>

  <form action="admin.php" method="post">
    <select name="chosenteam" id="">
      <option value="">---Fotboll---</option>
      <?php
      foreach ($fotballteams as $fotballteam) {
        echo '<option value="' . $fotballteam['id'] . '">' . $fotballteam['grupp'] . '</option>';
      }
      ?>
      <option value="">---Skidor---</option>

      <?php
      foreach ($skigroups as $skigroup) {
        echo '<option value="' . $skigroup['id'] . '">' . $skigroup['grupp'] . '</option>';
      }
      ?>
      <option value="">---Gymnastik---</option>
      <?php
      foreach ($gymgroups as $gymgroup) {
        echo '<option value="' . $gymgroup['id'] . '">' . $gymgroup['grupp'] . '</option>';
      }

      ?>
    </select>
    <input type="submit" value="Ta bort lag" name="deleteteam">
  </form>
</body>

</html>