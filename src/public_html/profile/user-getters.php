<?php 

function userExists($conn, $userID) {
    // use prepare statement
    $sql = "SELECT * FROM users WHERE ID = ?";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        echo("Error description: " . mysqli_error($conn));
        return;
    }
    mysqli_stmt_bind_param($stmt, "i", $userID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($row = mysqli_fetch_assoc($result)) {
        return true;
    } else {
        return false;
    }
}

function getUserNameByID($conn, $userID) {
    $sql = "SELECT Nickname FROM users WHERE ID = ?";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        echo("Error description: " . mysqli_error($conn));
        return;
    }
    mysqli_stmt_bind_param($stmt, "i", $userID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($row = mysqli_fetch_assoc($result)) {
        return $row['Nickname'];
    } else {
        return false;
    }
}

function getUserProfilePicture($conn, $userID) {
    $sql = "SELECT ProfileImagePath FROM users WHERE ID = ?";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        echo("Error description: " . mysqli_error($conn));
        return;
    }
    mysqli_stmt_bind_param($stmt, "i", $userID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($row = mysqli_fetch_assoc($result)) {
        return $row['ProfileImagePath'];
    } else {
        return false;
    }
}

function getUserFollowerCount($conn, $userID) {
    $sql = "SELECT COUNT(*) AS FollowerCount FROM follow WHERE Following = ?";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        echo("Error description: " . mysqli_error($conn));
        return;
    }
    mysqli_stmt_bind_param($stmt, "i", $userID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    return $row['FollowerCount'];
}

function getUserFollowingCount($conn, $userID) {
    $sql = "SELECT COUNT(*) AS FollowingCount FROM follow WHERE Follower = ?";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        echo("Error description: " . mysqli_error($conn));
        return;
    }
    mysqli_stmt_bind_param($stmt, "i", $userID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    return $row['FollowingCount'];
}

function isFollowing($conn, $visitedUser, $user) {
    $sql = "SELECT * FROM follow WHERE Follower = ? AND Following = ?";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        echo("Error description: " . mysqli_error($conn));
        return;
    }
    mysqli_stmt_bind_param($stmt, "ii", $user, $visitedUser);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($row = mysqli_fetch_assoc($result)) {
        return true;
    } else {
        return false;
    }
}

function getUserFollowers($conn, $userID) {
    $sql = "SELECT follow.Follower AS ID, users.Nickname
            FROM follow
            INNER JOIN users ON follow.Follower = users.ID
            WHERE Following = ?;";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        echo("Error description: " . mysqli_error($conn));
        return;
    }
    mysqli_stmt_bind_param($stmt, "i", $userID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $followers = array();
    while ($row = mysqli_fetch_assoc($result)) {
        array_push($followers, $row);
    }
    return $followers;
}

function getFollowingUsers($conn, $userID) {
    $sql = "SELECT follow.Following AS ID, users.Nickname
            FROM follow
            INNER JOIN users ON follow.Following = users.ID
            WHERE Follower = ?;";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        echo("Error description: " . mysqli_error($conn));
        return;
    }
    mysqli_stmt_bind_param($stmt, "i", $userID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $following = array();
    while ($row = mysqli_fetch_assoc($result)) {
        array_push($following, $row);
    }
    return $following;
}

?>
