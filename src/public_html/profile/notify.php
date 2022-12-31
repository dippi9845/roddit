<?php

function notify_user($conn, $userID, $title, $message) {
    $stmt = $conn->prepare("INSERT INTO notifications (UserID, Title, Message) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $userID, $title, $message);
    $stmt->execute();
    $stmt->close();
}

function get_notifications($conn, $userID) {
    $stmt = $conn->prepare("SELECT * FROM notifications WHERE UserID = ?");
    $stmt->bind_param("s", $userID);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result;
}

?>