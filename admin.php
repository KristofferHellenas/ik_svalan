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

// Lägga till ny meddlem
if(isset($_POST['submitAddMember'])){

  $first_name = $_POST['first_name'];
  $last_name = $_POST['last_name'];
  $e_mail = $_POST['e_mail'];
  $membership = $_POST['membership'];
  // $team = $_POST['lag'];
  // var_dump($team);

  $_SESSION['first_name'] = $first_name;
  $_SESSION['last_name'] = $last_name;
  $_SESSION['e_mail'] = $e_mail;
  $_SESSION['membership'] = $membership;
  // $_SESSION['lag'] = $team;


  $query = "INSERT INTO medlemmar (first_name, last_name, e_mail, membership) VALUES (?, ?, ?, ?)";
  $statementObj = $dbh->prepare($query);

  $statementObj->bind_param("ssss", $first_name, $last_name, $e_mail, $membership);
  $statementObj->execute();
}


// Lägga till nytt lag/grupp
if(isset($_POST['submitAddTeam'])){

  $new_team = $_POST['new_team'];
  $sport = $_POST['sport'];

  $_SESSION['new_team'] = $new_team;
  $_SESSION['sport'] = $sport;


  if($sport == 'fotboll'){
  $query = "INSERT INTO fotboll (grupp) VALUES (?)";
  $statementObj = $dbh->prepare($query);

  $statementObj->bind_param("s", $new_team);
  $statementObj->execute();
  }
  else if($sport == 'gymnastik'){
  $query = "INSERT INTO gymnastik (grupp) VALUES (?)";
  $statementObj = $dbh->prepare($query);

  $statementObj->bind_param("s", $new_team);
  $statementObj->execute();
  }
  else if($sport == 'skidor'){
  $query = "INSERT INTO skidor (grupp) VALUES (?)";
  $statementObj = $dbh->prepare($query);

  $statementObj->bind_param("s", $new_team);
  $statementObj->execute();
  }
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
  <title>Document</title>
</head>

<body>
<div class="adminContainer">

  <h1 class="headline">IK Svalan</h1>
  <form action="admin.php" method="post">
    <input type="submit" value="Logga ut" name="logout">
  </form>
  <div class="addContainer">
  <div class="addMember">
  <form action="admin.php" method="post" class="addMemberForm">
    <select class="select" name="chosenmember" id="">
      <?php
      foreach ($members as $member) {
        echo '<option value="' . $member['id'] . '">' . $member['first_name'] . ' ' . $member['last_name'] . '</option>';
      }
      ?>
    </select>
    <div class="field">
      <label  class ="label" for="membernewfirstname">Nytt förnamn:</label>
      <div class="control">
        <input class="input" type="text" name="membernewfirstname">
      </div>
    </div>
    <div class="field">
      <label class="label" for="membernewlastname">Nytt efternamn:</label>
      <div class="control">
        <input class="input" type="text" name="membernewlastname">
      </div>
    </div>
    <div class="field">
      <label class="label" for="newemail">Ny Mail:</label>
      <div class="control">
        <input class="input" type="text" name="newemail">
      </div>
    </div>
    <input class="button is-primary" type="submit" value="Ändra medlem" name="changemember">
    <input class="button is-danger" type="submit" value="Ta bort medlem" name="deletemember">
  </form>
  </div>
  </div>
  <!-- <table>
    <tr>
      <th>First name</th>
      <th>Last name</th>
      <th>E-mail</th>
      <th>Membership</th>
    </tr>
    <?php
    // foreach ($members as $member) {
    //   echo '<tr><td>' . $member['first_name'] . '</td><td>' . $member['last_name'] . '</td><td>' . $member['e_mail'] . '</td><td>' . $member['membership'] . '</td></tr>';
    // }
    ?>
  </table> -->

  <div class="addContainer">
  <h2 class="title">Lägg till ny medlem</h2>
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
            <input class="input" type="email" name="e_mail" placeholder="e.g alexsmith@gmail.com">
          </div>
        </div>

        <div class="field">
          <label class="label">Betalat medlemsavgift</label>
          <div class="control">
            <input class="input" type="text" name="membership" placeholder="Ja/Nej ">
          </div>
        </div>

        <!-- <div class="select">
        <label class="label">Lägg till medlem i grupp</label>
          <select name="lag">
            <option>Idrott & grupp</option>
            <option>--------Fotboll---------</option>
            <?php 
              // foreach($fotbollslag as $lag){
              //   echo '<option value="' . $lag['grupp'] . '">' . $lag['grupp'] . '</option>';
              // }
            ?>
            <option>--------Gymnastik------</option>
            <?php 
              // foreach($gymnastiklag as $lag){
              //   echo '<option value="' . $lag['grupp'] . '">' . $lag['grupp'] . '</option>';
              // }
            ?>
            <option>--------Skidor----------</option>
            <?php 
              // foreach($skidlag as $lag){
              //   echo '<option value="' . $lag['grupp'] . '">' . $lag['grupp'] . '</option>';
              // }
            ?>
          </select>
        </div> -->

        <input class="button is-primary" type="submit" name="submitAddMember" value="Lägg till medlem">
      </div>
    </form>

  <h2 class="title">Lägg till ntt lag</h2>
    <form action="admin.php" method="POST" class="addMemberForm">
    <div class="addMember">
      <div class="field">
        <label class="label">Namn på nytt lag</label>
        <div class="control">
          <input class="input" type="text" name="new_team" placeholder="e.g F07">
        </div>
      </div>

      <div class="field">
        <label class="label">Idrott</label>
        <div class="control">
          <input class="input" type="text" name="sport" placeholder="e.g fotboll, gymnastik, skidor">
        </div>
      </div>

        <input class="button is-primary" type="submit" name="submitAddTeam" value="Lägg till lag">
    </div>
    </form>
  </div>

    <h2 class="title">Medlemmar</h2>
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

        <?php foreach($members as $member){ ?>
          
          <tr>
            <td><?php echo htmlspecialchars($member['first_name']); ?></td>
            <td><?php echo htmlspecialchars($member['last_name']); ?></td>
            <td><?php echo htmlspecialchars($member['e_mail']); ?></td>
            <td><?php echo htmlspecialchars($member['membership']); ?></td>
            <td>
              <span class="icon is-small has-text-danger">
                <i class="fas fa-times-circle"></i>
              </span>
            </td>
          </tr>

        <?php } ?>

      </tbody>
    </table>


  <hr>
  <section>
    <div class="container">
    <h1>Fotboll</h1>
    </div>
    <?php echo '<div class="container"><p>Antal: ' . count($fotballmembers) . '</p></div>'; ?>
    <div class="columns">
    <?php
    foreach ($fotballteams as $team) {
      echo '<div class="column">';
      $i = 0;
      foreach ($fotballmembers as $fotballmember) {
        if ($fotballmember['grupp'] == $team['grupp']) {
          $i++;
        }
      }
      echo '<h2>' . $team['grupp'] . '</h2>';
      echo '<p>Antal: ' . $i .  '</p>';
      echo '<table class="table is-striped is-bordered">';
      echo '<thead><tr><th>Förnamn</th><th>Efternamn</th></tr></thead>';

      foreach ($fotballmembers as $fotballmember) {
        if ($fotballmember['grupp'] == $team['grupp']) {
          echo '<tr><td>' . $fotballmember['first_name'] . '</td><td>' . $fotballmember['last_name'] . '</td></tr>';
        }
      }
      echo '</table>';
      echo '</div>';
    }
    ?>
    </div>
  </section>
  <hr>
  <section>
  <div class="container">
    <h1>Gymnastik</h1>
  </div>
  <?php echo '<div class="container"><p>Antal: ' . count($gymmembers) . '</p></div>' ?>
  <div class="columns">
    <?php
    foreach ($gymgroups as $gymgroup) {
      $i = 0;
      echo '<div class="column">';
      foreach ($gymmembers as $gymmember) {
        if ($gymmember['grupp'] == $gymgroup['grupp']) {
          $i++;
        }
      }
      echo '<h2>' . $gymgroup['grupp'] . '</h2>';
      echo '<p>Antal: ' . $i . '</p>';
      echo '<table class="table is-striped is-bordered">';
      echo '<thead><tr><th>Förnamn</th><th>Efternamn</th></tr></thead>';
      foreach ($gymmembers as $gymmember) {
        if ($gymmember['grupp'] == $gymgroup['grupp']) {
          echo '<tr><td>' . $gymmember['first_name'] . '</td><td>' . $gymmember['last_name'] . '</td></tr>';
        }
      }
      echo '</table>';
      echo '</div>';
    }
    ?>
    </div>
  </section>
  <hr>
  <section>
  <div class="container">
    <h1>Skidor</h1>
  </div>
    <?php echo '<div class="container"><p>Antal: ' . count($skimembers) . '</p></div>'; ?>
    <div class="columns">
    <?php
    foreach ($skigroups as $skigroup) {
      echo '<div class="column">';
      $i = 0;
      foreach ($skimembers as $skimember) {
        if ($skimember['grupp'] == $skigroup['grupp']) {
          $i++;
        }
      }
      echo '<h2>' . $skigroup['grupp'] . '</h2>';
      echo '<p>Antal: ' . $i . '</p>';
      echo '<table class="table is-striped is-bordered">';
      echo '<thead><tr><th>Förnamn</th><th>Efternamn</th></tr></thead>';
      foreach ($skimembers as $skimember) {
        if ($skimember['grupp'] == $skigroup['grupp']) {
          echo '<tr><td>' . $skimember['first_name'] . '</td><td>' . $skimember['last_name'] . '</td></tr>';
        }
      }
      echo '</table>';
      echo '</div>';
    }
    ?>
    </div>
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
  </div>
</body>

</html>