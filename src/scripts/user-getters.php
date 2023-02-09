<?php 

/**
 * Checks if the user exists.
 * @param object $conn mysqli connection
 * @param string $userID int user id
 * @return boolean true if user exists
 */
function userExists($conn, $userID) {
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

/**
 * Returns the biography of a given user id.
 * @param object $conn mysqli connection
 * @param string $userID int user id
 * @return string the string of the user
 */
function getUserBiography($conn, $userID) {
    $sql = "SELECT Bio FROM users WHERE ID = ?";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        echo("Error description: " . mysqli_error($conn));
        return;
    }
    mysqli_stmt_bind_param($stmt, "i", $userID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($row = mysqli_fetch_assoc($result)) {
        return $row['Bio'];
    } else {
        return false;
    }
}

/**
 * Returns the userNickname of a given user id.
 * @param object $conn mysqli connection
 * @param string $userID int user id
 * @return string userNickname of the user
 */
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

/**
 * Gets the profile picture of a user.
 * @param object $conn mysqli connection
 * @param string $userID int user id
 * @return string profile picture path
 */
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

/**
 * Gets user's follower count.
 * @param object $conn mysqli connection
 * @param string $userID int user id
 * @return int follower number of users that are following the user
 */
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

/**
 * Gets user's following count.
 * @param object $conn mysqli connection
 * @param string $userID int user id
 * @return int following number of users that the user is following
 */
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

/**
 * Checks if the user is following another user.
 * @param object $conn mysqli connection
 * @param string $userID int user id
 * @param string $visitedUser int visited user id
 * @return boolean true if user is following
 */
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

/**
 * Gets the user's followers.
 * @param object $conn mysqli connection
 * @param string $userID int user id
 * @return array followers the users that are following the user
 */
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

/**
 * Gets the user's following.
 * @param object $conn mysqli connection
 * @param string $userID int user id
 * @return array following the users that the user is following
 */
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
