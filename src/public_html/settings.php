<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/profile/globals.php');

$err = false;
$text_err = "";

if (isset($_POST['new-email'])) {
    
    
    if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $new_email = htmlspecialchars($_POST['new-email'], ENT_QUOTES, 'UTF-8');
        $data = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'].'/../setup.json'));
        
        $conn = new mysqli("localhost", $data->dbName, $data->dbPassword, $data->dbUserName);
        $stmt = $conn->prepare("UPDATE `users` SET `Email` = ? WHERE `ID` = ?");
        $stmt->bind_param("si", $new_email, $_SESSION['UserID']);
        $stmt->execute();
        
        $stmt->close();
        $conn->close();
    }

    else {
        $err = true;
        $text_err = "Email provided is not a valid email";
    }
}

if (isset($_POST['new-nickname'])) {
    if (strlen($_POST['new-nickname']) > 64) {
        $err = true;
        $text_err = "Nickname provided is too long, (more than 64 characters)";
    }

    if (!preg_match("/[0-9a-z_]/", $_POST['new-nickname'])) {
        $err = true;
        $text_err = "Nickname provided is not valid, (only numbers, letters and underscore)";
    }

    if ( !$err ) {
        $new_nickname = htmlspecialchars($_POST['new-nickname'], ENT_QUOTES, 'UTF-8');
        $data = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'].'/../setup.json'));
        
        $conn = new mysqli("localhost", $data->dbName, $data->dbPassword, $data->dbUserName);
        $stmt = $conn->prepare("UPDATE `users` SET `Nickname` = ? WHERE `ID` = ?");
        $stmt->bind_param("si", $new_nickname, $_SESSION['UserID']);
        $stmt->execute();
        
        $stmt->close();
        $conn->close();
    }
}

if (isset($_POST['new-password'])) {
    
    if ( $_POST['new-password'] != $_POST['confirm-new-pass']) {
        $err = true;
        $text_err = "Two passwords are different";
    }

    if ( !$err ) {
        $data = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'].'/../setup.json'));
        
        $salt = uniqid();
        $password = password_hash(saltPass($password, $salt), PASSWORD_DEFAULT);
        
        $conn = new mysqli("localhost", $data->dbName, $data->dbPassword, $data->dbUserName);
        $stmt = $conn->prepare("UPDATE `users` SET `Password` = ?, Salt = ? WHERE `ID` = ?");
        $stmt->bind_param("ssi", $new_password, $salt, $_SESSION['UserID']);
        $stmt->execute();
        
        $stmt->close();
        $conn->close();
    }
}

if (isset($_POST['new-biography'])) {
    $new_biography = htmlspecialchars($_POST['new-biography'], ENT_QUOTES, 'UTF-8');
    $data = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'].'/../setup.json'));
    
    $conn = new mysqli("localhost", $data->dbName, $data->dbPassword, $data->dbUserName);
    $stmt = $conn->prepare("UPDATE `users` SET `Bio` = ? WHERE `ID` = ?");
    $stmt->bind_param("si", $new_biography, $_SESSION['UserID']);
    $stmt->execute();
    
    $stmt->close();
    $conn->close();
}

if (file_exists($_FILES['new-photo']['tmp_name']) && is_uploaded_file($_FILES['new-photo']['tmp_name'])) {
    $check = getimagesize($_FILES['new-photo']['tmp_name']);
    if ($check !== false) {
        $path = saveImage($_FILES['new-photo']);
        if ($path != false) {
            $data = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'].'/../setup.json'));
        
            $conn = new mysqli("localhost", $data->dbName, $data->dbPassword, $data->dbUserName);
            $stmt = $conn->prepare("UPDATE `users` SET `Photo` = ? WHERE `ID` = ?");
            $stmt->bind_param("si", $path, $_SESSION['UserID']);
            $stmt->execute();
            
            $stmt->close();
            $conn->close();
        }
        
    }

}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Roddit</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato">
    <link rel="stylesheet" href="assets/css/settings.css"> <!-- importarlo in questa dir, rinominarlo -->
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col">
                <h1 class="text-center">Settings</h1>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="alert alert-danger text-center" role="alert" style="display: none;"><span></span></div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6"><img></div>
            <div class="col-md-6">
                <form>
                    <div class="row setting-row">
                        <div class="col">
                            <label class="form-label">Change email</label>
                            <input class="form-control" type="email" placeholder="New Email" name="new-email">
                        </div>
                    </div>
                    <div class="row setting-row">
                        <div class="col">
                            <label class="form-label">Change Nickname</label>
                            <input class="form-control" type="text" placeholder="New nickname" name="new-nickname">
                        </div>
                    </div>
                    <div class="row setting-row">
                        <div class="col">
                            <label class="form-label">Change Biography</label>
                            <textarea class="form-control" placeholder="Biography"></textarea>
                        </div>
                    </div>
                    <div class="row setting-row">
                        <div class="col">
                            <label class="form-label">Change password</label>
                            <input class="form-control" type="password" name="new-pass" placeholder="New Password">
                            <input class="form-control" type="password" name="confirm-new-pass" placeholder="Confirm new password" style="margin-top: 20px;">
                        </div>
                    </div>
                    <div class="row setting-row">
                        <div class="col">
                            <label class="form-label">Change Profile image</label>
                            <input class="form-control" type="file">
                        </div>
                    </div><button class="btn btn-primary" type="button" style="margin-top: 20px;">Update</button>
                </form>
            </div>
        </div>
    </div>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
</body>

</html>