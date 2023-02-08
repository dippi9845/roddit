<?php
/**
 * Dislikes a post removing it from the likes table and decrementing the likes counter.
 * @param object $conn mysqli connection
 * @param string $userID int user id
 * @param string $postID int post id
 */
function dislikelikePost($conn, $userID, $postID) {
    $sql = "DELETE FROM likes WHERE User = '{$userID}' AND Post = '{$postID}'";
    if (mysqli_query($conn, $sql)) {
        echo "Disliked successfully<br>";
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }

    $sql = "UPDATE post SET Likes = Likes - 1 WHERE ID = '{$postID}'";
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

dislikelikePost($conn, $_SESSION['userID'], $_POST['postID']);

$conn->close();

?>
