<?php

include_once($_SERVER['DOCUMENT_ROOT'].'/profile/globals.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/profile/post-handling.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/html-snippets/post.php');

if (!isUserLoggedIn(true)) {
    header('Location: /login.php');
}

$file = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/../setup.json');
$data = json_decode($file, false);

$conn = new mysqli("localhost", $data->dbName, $data->dbPassword, $data->dbUserName);

if (!isset($_POST['query'])) {
    $_POST['query'] = "";
}

if ($_POST["query"] == "") {
    $posts = getAllPostOfFollowedUsers($conn, $_SESSION['userID']);
} else {
    $posts = getAllPostByContent($conn, $_POST["query"]);
}

$noContent = $posts == null || count($posts) == 0;

$postsID = array();
foreach ($posts as $post) {
    array_push($postsID, $post['ID']);
}

echo(implode(", ", $postsID));

$conn->close();
?>
