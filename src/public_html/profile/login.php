<?php

    function isFormValid() {
        return isset($_POST['email']) && isset($_POST['password']);
    }

    function main($data) {
        if (!isFormValid()) {
            echo("<br/>Invalid form");
            return false;
        }

        $conn = new mysqli("localhost", $data->dbName, $data->dbPassword, $data->dbUserName);

        if (!areUserCredsCorrect($conn, $_POST['email'], $_POST['password'])) {
            echo("<br/>Invalid credentials");
            return false;
        }

        createSession($_POST['email']);

        if (isset($_POST['remember'])) {
            createCookie($_POST['email']);
        }

        return true;
    }

    include_once($_SERVER['DOCUMENT_ROOT'].'/profile/globals.php');

    $file = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/setup.json');
    $data = json_decode($file, false);
    
    if (main($data)) {
        header('Location: /index.php');
    } else {
        header('Location: /login.php');
    }

?>
