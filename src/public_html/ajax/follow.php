<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/../scripts/notify.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/../scripts/user-getters.php';

/**
 * Follows a user adding it to the follow table.
 * @param object $conn mysqli connection
 * @param string $userID int user id
 * @param string $followedUserID int followed user id
 */
function followUser($conn, $userID, $followedUserID) {
    $sql = "INSERT INTO follow (Follower, Following) VALUES ('{$userID}', '{$followedUserID}')";
    if (mysqli_query($conn, $sql)) {
        echo "Followed successfully<br>";
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
}

session_start();
$file = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/../setup.json');
$data = json_decode($file, false);

$conn = new mysqli("localhost", $data->dbName, $data->dbPassword, $data->dbUserName);

followUser($conn, $_SESSION['userID'], $_POST['followedUser']);

notify_user($conn, $_POST['followedUser'], "New follower", "You have a new follower: " . getUserNameByID($conn, $_SESSION['userID']));

$conn->close();

?>
