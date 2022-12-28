<?php

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

    $conn->close();

?>
