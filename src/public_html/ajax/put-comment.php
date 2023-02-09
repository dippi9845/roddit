<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/../scripts/user-getters.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/../scripts/notify.php';

header('Content-Type: application/json; charset=utf-8');
session_start();

if (isset($_SESSION['userID']) && isset($_GET['text']) && isset($_GET['postID'])) {

    $data = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'].'/../setup.json'));
    $conn = new mysqli("localhost", $data->dbName, $data->dbPassword, $data->dbUserName);

    $stmt = $conn->prepare("INSERT INTO comment (User, entityID, entityType, Text) VALUE (?, ?, 'Post', ?)");
    $username = getUserNameByID($conn, $_SESSION['userID']);
    $stmt->bind_param("sis", $username , $_GET['postID'], $_GET['text']);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare("UPDATE post SET Comments=Comments + 1 WHERE ID = ?");
    $stmt->bind_param("i", $_GET['postID']);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare(
        "SELECT ProfileImagePath as ProfileImage, Nickname as User
        FROM `users`
        WHERE `ID` = ?");

    $stmt->bind_param("i", $_SESSION['userID']);
    $stmt->execute();
    $result = $stmt->get_result();
    $mydata = $result->fetch_all(MYSQLI_ASSOC);

    $stmt->close();

    notify_user($conn, get_post_creator($conn, $_GET['postID']), "New comment", "You have a new comment to a post: ".substr(get_post_title($conn, $_GET['postID']), 0, 100)." , from ".getUserNameByID($conn, $_SESSION['userID']));
    
    $conn->close();

    echo json_encode($mydata[0]);
}
?>