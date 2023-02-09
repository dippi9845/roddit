<?php

/**
 * Checks if the form is valid
 */
function isFormValid() {
    return isset($_POST['email']) && isset($_POST['password']);
}

/**
 * Logs in the user.
 */
function main($conn) {
    if (!isFormValid()) {
        echo("<br/>Invalid form");
        return false;
    }

    if (!$userID = getUserID($conn, $_POST['email'], $_POST['password'])) {
        echo("<br/>Invalid credentials");
        return false;
    }

    createSession($userID);

    if (isset($_POST['remember'])) {
        createCookie($conn, $userID);
    }

    $conn->close();

    return true;
}

include_once($_SERVER['DOCUMENT_ROOT'].'/../scripts/globals.php');

$file = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/../setup.json');
$data = json_decode($file, false);

$conn = new mysqli("localhost", $data->dbName, $data->dbPassword, $data->dbUserName);

if (main($conn)) {
    header('Location: /index.php');
} else {
    header('Location: /login.php');
}

?>
