<?php
header('Content-Type: application/json; charset=utf-8');
session_start();
if (isset($_SESSION['userID'])) {
    $data = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'].'/../setup.json'));
    $conn = new mysqli("localhost", $data->dbName, $data->dbPassword, $data->dbUserName);

    $stmt = $conn->prepare("SELECT ID, Title, Message FROM `notification` WHERE `UserID` = ? ORDER BY inserimento DESC LIMIT ?, ?");
    $stmt->bind_param("iii", $_SESSION['userID'], $_GET['o'], $_GET['n']);
    $stmt->execute();
    $result = $stmt->get_result();
    $notifications = $result->fetch_all(MYSQLI_ASSOC);

    $stmt->close();
    $conn->close();

    echo json_encode($notifications);
}
?>