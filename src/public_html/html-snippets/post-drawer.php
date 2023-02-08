<?php

include_once($_SERVER['DOCUMENT_ROOT'].'../scripts/globals.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '../scripts/post-handling.php');
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

if (!isset($_POST['offset']) || !isset($_POST['limit'])) {
    $_POST['offset'] = 0;
    $_POST['limit'] = 5;
}

if ($_POST["query"] == "") {
    $posts = getPostOfFollowedUsers($conn, $_SESSION['userID'], $_POST['offset'], $_POST['limit']);
} else {
    $posts = getPostByContent($conn, $_POST["query"], $_POST['offset'], $_POST['limit']);
}

foreach ($posts as $post) {
    drawPost($post['ID'], $post['UserID'], $post['Nickname'], $post['ProfileImagePath'], $post['Title'], $post['Text'], $post['Likes'], isLiked($conn, $post['ID'], $_SESSION['userID']), $post['Comments'], $post['PathToFile']);
}

$conn->close();
?>
