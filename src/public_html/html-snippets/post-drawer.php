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
    $posts = getPostOfFollowedUsers($conn, $_SESSION['userID']);
} else {
    $posts = getPostByContent($conn, $_POST["query"]);
}

$noContent = $posts == null || count($posts) == 0;

foreach ($posts as $post) {
    drawPost($post['ID'], $post['UserID'], $post['Nickname'], $post['ProfileImagePath'], $post['Title'], $post['Text'], $post['Likes'], isLiked($conn, $post['ID'], $_SESSION['userID']), null, $post['PathToFile']);
}

if ($noContent) {
    echo "<h1>No content found</h1>";
}
$conn->close();
?>
