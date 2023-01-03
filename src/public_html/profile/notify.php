<?php

function notify_user($conn, $userID, $title, $message) {
    $stmt = $conn->prepare("INSERT INTO notification (UserID, Title, Message) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $userID, $title, $message);
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