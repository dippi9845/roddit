<?php

function isFormValid() {
    return isset($_POST['title']) && isset($_POST['text']);
}

function createImageFolderIfNotExists() {
    $path = $_SERVER['DOCUMENT_ROOT'].'/uploads/images/';
    if (!file_exists($path)) {
        mkdir($path, 0777, true);
    }
}

function saveImage($image) {
    createImageFolderIfNotExists();
    $path = $_SERVER['DOCUMENT_ROOT'].'/uploads/images/'.uniqid().basename($image['name']);
    if (!move_uploaded_file($image['tmp_name'], $path)) {
        return false;
    }
    return $path;
}

function createPostWithFile($conn, $title, $text, $path, $type) {
    $sql = "INSERT INTO post (Creator, Title, Text, PathToImage, MediaType) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt->bind_param("sssss", $_SESSION['userID'], $title, $text, $path, $type)) {
        return false;
    }
    if (!$stmt->execute()) {
        return false;
    }
    $stmt->close();
    return true;
}

function createPost($conn, $title, $text) {
    $sql = "INSERT INTO post (Creator, Title, Text) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt->bind_param("sss", $_SESSION['userID'], $title, $text)) {
        return false;
    }

    if (!$stmt->execute()) {
        return false;
    }
    $stmt->close();
    return true;
}

function main($data) {
    if (!isFormValid()) {
        echo("<br/>Invalid form");
        return false;
    }

    $conn = new mysqli("localhost", $data->dbName, $data->dbPassword, $data->dbUserName);
    if (!createPost($conn, $_POST['title'], $_POST['text'], $_POST['image'])) {
        echo("<br/>Post not created");
        return false;
    }
    $conn->close();

    if (!isset($_POST['image'])) {
        return true;
    }

    if (!saveImage($_POST['image'])) {
        echo("<br/>Image not saved");
        return false;
    }

    return true;
}

include_once($_SERVER['DOCUMENT_ROOT'].'/profile/globals.php');

$file = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/setup.json');
$data = json_decode($file, false);

if (main($data)) {
    echo("<br/>Post created");
} else {
    echo("<br/>Something went wrong");
}

?>
