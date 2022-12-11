<?php

function isFormValid() {
    return isset($_POST['title']) && isset($_POST['text']);
}

function createPost($conn, $title, $text, $image=null) {
    if (!is_null($image)) {
        $sql = "INSERT INTO posts (Creator, Title, Text, PathToImage) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $_SESSION['userId'], $title, $text, $image);
    } else {
        $sql = "INSERT INTO posts (Creator, Title, Text) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $_SESSION['userId'], $title, $text);
    }

    $stmt->execute();
    $stmt->close();
}

function main($data) {
    if (!isFormValid()) {
        echo("<br/>Invalid form");
        return false;
    }
}
include_once($_SERVER['DOCUMENT_ROOT'].'/profile/globals.php');

$file = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/setup.json');
$data = json_decode($file, false);

main($data);

?>
