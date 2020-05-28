<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$user = 'Admin';
$pw = 'password';

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

// Ta bort lag
if (isset($_POST['deleteteam'])) {
    $id = $_POST['chosenteam'];

    $pdoQuery = "DELETE FROM fotboll WHERE id = :id";
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
    <form action="putte.php" method="post">
        <select name="chosenteam" id="">
            <?php
            foreach ($fotballteams as $fotballteam) {
                echo '<option value="' . $fotballteam['id'] . '">' . $fotballteam['grupp'] . '</option>';
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
        <input type="submit" value="Ta bort lag" name="deleteteam">
    </form>
    <table>
        <tr>
            <th>First name</th>
            <th>Last name</th>
            <th>E-mail</th>
            <th>Membership</th>
        </tr>
        <?php
        foreach ($fotballteams as $fotballteam) {
            echo '<tr><td>' . $fotballteam['grupp'] . '</td><td>';
        }
        ?>
    </table>

</body>

</html>