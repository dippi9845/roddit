<?php 

function getUserNameByID($conn, $userID) {
    $sql = "SELECT Nickname FROM users WHERE ID = {$userID}";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    return $row['Nickname'];
}

function getUserFollowerCount($conn, $userID) {
    $sql = "SELECT COUNT(*) AS FollowerCount FROM follow WHERE Following = {$userID}";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    return $row['FollowerCount'];
}

function getUserFollowingCount($conn, $userID) {
    $sql = "SELECT COUNT(*) AS FollowingCount FROM follow WHERE Follower = {$userID}";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    return $row['FollowingCount'];
}

function isFollowing($conn, $visitedUser, $user) {
    $sql = "SELECT * FROM follow WHERE Follower = {$user} AND Following = {$visitedUser}";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    if ($row) {
        return true;
    } else {
        return false;
    }
}

function getUserFollowers($conn, $userID) {
    $sql = "SELECT follow.Follower AS ID, users.Nickname
            FROM follow
            INNER JOIN users ON follow.Follower = users.ID
            WHERE Following = {$userID};";
    $result = mysqli_query($conn, $sql);
    $followers = array();
    while ($row = mysqli_fetch_assoc($result)) {
        array_push($followers, $row);
    }
    return $followers;
}

?>
