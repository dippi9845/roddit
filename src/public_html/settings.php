<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/profile/globals.php');
session_start();

$err = false;
$text_err = "";

$data = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'].'/../setup.json'));

$conn = new mysqli("localhost", $data->dbName, $data->dbPassword, $data->dbUserName);

if (isset($_POST['new-email']) && !empty($_POST['new-email'])) {

    if (filter_var($_POST['new-email'], FILTER_VALIDATE_EMAIL)) {
        $new_email = htmlspecialchars($_POST['new-email'], ENT_QUOTES, 'UTF-8');
  
        $stmt = $conn->prepare("UPDATE `users` SET `Email` = ? WHERE `ID` = ?");
        $stmt->bind_param("si", $new_email, $_SESSION['userID']);
        $stmt->execute();
        $stmt->close();
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
        
        $stmt = $conn->prepare("UPDATE `users` SET `Nickname` = ? WHERE `ID` = ?");
        $stmt->bind_param("si", $new_nickname, $_SESSION['userID']);
        $stmt->execute();
        $stmt->close();
    }
}

if (isset($_POST['new-password']) && $_POST['new-password'] != "" && isset($_POST['confirm-new-pass']) && $_POST['confirm-new-pass'] != "") {
    
    if ( $_POST['new-password'] != $_POST['confirm-new-pass']) {
        $err = true;
        $text_err = "Two passwords are different";
    }

    if ( !$err ) {
        
        $salt = uniqid();
        $password = password_hash(saltPass($password, $salt), PASSWORD_DEFAULT);
        
        $stmt = $conn->prepare("UPDATE `users` SET `Password` = ?, Salt = ? WHERE `ID` = ?");
        $stmt->bind_param("ssi", $password, $salt, $_SESSION['userID']);
        $stmt->execute();
        
        $stmt->close();
    }
}

if (isset($_POST['new-biography']) && !empty($_POST['new-biography'])) {
    $new_biography = htmlspecialchars($_POST['new-biography'], ENT_QUOTES, 'UTF-8');
    
    $stmt = $conn->prepare("UPDATE `users` SET `Bio` = ? WHERE `ID` = ?");
    $stmt->bind_param("si", $new_biography, $_SESSION['userID']);
    $stmt->execute();
    
    $stmt->close();
}

if (isset($_FILES['new-photo']) && file_exists($_FILES['new-photo']['tmp_name']) && is_uploaded_file($_FILES['new-photo']['tmp_name'])) {
    $check = getimagesize($_FILES['new-photo']['tmp_name']);
    if ($check !== false) {
        $path = saveImage($_FILES['new-photo']);
        if ($path != false) {
        
            $stmt = $conn->prepare("UPDATE `users` SET `ProfileImagePath` = ? WHERE `ID` = ?");
            $stmt->bind_param("si", $path, $_SESSION['userID']);
            $stmt->execute();
            
            $stmt->close();
        }
        
    }

}

$stmt = $conn->prepare("SELECT ProfileImagePath, Email, Nickname, Bio FROM `users` WHERE `ID` = ?");
$stmt->bind_param("i", $_SESSION['userID']);
$stmt->execute();

$result = $stmt->get_result();
$row = $result->fetch_assoc();
$photoPath = $row['ProfileImagePath'];

$stmt->close();

$conn->close();
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
    <nav class="navbar navbar-light navbar-expand-md py-3">
        <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="#">
                <span class="bs-icon-sm bs-icon-rounded bs-icon-primary d-flex justify-content-center align-items-center me-2 bs-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor" viewBox="0 0 16 16" class="bi bi-bezier">
                        <path fill-rule="evenodd" d="M0 10.5A1.5 1.5 0 0 1 1.5 9h1A1.5 1.5 0 0 1 4 10.5v1A1.5 1.5 0 0 1 2.5 13h-1A1.5 1.5 0 0 1 0 11.5v-1zm1.5-.5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-1zm10.5.5A1.5 1.5 0 0 1 13.5 9h1a1.5 1.5 0 0 1 1.5 1.5v1a1.5 1.5 0 0 1-1.5 1.5h-1a1.5 1.5 0 0 1-1.5-1.5v-1zm1.5-.5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-1zM6 4.5A1.5 1.5 0 0 1 7.5 3h1A1.5 1.5 0 0 1 10 4.5v1A1.5 1.5 0 0 1 8.5 7h-1A1.5 1.5 0 0 1 6 5.5v-1zM7.5 4a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-1z"></path>
                        <path d="M6 4.5H1.866a1 1 0 1 0 0 1h2.668A6.517 6.517 0 0 0 1.814 9H2.5c.123 0 .244.015.358.043a5.517 5.517 0 0 1 3.185-3.185A1.503 1.503 0 0 1 6 5.5v-1zm3.957 1.358A1.5 1.5 0 0 0 10 5.5v-1h4.134a1 1 0 1 1 0 1h-2.668a6.517 6.517 0 0 1 2.72 3.5H13.5c-.123 0-.243.015-.358.043a5.517 5.517 0 0 0-3.185-3.185z"></path>
                    </svg>
                </span>
                <span onclick="window.location='/';">Roddit</span>
            </a>
            <button data-bs-toggle="collapse" class="navbar-toggler" data-bs-target="#navcol-4">
                <span class="visually-hidden">Toggle navigation</span>
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse flex-grow-0 order-md-first" id="navcol-4">
                <div class="d-none d-md-block">
                    <a class="btn btn-light me-2" href="profile.php">My Profile</a>
                </div>
                <div class="d-md-none my-2"><button class="btn btn-light me-2" type="button">Button</button><button class="btn btn-primary" type="button">Button</button></div>
            </div>
            <div class="d-none d-md-block">
                <button onclick="window.location='new-post.php';" class="btn btn-light me-2" type="button">New Post</button>
                <button class="btn btn-light me-2" type="button" onclick="window.location='profile/logout.php';">Log out</button>
            </div>
        </div>
    </nav>

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
                <p>
                    <strong>Email: </strong><?= $row['Email'] ?> <br>
                    <strong>Nickname: </strong><?= $row['Nickname'] ?> <br>
                    <strong>Biography: </strong><?= $row['Bio'] ?> <br>
                </p>
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