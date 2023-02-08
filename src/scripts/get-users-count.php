<?php

include_once($_SERVER['DOCUMENT_ROOT'].'../scripts/globals.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/html-snippets/user.php');

if (!isUserLoggedIn(true)) {
    header('Location: /login.php');
}

$file = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/../setup.json');
$data = json_decode($file, false);

$conn = new mysqli("localhost", $data->dbName, $data->dbPassword, $data->dbUserName);

if (!isset($_POST['query'])) {
    $_POST['query'] = "";
}

$usersCount = 0;

if ($_POST["query"] != "") {
    $usersCount = getAllSearchedUsersCount($conn, $_POST["query"]);
}

echo $usersCount;

$conn->close();
?>
