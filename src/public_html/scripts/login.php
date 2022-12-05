<?php

    function isFormValid() {
        return isset($_POST['email']) && isset($_POST['password']);
    }

    function areUserCredsCorrect($email, $password) {
        //TODO: Check if the user and password are correct
    }

    function createSession($email) {
        //TODO: Create a session for the user
    }

    function createCookie($email) {
        //TODO: Create a cookie for the user
    }

    function main() {
        if (!isFormValid()) {
            echo("Invalid form");
            return false;
        }

        if (!areUserCredsCorrect($_POST['email'], $_POST['password'])) {
            echo("Invalid credentials");
            return false;
        }

        createSession($_POST['email']);

        if (isset($_POST['remember'])) {
            createCookie($_POST['email']);
        }

        return true;
    }

    if (main()) {
        header('Location: /');
    } else {
        header('Location: /login.php');
    }

?>
