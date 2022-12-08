<?php

function areUserCredsCorrect($conn, $userEmail, $userPassword) {
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("SELECT Salt FROM users WHERE Email = ?");
    $stmt->bind_param("s", $userEmail);

    $stmt->execute();
    $salt = $stmt->get_result()->fetch_assoc()['Salt'];

    $stmt = $conn->prepare("SELECT ID, Email, Password FROM users WHERE Email = ?");
    $stmt->bind_param("s", $userEmail);
    $stmt->execute();

    $result = $stmt->get_result();
    if ($result->num_rows == 0) {
        return false;
    }

    $hashedPassword = realPass($userPassword, $salt);

    $connDbPass = $result->fetch_assoc()['Password'];

    return password_verify($hashedPassword, $connDbPass);
}

function createSession($email) {
    session_start();
    $_SESSION['email'] = $email;
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