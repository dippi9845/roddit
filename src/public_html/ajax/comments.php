<?php
    header('Content-Type: application/json');

    $data = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'].'/../setup.json'), true);
    $conn = new mysqli("localhost", $data->dbName, $data->dbPassword, $data->dbUserName);

    if ($conn->connect_error) {
        die("{ \"Error\": \"" . $conn->connect_error . "\" }");
    }

    if (!isset($_POST['postID'])) {
        die("{ \"Error\": \"No ID\" }");
    }

    $postID = $_POST['postID'];

    $stmt = $conn->prepare(
    "SELECT comment.User, comment.Text, users.ProfileImagePath as ProfileImage
    FROM comment
    INNER JOIN users ON comment.User = users.Nickname
    WHERE `entityType`='Post' AND `entityID`=?
    ");

    $comments = array();

    while ($row = $result->fetch_assoc()) {
        $comments[] = $row;
    }

    echo json_encode($comments);
?>