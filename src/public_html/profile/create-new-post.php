<?php

/**
 * checks if the form is valid
 */
function isFormValid() {
    return isset($_POST['title']) && isset($_POST['text']);
}

/**
 * Creates a post with a file
 */
function main($data) {
    if (!isFormValid()) {
        echo("<br/>Invalid form");
        return false;
    }

    $conn = new mysqli("localhost", $data->dbName, $data->dbPassword, $data->dbUserName);

    if (!file_exists($_FILES['file']['tmp_name']) || !is_uploaded_file($_FILES['file']['tmp_name'])) {
        if (!createPost($conn, $_POST['title'], $_POST['text'])) {
            echo("<br/>Post not created");
            return false;
        }
    
    } else {
        if (! $path = saveImage($_FILES['file'])) {
            echo("<br/>Image not saved");
            return false;
        }

        if (!createPostWithFile($conn, $_POST['title'], $_POST['text'], $path, $_FILES['file']['type'])) {
            echo("<br/>Post not created");
            return false;
        }
    }

    $conn->close();

    return true;
}

include_once($_SERVER['DOCUMENT_ROOT'].'/profile/globals.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/profile/post-handling.php');
session_start();

$file = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/../setup.json');
$data = json_decode($file, false);

if (main($data)) {
    echo("<br/>Post created");
} else {
    echo("<br/>Something went wrong");
}

header("Location: /profile.php");

?>
