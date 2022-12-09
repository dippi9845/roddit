<?php

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
    session_start();
    $_SESSION['userID'] = $userID;
}

function createCookie($conn, $userID) {
    $selector = base64_encode(random_bytes(9));
    $authenticator = random_bytes(33);
    $expirationTime = time() + 3600 * 24 * 15;  // the cookie will expire in 15 days
    $myHash = hash('sha256', $authenticator);
    $myDate = date('Y-m-d\TH:i:s', $expirationTime);

    # creating the cookie client side
    setcookie("roddit", $selector, intval($expirationTime), "/");

    # creating the cookie server side
    $sql = "INSERT INTO cookies (UserID, token, HashToken, ExpireDate) VALUES ('{$userID}', '{$selector}', '{$myHash}', '{$myDate}')";
    if (mysqli_query($conn, $sql)) {
        echo "Cookie created successfully on server side<br>";
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }

}

function saltPass($pass, $salt) {
    return $pass . "Sono Bello" . $salt;
}

function realPass($pass, $salt) {
    return password_hash(saltPass($pass, $salt), PASSWORD_DEFAULT);
}

?>
