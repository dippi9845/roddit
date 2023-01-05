<?php

function createPostWithFile($conn, $title, $text, $path, $type) {
    $sql = "INSERT INTO post (Creator, Title, Text, PathToFile, MediaType) VALUES (?, ?, ?, ?, ?)";
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
    $sql = "SELECT post.*, users.Nickname
            FROM post
            INNER JOIN users ON post.Creator = users.ID
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

function getPostOfFollowedUsers($conn, $userID, $offset, $perPage) {
    $sql = "SELECT post.*, users.Nickname, users.ProfileImagePath, users.ID AS UserID
            FROM post
            INNER JOIN users ON post.Creator = users.ID
            WHERE Creator IN (SELECT Following FROM follow WHERE Follower = ?)
            ORDER BY ID
            LIMIT ?, ?;";
    $stmt = $conn->prepare($sql);
    if (!$stmt->bind_param("sss", $userID, $offset, $perPage)) {
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

function getAllPostOfFollowedUsers($conn, $userID) {
    $sql = "SELECT post.ID
            FROM post
            INNER JOIN users ON post.Creator = users.ID
            WHERE Creator IN (SELECT Following FROM follow WHERE Follower = ?)
            ORDER BY ID";
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

function getPostByContent($conn, $content, $offset, $perPage) {
    $sql = "SELECT post.*, users.Nickname, users.ProfileImagePath, users.ID AS UserID
            FROM post
            INNER JOIN users ON post.Creator = users.ID
            WHERE Title REGEXP ?
            OR Text REGEXP ?
            ORDER BY ID DESC
            LIMIT ?, ?;";

    $stmt = $conn->prepare($sql);

    $content = explode(' ',$content);
    $content = implode('|', $content);

    if (!$stmt->bind_param("ssss", $content, $content, $offset, $perPage)) {
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

function getAllPostByContent($conn, $content) {
    $sql = "SELECT post.ID
    FROM post
    INNER JOIN users ON post.Creator = users.ID
    WHERE Title REGEXP ?
    OR Text REGEXP ?
    ORDER BY ID DESC";

    $stmt = $conn->prepare($sql);

    $content = explode(' ',$content);
    $content = implode('|', $content);

    if (!$stmt->bind_param("ss", $content, $content)) {
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
