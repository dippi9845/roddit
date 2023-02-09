<?php

/**
 * Creates the uploads folder if it doesn't exist
 */
function createImageFolderIfNotExists() {
    $path = $_SERVER['DOCUMENT_ROOT'].'/uploads/images/';
    if (!file_exists($path)) {
        mkdir($path, 0777, true);
    }
}

/**
 * Saves an image to the server.
 * @param array $image The image array from $_FILES
 * @return string|false The path to the image or false if the image could not be saved
 */
function saveImage($image) {
    createImageFolderIfNotExists();
    $path = '/uploads/images/'.uniqid().basename($image['name']);
    if (!move_uploaded_file($image['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $path)) {
        return false;
    }
    return $path;
}

/**
 * Checks if user is logged in
 * @param bool $loginIfCookieExists If true, the function will try to login the user using the cookie
 */
function isUserLoggedIn($loginIfCookieExists = false) {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    if (isset($_SESSION['userID'])) {
        return true;
    }

    if ($loginIfCookieExists) {
        $file = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/../setup.json');
        $data = json_decode($file, false);
        $conn = new mysqli("localhost", $data->dbName, $data->dbPassword, $data->dbUserName);
        if (tryLoginCookie($conn)) {
            return true;
        }
    }

    return false;
}

function getUserID($conn, $userEmail, $userPassword) {
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("SELECT ID, Password, Salt FROM users WHERE Email = ?");
    $stmt->bind_param("s", $userEmail);
    $stmt->execute();

    $result = $stmt->get_result();
    if ($result->num_rows == 0) {
        return false;
    }

    $row = $result->fetch_assoc();
    $userID = $row['ID'];
    $salt = $row['Salt'];
    $hashedPassword = $row['Password'];

    if (password_verify(saltPass($userPassword, $salt), $hashedPassword)) {
        return $userID;
    } else {
        return false;
    }
}

/**
 * Creates a session for the user.
 * @param string $userID The user id
 */
function createSession($userID) {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['userID'] = $userID;
}

/**
 * Creates a cookie for the user.
 * @param object $conn mysqli connection
 * @param string $userID The user id
 */
function createCookie($conn, $userID) {
    $selector = base64_encode(random_bytes(9));
    $authenticator = random_bytes(33);
    $expirationTime = time() + 3600 * 24 * 15;  // the cookie will expire in 15 days
    $myHash = hash('sha256', $authenticator);
    $myDate = date('Y-m-d\TH:i:s', $expirationTime);

    $cookieArray = [$selector, base64_encode($authenticator), $expirationTime, "/"];
    $cookieContent = "{
        \"selector\" : \"{$cookieArray[0]}\",
        \"authenticator\" : \"{$cookieArray[1]}\",
        \"expirationTime\" : \"{$cookieArray[2]}\",
        \"path\" : \"{$cookieArray[3]}\"}";

    # creating the cookie client side
    setcookie("roddit", $cookieContent, intval($expirationTime), "/");

    # creating the cookie server side
    $sql = "INSERT INTO cookies (UserID, token, HashToken, ExpireDate) VALUES ('{$userID}', '{$selector}', '{$myHash}', '{$myDate}')";
    if (mysqli_query($conn, $sql)) {
        echo "Cookie created successfully on server side<br>";
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }

}

/**
 * Tries to login the user using the cookie.
 * @param object $conn mysqli connection
 * @return bool true if the user is logged in, false otherwise
 */
function tryLoginCookie($conn) {
    if (!isset($_COOKIE["roddit"])) {
        return false;
    }

    $dumpedCookie = json_decode($_COOKIE['roddit']);
    $selector = $dumpedCookie->selector;
    $authenticator = $dumpedCookie->authenticator;

    $sql = "SELECT * FROM cookies WHERE Token = ?";

    $stmt =  $conn->prepare($sql);

    $stmt->bind_param("s", $selector);
    $stmt->execute();

    $result = $stmt->get_result();
    if ($result->num_rows == 0) {
        return false;   // the cookie exists only on this machine
    }

    $row = $result->fetch_assoc();
    $userID = $row['UserID'];
    $hashedToken = $row['HashToken'];
    $expirationDate = $row['ExpireDate'];

    if (time() > strtotime($expirationDate)) {
        return false;
    }

    if (hash_equals($hashedToken, hash('sha256', base64_decode($authenticator)))) {
        createSession($userID);
    } else {
        return false;
    }

    return true;
}

/**
 * Gets users that Nickname is similar to a given pattern and a given offset.
 * @param object $conn mysqli connection
 * @param string $search The pattern to search
 * @param int $offset The offset
 * @param int $perPage The number of users per page
 * @return array|bool The array of users or false if an error occurred
 */
function getSearchedUsers($conn, $search, $offset, $perPage) {
    $sql = "SELECT users.ID, users.Nickname, users.ProfileImagePath
            FROM users
            WHERE Nickname REGEXP ?
            ORDER BY Nickname
            LIMIT ?, ?";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt->bind_param("sss", $search, $offset, $perPage)) {
        return false;
    }

    if (!$stmt->execute()) {
        return false;
    }
    $result = $stmt->get_result();
    $users = array();
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    return $users;
}

/**
 * Gets the count of users that Nickname is similar to a given pattern.
 * @param object $conn mysqli connection
 * @param string $search The pattern to search
 * @return int The count of users
 */
function getAllSearchedUsersCount($conn, $search) {
    $sql = "SELECT COUNT(*) AS Count 
            FROM users
            WHERE Nickname REGEXP ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $search);
    $stmt->execute();

    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['Count'];
}

function saltPass($pass, $salt) {
    return $pass . "Sono Bello" . $salt;
}

function realPass($pass, $salt) {
    return password_hash(saltPass($pass, $salt), PASSWORD_DEFAULT);
}

?>
