<?php

    function unfollowUser($conn, $userID, $followedUserID) {
        $sql = "DELETE FROM follow WHERE Follower = '{$userID}' AND Following = '{$followedUserID}'";
        if (mysqli_query($conn, $sql)) {
            echo "Unfollowed successfully<br>";
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
    }

    session_start();
    $file = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/../setup.json');
    $data = json_decode($file, false);

    $conn = new mysqli("localhost", $data->dbName, $data->dbPassword, $data->dbUserName);

    unfollowUser($conn, $_SESSION['userID'], $_POST['unfollowedUser']);

    $conn->close();

?>
