<?php

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
        $file = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/setup.json');
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

function createSession($userID) {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['userID'] = $userID;
}

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

    if (hash_equals($hashedToken, hash('sha256', base64_decode($authenticator)))) {
        createSession($userID);
    } else {
        return false;
    }

    return true;
}

function saltPass($pass, $salt) {
    return $pass . "Sono Bello" . $salt;
}

function realPass($pass, $salt) {
    return password_hash(saltPass($pass, $salt), PASSWORD_DEFAULT);
}

?>
