<?php

    function isFormValid() {
        return isset($_POST['email']) && isset($_POST['password']);
    }

    function areUserCredsCorrect($dbName, $dbUserName, $dbPassword, $userEmail, $userPassword) {
    }

    function createSession($email) {
        //TODO: Create a session for the user
    }

    function createCookie($email) {
        //TODO: Create a cookie for the user
    }

    function main($data) {
        if (!isFormValid()) {
            echo("<br/>Invalid form");
            return false;
        }

        if (!areUserCredsCorrect($data['dbName'], $data['dbUserName'], $data['dbPassword'], $_POST['email'], $_POST['password'])) {
            echo("Invalid credentials");
            echo("<br/>Invalid credentials");
            return false;
        }

        createSession($_POST['email']);

        if (isset($_POST['remember'])) {
            createCookie($_POST['email']);
        }

        return true;
    }

    $file = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/setup.json');
    $data = json_decode($file, false);
    
        header('Location: /index.php');
    if (main($data)) {
    } else {
        header('Location: /login.php');
    }

?>
