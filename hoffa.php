<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$user = 'zooJanitor';
$pw = 'Hejsan123';
$dbh = new PDO('mysql:dbname=ik svalan;host=localhost', $user, $pw);

$members = [];

// Databas
foreach($dbh->query("SELECT * FROM medlemmar") as $row){
  $members[] = $row;
}

// Hämta alla fotbolls lag
$fotballteams = [];
foreach($dbh->query("SELECT * FROM fotboll") as $row){
  $fotballteams[] = $row;
}


// Hämta alla fotbollsspelare
$fotballmembers = [];

foreach($dbh->query("SELECT * FROM `medlemmar`
JOIN fotboll_medlemmar
ON fotboll_medlemmar.medlem_id = medlemmar.id
JOIN fotboll
ON fotboll.id = fotboll_medlemmar.fotboll_id
") as $row){
  $fotballmembers[] = $row;
}

// Hämta alla gymnastik grupper
$gymgroups = [];
foreach($dbh->query("SELECT * FROM gymnastik") as $row){
  $gymgroups[] = $row;
}

// Hämta alla gymnaster
$gymmembers = [];

foreach($dbh->query("SELECT * FROM `medlemmar`
JOIN gymnastik_medlemmar
ON gymnastik_medlemmar.medlem_id = medlemmar.id
JOIN gymnastik
ON gymnastik.id = gymnastik_medlemmar.gymnastik_id
") as $row){
  $gymmembers[] = $row;
}

// Hämta alla skidgrupper
$skigroups = [];
foreach($dbh->query("SELECT * FROM skidor") as $row){
  $skigroups[] = $row;
}

// Hämta alla skidåkare
$skimembers = [];
foreach($dbh->query("SELECT * FROM `medlemmar`
JOIN skidor_medlemmar
ON skidor_medlemmar.medlem_id = medlemmar.id
JOIN skidor
ON skidor.id = skidor_medlemmar.skidor_id
") as $row){
  $skimembers[] = $row;
}


// echo '<pre>';
// var_dump($fotballmembers);
// echo '</pre>';

// Ändra medlem
if(isset($_POST['changemember'])){

  $cMember = $_POST['chosenmember'];

  $newmail = $members[$cMember - 1]['e_mail'];
  $newfirstname = $members[$cMember - 1]['first_name'];
  $newlastname = $members[$cMember - 1]['last_name'];
  $membership = $members[$cMember -1]['membership'];

  if($_POST['membernewfirstname'] != null){
    $newfirstname = $_POST['membernewfirstname'];
  }
  if($_POST['membernewlastname'] != null){
    $newlastname = $_POST['membernewlastname'];
  }
  if($_POST['newemail'] != null ){
    $newmail = $_POST['newemail'];
  }
  if($_POST['membership'] != null){
    $membership = $_POST['membership'];
  }

  $pdoQuery = "UPDATE medlemmar SET first_name = :first_name, last_name = :last_name, e_mail = :e_mail, membership = :membership WHERE id = :id";
  $sth = $dbh->prepare($pdoQuery);
  $sth->execute([':first_name' => $newfirstname, ':last_name' => $newlastname, ':e_mail' => $newmail, ':id' => $cMember, ':membership' => $membership]);

}

// Ta bort medlem
if(isset($_POST['deletemember'])){
  $id = $_POST['chosenmember'];

  $pdoQuery = "DELETE FROM medlemmar WHERE id = :id";
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
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.8.2/css/bulma.min.css">
  <script defer src="https://use.fontawesome.com/releases/v5.3.1/js/all.js"></script>
  <link rel="stylesheet" href="style.css">
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
    <select name="membership" id="">
     <option value="0">Medlem</option>
     <option value="1">Inte medlem</option>
    </select>    
    <input type="submit" value="Ändra medlem" name="changemember">
    <input type="submit" value="Ta bort medlem" name="deletemember">
  </form>
  <h1>Alla medlemmar</h1>
  <table>
  <tr>
  <th>First name</th>
  <th>Last name</th>
  <th>E-mail</th>
  <th>Membership</th>
  </tr>
  <?php
      foreach($members as $member){
        echo '<tr><td>' . $member['first_name'] . '</td><td>' . $member['last_name'] . '</td><td>' . $member['e_mail'] . '</td><td>' . $member['membership'] . '</td></tr>';
      }
  ?>
  </table>
  <hr>
  <section>
  <div class="container">
  <h1>Fotboll</h1>
  </div>
  <?php echo '<div class="container"><p>Antal: ' . count($fotballmembers) . '</p></div>'; ?>
  <div class="columns">
  <?php
    foreach($fotballteams as $team){
      echo '<div class ="column">';
      $i = 0;
      foreach($fotballmembers as $fotballmember){
        if($fotballmember['grupp'] == $team['grupp']){
          $i++;
        }
      }
      echo '<h2>' . $team['grupp'] . '</h2>';
      echo '<p>Antal: ' . $i .  '</p>';
      echo '<table>';
      echo '<tr><th>Förnamn</th><th>Efternamn</th></tr>';

        foreach($fotballmembers as $fotballmember){
          if($fotballmember['grupp'] == $team['grupp']){
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
    foreach($gymgroups as $gymgroup){
      echo '<div class="column">';
      $i = 0;
      foreach($gymmembers as $gymmember){
        if($gymmember['grupp'] == $gymgroup['grupp']){
          $i++;
        }
      }      
      echo '<h2>' . $gymgroup['grupp'] . '</h2>';
      echo '<p>Antal: ' . $i . '</p>';
      echo '<table>';
      echo '<tr><th>Förnamn</th><th>Efternamn</th></tr>';
        foreach($gymmembers as $gymmember){
          if($gymmember['grupp'] == $gymgroup['grupp']){
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
    foreach($skigroups as $skigroup){
      echo '<div class="column">';
      $i = 0;
      foreach($skimembers as $skimember){
        if($skimember['grupp'] == $skigroup['grupp']){
          $i++;
        }
      }
      echo '<h2>' . $skigroup['grupp'] . '</h2>';
      echo '<p>Antal: ' . $i . '</p>';
      echo '<table>';
      echo '<tr><th>Förnamn</th><th>Efternamn</th></tr>';
        foreach($skimembers as $skimember){
          if($skimember['grupp'] == $skigroup['grupp']){
            echo '<tr><td>' . $skimember['first_name'] . '</td><td>' . $skimember['last_name'] . '</td></tr>';
          }
        }
      echo '</table>';
      echo '</div>';
    }
    ?>
    </div>
    </section>
</body>
</html>