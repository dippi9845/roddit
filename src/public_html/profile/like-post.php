<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/notify.php';

/**
 * Likes a post adding it to the likes table.
 * @param object $conn mysqli connection
 * @param string $userID int user id
 * @param string $postID int post id
 */
function likePost($conn, $userID, $postID) {
    $sql = "INSERT INTO likes (User, Post) VALUES ('{$userID}', '{$postID}')";
    if (mysqli_query($conn, $sql)) {
        echo "Liked successfully<br>";
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }

    $sql = "UPDATE post SET Likes = Likes + 1 WHERE ID = '{$postID}'";
    if (mysqli_query($conn, $sql)) {
        echo "Updated successfully<br>";
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
}

session_start();
$file = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/../setup.json');
$data = json_decode($file, false);

$conn = new mysqli("localhost", $data->dbName, $data->dbPassword, $data->dbUserName);

likePost($conn, $_SESSION['userID'], $_POST['postID']);
notify_user($conn, $_POST['userID'], "New like", "You have a new like!");

$conn->close();

?>
