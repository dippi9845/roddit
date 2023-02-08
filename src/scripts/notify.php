<?php

function get_post_creator($conn, $postID) {
    $stmt = $conn->prepare("SELECT Creator FROM post WHERE ID = ?");
    $stmt->bind_param("i", $postID);
    $stmt->execute();
    $result = $stmt->get_result();
    $nickname = $result->fetch_all(MYSQLI_ASSOC)[0]['Creator'];
    $stmt->close();
    // ------ now get id from nickname
    $stmt = $conn->prepare("SELECT ID FROM users WHERE Nickname = ?");
    
    $stmt->bind_param("s", $nickname);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_all(MYSQLI_ASSOC)[0]['ID'];
}

function get_post_title($conn, $postID) {
    $stmt = $conn->prepare("SELECT Title FROM post WHERE ID = ?");
    $stmt->bind_param("i", $postID);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_all(MYSQLI_ASSOC)[0]['Title'];
}

function notify_user($conn, $userID, $title, $message) {
    $stmt = $conn->prepare("INSERT INTO notification (UserID, Title, Message) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $userID, $title, $message);
    $stmt->execute();
    $stmt->close();
}

function get_notifications($conn, $userID) {
    $stmt = $conn->prepare("SELECT * FROM notification WHERE UserID = ?");
    $stmt->bind_param("s", $userID);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result;
}

function delete_notification($conn, $userID, $start, $offset) {
    $stmt = $conn->prepare("DELETE FROM notification WHERE UserID = ? LIMIT ?, ? ORDER BY Inserimento DESC");
    $stmt->bind_param("sii", $userID, $start, $offset);
    $stmt->execute();
    $stmt->close();
}

?>