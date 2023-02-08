<?php
$err = false;
$text_err = "";

if ( isset($_POST['first']) && (!isset($_POST['privacy-policy']) || $_POST['privacy-policy'] != "accept")) {
    $err = true;
    $text_err = "You must accept the privacy policy";
}

if ( isset($_POST['first']) && (!isset($_POST['terms-conditions']) || $_POST['terms-conditions'] != "accept")){
    $err = true;
    $text_err = "You must accept the terms and conditions";
}

if ( ! $err && isset($_POST['first'])) {
    if (isset($_POST['nickname']) && isset($_POST['email']) && isset($_POST['password']) && isset($_POST['pass_conf'])) {

        $nickname = $_POST['nickname'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $pass_conf = $_POST['pass_conf'];

        if ( $password != $pass_conf) {
            $err = true;
            $text_err = "Two passwords are different";
        }

        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $err = true;
            $text_err = "Email provided is not a valid email";
        }

        if (strlen($_POST['nickname']) > 64) {
            $err = true;
            $text_err = "Nickname provided is too long, (more than 64 characters)";
        }

        if (!preg_match("/[0-9a-z_]/", $_POST['nickname'])) {
            $err = true;
            $text_err = "Nickname provided is not valid, (only numbers, letters and underscore)";
        }

        if ( !$err ) {
            include_once($_SERVER['DOCUMENT_ROOT'].'../scripts/globals.php');
            $data = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'].'/../setup.json'));
            
            $conn = new mysqli("localhost", $data->dbName, $data->dbPassword, $data->dbUserName);
        
            $nickname = htmlspecialchars($nickname, ENT_QUOTES, 'UTF-8');
            $email = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
            
            $salt = uniqid();
            $password = password_hash(saltPass($password, $salt), PASSWORD_DEFAULT);
            
            $stmt = $conn->prepare("INSERT INTO `users` (`Nickname`, `Email`, `Password`, `Salt`) VALUE (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $nickname, $email, $password, $salt);
            $stmt->execute();

            $stmt->close();
            $conn->close();
            
            header("Location: /login.php");

        }
    }

    else {
        $err = true;
        $text_err = "You must fill all the fields";
    }
}

if (!isset($_POST['nickname'])){
    $_POST['nickname'] = "";
}

if (!isset($_POST['email'])){
    $_POST['email'] = "";
}

if (!isset($_POST['password'])){
    $_POST['password'] = "";
}

if (!isset($_POST['pass_conf'])){
    $_POST['pass_conf'] = "";
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
    <link rel="stylesheet" href="assets/css/index.css">
    <link rel="stylesheet" href="assets/css/Navbar-Centered-Brand-icons.css">
    <link rel="stylesheet" href="assets/css/register.css">
    <link rel="icon" type="image/x-icon" href="fav.ico">
</head>

<body>
    <nav class="navbar navbar-light navbar-expand-md py-3">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="fav.ico" alt="" width="50px">
                <span onclick="window.location='/';">Roddit</span>
            </a>
            <div class="collapse navbar-collapse flex-grow-0 order-md-first" id="navcol-4">
                <div class="d-none d-md-block">
                </div>
            </div>
            <div class="d-none d-md-block">
            </div>
        </div>
    </nav>
    <div class="container">
        <?php if ($err) { ?>
            <div class="row">
                <div class="col">
                    <div class="alert alert-danger text-center" role="alert">
                    <span><?= $text_err ?></span>
                </div>
            </div>
        <?php } ?>
        </div>
        <div class="row">
            <div class="col">
                <h1 style="text-align: center;">New Account</h1>
            </div>
        </div>
        <div class="row d-xxl-flex">
            <div class="col d-md-flex d-lg-flex d-xl-flex d-xxl-flex justify-content-md-center justify-content-lg-center justify-content-xl-center justify-content-xxl-center">
                <form method="post">
                    <input class="form-control register" type="text" name="nickname" placeholder="Nickname" value="<?= $_POST['nickname']?>">
                    <input class="form-control register" type="email" name="email" placeholder="Email" value="<?= $_POST['email']?>">
                    <input class="form-control register" type="password" name="password" placeholder="Password" value="<?= $_POST['password']?>">
                    <input class="form-control register" type="password" name="pass_conf" placeholder="Confirm Password" value="<?= $_POST['pass_conf']?>">
                    <input class="form-control register" type="hidden" name="first" value="Data">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="privacy-policy" name="privacy-policy" value="accept">
                        <label class="form-check-label" for="formCheck-2">Accept privacy policy</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="terms-conditions" name="terms-conditions" value="accept">
                        <label class="form-check-label" for="formCheck-1">Accept terms and conditions</label>
                    </div><button class="btn btn-danger" id="submit" type="submit">Register</button>
                </form>
            </div>
        </div>
    </div>
    <footer class="text-center" style="margin-top: 40px;">
        <div class="container text-muted py-4 py-lg-5">
            <ul class="list-inline">
                <li class="list-inline-item me-4"><a class="link-secondary" href="#">Log in</a></li>
                <li class="list-inline-item me-4"><a class="link-secondary" href="#">Privacy Policy</a></li>
                <li class="list-inline-item"><a class="link-secondary" href="#">Terms &amp; Conditions</a></li>
            </ul>
            <p class="mb-0">Copyright © 2022 Roddit</p>
        </div>
    </footer>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
</body>

</html>