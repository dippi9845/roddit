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

function createCookie($email) {
    //TODO: Create a cookie for the user
}

function saltPass($pass, $salt) {
    return $pass . "Sono Bello" . $salt;
}

function realPass($pass, $salt) {
    return password_hash(saltPass($pass, $salt), PASSWORD_DEFAULT);
}

?>
