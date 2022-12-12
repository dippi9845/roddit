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
    $image = str_replace('data:image/png;base64,', '', $image);
    $image = str_replace(' ', '+', $image);
    $data = base64_decode($image);

    createImageFileIfNotExists();

    $file = $_SERVER['DOCUMENT_ROOT'].'/profile/images/'.uniqid().'.png';
    $success = file_put_contents($file, $data);
    return $success;
}

function createPost($conn, $title, $text, $image=null) {
    if (!is_null($image)) {
        $sql = "INSERT INTO posts (Creator, Title, Text, PathToImage, MediaType) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if (!$stmt->bind_param("sss", $_SESSION['userId'], $title, $text, $image, $image['type'])) {
            return false;
        }
    } else {
        $sql = "INSERT INTO posts (Creator, Title, Text) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if (!$stmt->bind_param("sss", $_SESSION['userId'], $title, $text)) {
            return false;
        }
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
