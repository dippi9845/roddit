<?php

/**
 * Creates a new post with a file.
 * @param object $conn mysqli connection
 * @param string $title string title
 * @param string $text string text
 * @param string $path string path to file
 * @param string $type string type of file
 * @return boolean true if successful
 */
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

/**
 * Creates a new post with only text.
 * @param object $conn mysqli connection
 * @param string $title string title
 * @param string $text string text
 * @return boolean true if successful
 */
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

/**
 * Returns all posts from a user.
 * @param object $conn mysqli connection
 * @param string $userID int user id
 * @return array posts containing all posts from a user
 */
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

/**
 * Checks if a post is liked by a user.
 * @param object $conn mysqli connection
 * @param string $postID int post id
 * @param string $userID int user id
 * @return boolean true if post is liked by user
 */
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

/**
 * Gets all posts of followed users.
 * @param object $conn mysqli connection
 * @param string $userID int user id
 * @param string $offset int offset
 * @param string $perPage int posts per page
 * @return array posts containing all posts of followed users
 */
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

/**
 * Gets the number of posts of followed users.
 * @param object $conn mysqli connection
 * @param string $userID int user id
 * @return int number of posts of followed users
 */
function getAllPostOfFollowedUsersCount($conn, $userID) {
    $sql = "SELECT COUNT(DISTINCT post.ID) AS 'Total'
            FROM post
            INNER JOIN users ON post.Creator = users.ID
            WHERE Creator IN (SELECT Following FROM follow WHERE Follower = ?)
            ORDER BY post.ID";
    $stmt = $conn->prepare($sql);
    if (!$stmt->bind_param("s", $userID)) {
        return false;
    }
    if (!$stmt->execute()) {
        return false;
    }

    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['Total'];
}

/**
 * Gets all posts of a user.
 * @param object $conn mysqli connection
 * @param string $userID int user id
 * @param string $offset int offset
 * @param string $perPage int posts per page
 * @return array posts containing all posts of a user
 */
function getPostByContent($conn, $content, $offset, $perPage) {
    $sql = "SELECT post.*, users.Nickname, users.ProfileImagePath, users.ID AS UserID
            FROM post
            INNER JOIN users ON post.Creator = users.ID
            WHERE Title REGEXP ?
            OR Text REGEXP ?
            ORDER BY ID DESC
            LIMIT ?, ?;";

    $stmt = $conn->prepare($sql);

    $content = explode('+',$content);
    $content = implode('|', $content);

    echo($content);

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

/**
 * Gets the number of posts of a user.
 * @param object $conn mysqli connection
 * @param string $userID int user id
 * @return int number of posts of a user
 */
function getAllPostByContentCount($conn, $content) {
    $sql = "SELECT COUNT(DISTINCT post.ID) AS 'Total'
            FROM post
            INNER JOIN users ON post.Creator = users.ID
            WHERE Title REGEXP ?
            OR Text REGEXP ?
            ORDER BY post.ID DESC;";

    $stmt = $conn->prepare($sql);

    $content = explode('+',$content);
    $content = implode('|', $content);

    if (!$stmt->bind_param("ss", $content, $content)) {
        return false;
    }

    if (!$stmt->execute()) {
        return false;
    }

    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['Total'];
}

?>
