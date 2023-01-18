<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/profile/globals.php');
session_start();

$err = false;
$text_err = "";

$data = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'].'/../setup.json'));

$conn = new mysqli("localhost", $data->dbName, $data->dbPassword, $data->dbUserName);
$stmt = $conn->prepare("SELECT ProfileImagePath FROM `users` WHERE `ID` = ?");
$stmt->bind_param("i", $_SESSION['userID']);
$stmt->execute();

$result = $stmt->get_result();
$row = $result->fetch_assoc();
$photoPath = $row['ProfileImagePath'];

$stmt->close();
$conn->close();



if (isset($_POST['new-email']) && !empty($_POST['new-email'])) {

    if (filter_var($_POST['new-email'], FILTER_VALIDATE_EMAIL)) {

        $new_email = htmlspecialchars($_POST['new-email'], ENT_QUOTES, 'UTF-8');
        $data = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'].'/../setup.json'));
        
        $conn = new mysqli("localhost", $data->dbName, $data->dbPassword, $data->dbUserName);
        $stmt = $conn->prepare("UPDATE `users` SET `Email` = ? WHERE `ID` = ?");
        $stmt->bind_param("si", $new_email, $_SESSION['userID']);
        $stmt->execute();
        $stmt->close();
        $conn->close();
    }

    else {
        $err = true;
        $text_err = "Email provided is not a valid email";
    }
}

if (isset($_POST['new-nickname']) && !empty($_POST['new-nickname'])) {
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
        $stmt->bind_param("si", $new_nickname, $_SESSION['userID']);
        $stmt->execute();
        
        $stmt->close();
        $conn->close();
    }
}

if (isset($_POST['new-password']) && $_POST['new-password'] != "" && isset($_POST['confirm-new-pass']) && $_POST['confirm-new-pass'] != "") {
    
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
        $stmt->bind_param("ssi", $password, $salt, $_SESSION['userID']);
        $stmt->execute();
        
        $stmt->close();
        $conn->close();
    }
}

if (isset($_POST['new-biography']) && !empty($_POST['new-biography'])) {
    $new_biography = htmlspecialchars($_POST['new-biography'], ENT_QUOTES, 'UTF-8');
    $data = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'].'/../setup.json'));
    
    $conn = new mysqli("localhost", $data->dbName, $data->dbPassword, $data->dbUserName);
    $stmt = $conn->prepare("UPDATE `users` SET `Bio` = ? WHERE `ID` = ?");
    $stmt->bind_param("si", $new_biography, $_SESSION['userID']);
    $stmt->execute();
    
    $stmt->close();
    $conn->close();
}

if (isset($_FILES['new-photo']) && file_exists($_FILES['new-photo']['tmp_name']) && is_uploaded_file($_FILES['new-photo']['tmp_name'])) {
    $check = getimagesize($_FILES['new-photo']['tmp_name']);
    if ($check !== false) {
        $path = saveImage($_FILES['new-photo']);
        if ($path != false) {
            $data = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'].'/../setup.json'));
        
            $conn = new mysqli("localhost", $data->dbName, $data->dbPassword, $data->dbUserName);
            $stmt = $conn->prepare("UPDATE `users` SET `ProfileImagePath` = ? WHERE `ID` = ?");
            $stmt->bind_param("si", $path, $_SESSION['userID']);
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
    <link rel="stylesheet" href="assets/css/settings.css">
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col">
                <h1 class="text-center">Settings</h1>
            </div>
        </div>
        <?php if ($err) { ?>
        <div class="row">
            <div class="col">
                <div class="alert alert-danger text-center" role="alert"><span><?= $text_err ?></span></div>
            </div>
        </div>
        <?php } ?>
        <div class="row">
            <div class="col-md-6">
                <img src="<?= $photoPath ?>" style="max-width: 300px;" >
                <!-- Mostrare i dati attuali -->
            </div>
            <div class="col-md-6">
                <form method="post" autocomplete="off">
                    <div class="row setting-row">
                        <div class="col">
                            <label class="form-label">Change Email</label>
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
                            <textarea class="form-control" type="text" placeholder="Biography" name="new-biography"></textarea>
                        </div>
                    </div>
                    <div class="row setting-row">
                        <div class="col">
                            <label class="form-label">Change Password</label>
                            <input class="form-control" type="password" name="new-password" placeholder="New Password">
                            <input class="form-control" type="password" name="confirm-new-pass" placeholder="Confirm new password" style="margin-top: 20px;">
                        </div>
                    </div>
                    <div class="row setting-row">
                        <div class="col">
                            <label class="form-label">Change Profile Image</label>
                            <input class="form-control" type="file" name="new-photo">
                        </div>
                    </div>
                    <button class="btn btn-primary" type="submit" style="margin-top: 20px;">Update</button>
                </form>
            </div>
        </div>
    </div>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
</body>

</html>