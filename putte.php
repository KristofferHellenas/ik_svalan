<?php

// php delete data in mysql database using PDO

if (isset($_POST['delete'])) {
    try {
        $pdoConnect = new PDO("mysql:host=localhost;dbname=ik svalan", "Admin", "password");
    } catch (PDOException $exc) {
        echo $exc->getMessage();
        exit();
    }

    // get id to delete

    $id = $_POST['id'];

    // mysql delete query 

    $pdoQuery = "DELETE FROM `medlemmar` WHERE `id` = :id";

    $pdoResult = $pdoConnect->prepare($pdoQuery);

    $pdoExec = $pdoResult->execute(array(":id" => $id));

    if ($pdoExec) {
        echo 'Data Deleted';
    } else {
        echo 'ERROR Data Not Deleted';
    }
}

?>

<!DOCTYPE html>

<html>

<head>

    <title>Ta bort medlem</title>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

</head>

<body>

    <form action="putte.php" method="post">

        ID To Delete : <input type="text" name="id" required><br><br>

        <input type="submit" name="delete" value="Delete Data">

    </form>
    <p>HELVETE</p>

</body>

</html>