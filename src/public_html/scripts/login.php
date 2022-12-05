<?php

    function isFormValid() {
        return isset($_POST['email']) && isset($_POST['password']);
    }

    function main() {
        if (!isFormValid()) {
            echo("Invalid form");
            return false;
        }

    }

    if (main()) {
        header('Location: /');
    } else {
        header('Location: /login.php');
    }

?>
