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
            WHERE Creator = ?;";
    $stmt = $conn->prepare($sql);
    if (!$stmt->bind_param("s", $userID)) {
        return false;
    }
    if (!$stmt->execute()) {
        return false;
    }
    $result = $stmt->get_result();
    $posts = array();
    while ($row = $result->fetch_assoc()) {
        $posts[] = $row;
    }
    return $posts;
}

function isLiked($conn, $postID, $userID) {
    $sql = "SELECT *
            FROM likes
            WHERE Post = ? AND User = ?;";
    $stmt = $conn->prepare($sql);
    if (!$stmt->bind_param("ss", $postID, $userID)) {
        return false;
    }
    if (!$stmt->execute()) {
        return false;
    }
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        return true;
    }
    return false;
}

function getPostOfFollowedUsers($conn, $userID) {
    $sql = "SELECT *
            FROM post
            WHERE Creator IN (SELECT Following FROM follow WHERE Follower = ?);";
    $stmt = $conn->prepare($sql);
    if (!$stmt->bind_param("s", $userID)) {
        return false;
    }
    if (!$stmt->execute()) {
        return false;
    }
    $result = $stmt->get_result();
    $posts = array();
    while ($row = $result->fetch_assoc()) {
        $posts[] = $row;
    }
    return $posts;
}

?>
