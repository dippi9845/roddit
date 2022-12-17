<?php

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

function getUsersPosts($conn, $userID) {
    $sql = "SELECT *
            FROM post
            WHERE Creator = '$userID';";
    $result = $conn->query($sql);
    if (!$result) {
        return false;
    }
    $posts = array();
    while ($row = $result->fetch_assoc()) {
        $posts[] = $row;
    }
    return $posts;
}

?>
