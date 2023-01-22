<?php
header('Content-Type: application/json; charset=utf-8');
session_start();
if (isset($_SESSION['userID'])) {
    $data = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'].'/../setup.json'));
    $conn = new mysqli("localhost", $data->dbName, $data->dbPassword, $data->dbUserName);

    $stmt = $conn->prepare("SELECT Title, Message FROM `notification` WHERE `UserID` = ? ORDER BY inserimento DESC");
    $stmt->bind_param("i", $_SESSION['userID']);
    $stmt->execute();
    $result = $stmt->get_result();
    $notifications = $result->fetch_all(MYSQLI_ASSOC);

    $stmt->close();
    $conn->close();

    echo json_encode($notifications);
}
?>