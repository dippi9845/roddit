<?php

    function isFormValid() {
        return isset($_POST['email']) && isset($_POST['password']);
    }

    function areUserCredsCorrect($dbName, $dbUserName, $dbPassword, $userEmail, $userPassword) {
        $conn = new mysqli("localhost", $dbUserName, $dbPassword, $dbName);
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

        $hashedPassword = $userPassword + $salt;

        $connDbPass = $result->fetch_assoc()['Password'];

        return password_verify($hashedPassword, $connDbPass);
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

        if (!areUserCredsCorrect($data->dbName, $data->dbUserName, $data->dbPassword, $_POST['email'], $_POST['password'])) {
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
