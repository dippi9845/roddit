<?php

include_once($_SERVER['DOCUMENT_ROOT'].'/../scripts/globals.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/html-snippets/user.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/../scripts/user-getters.php');

if (!isUserLoggedIn(true)) {
    header('Location: /login.php');
}

$file = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/../setup.json');
$data = json_decode($file, false);

$conn = new mysqli("localhost", $data->dbName, $data->dbPassword, $data->dbUserName);

if (!isset($_POST['query'])) {
    $_POST['query'] = "";
}

if (!isset($_POST['offset']) || !isset($_POST['limit'])) {
    $_POST['offset'] = 0;
    $_POST['limit'] = 5;
}

if ($_POST["query"] != "") {
    $users = getSearchedUsers($conn, $_POST["query"], $_POST['offset'], $_POST['limit']);

    foreach ($users as $user) {
        drawUserCard($user['ID'], $user['Nickname'], $user['ProfileImagePath'], isFollowing($conn, $user['ID'], $_SESSION['userID']), getUserFollowerCount($conn, $user['ID']), getUserFollowingCount($conn, $user['ID']));
    }
}

$conn->close();
?>

<script src="assets/js/btn-ajax-form.js"></script>
