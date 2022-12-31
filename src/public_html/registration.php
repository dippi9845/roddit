<?php
$err = false;
$text_err = "";

if (!isset($_POST['privacy-policy']) || $_POST['privacy-policy'] != "accept") {
    $err = true;
    $text_err = "You must accept the privacy policy";
}

if (!isset($_POST['terms-conditions']) || $_POST['terms-conditions'] != "accept") {
    $err = true;
    $text_err = "You must accept the terms and conditions";
}

if ( ! $err ) {
    if (isset($_POST['nickname']) && isset($_POST['email']) && isset($_POST['password']) && isset($_POST['pass_conf'])) {

        $nickname = $_POST['nickname'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $pass_conf = $_POST['pass_conf'];
        
        $salt = uniqid();
        $passw = password_hash($_POST['password']."Sono Bello".$salt, PASSWORD_DEFAULT);
        $conf = password_hash($_POST['pass_conf']."Sono Bello".$salt, PASSWORD_DEFAULT);

        if ( $passw != $conf) {
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
            include_once($_SERVER['DOCUMENT_ROOT'].'/profile/globals.php');
            $data = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'].'/../setup.json'));
            
            $conn = new mysqli("localhost", $data->dbName, $data->dbPassword, $data->dbUserName);
        
            $nickname = htmlspecialchars($_POST['nickname'], ENT_QUOTES, 'UTF-8');
            $email = htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8');
            $password = htmlspecialchars($_POST['password'], ENT_QUOTES, 'UTF-8');
            
            $salt = uniqid();
            $passw = saltPass($_POST['password'], $salt);
            
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
</head>

<body>
    <div class="container">
        <nav class="navbar navbar-light navbar-expand-md py-3">
            <div class="container"><a class="navbar-brand d-flex align-items-center" href="#"><span class="bs-icon-sm bs-icon-rounded bs-icon-primary d-flex justify-content-center align-items-center me-2 bs-icon"><svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor" viewBox="0 0 16 16" class="bi bi-bezier">
                            <path fill-rule="evenodd" d="M0 10.5A1.5 1.5 0 0 1 1.5 9h1A1.5 1.5 0 0 1 4 10.5v1A1.5 1.5 0 0 1 2.5 13h-1A1.5 1.5 0 0 1 0 11.5v-1zm1.5-.5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-1zm10.5.5A1.5 1.5 0 0 1 13.5 9h1a1.5 1.5 0 0 1 1.5 1.5v1a1.5 1.5 0 0 1-1.5 1.5h-1a1.5 1.5 0 0 1-1.5-1.5v-1zm1.5-.5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-1zM6 4.5A1.5 1.5 0 0 1 7.5 3h1A1.5 1.5 0 0 1 10 4.5v1A1.5 1.5 0 0 1 8.5 7h-1A1.5 1.5 0 0 1 6 5.5v-1zM7.5 4a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-1z"></path>
                            <path d="M6 4.5H1.866a1 1 0 1 0 0 1h2.668A6.517 6.517 0 0 0 1.814 9H2.5c.123 0 .244.015.358.043a5.517 5.517 0 0 1 3.185-3.185A1.503 1.503 0 0 1 6 5.5v-1zm3.957 1.358A1.5 1.5 0 0 0 10 5.5v-1h4.134a1 1 0 1 1 0 1h-2.668a6.517 6.517 0 0 1 2.72 3.5H13.5c-.123 0-.243.015-.358.043a5.517 5.517 0 0 0-3.185-3.185z"></path>
                        </svg></span><span>Brand</span></a>
                <div class="d-none d-md-block"></div>
            </div>
        </nav>
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
            <!-- 
            <ul class="list-inline">
                <li class="list-inline-item me-4"><svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor" viewBox="0 0 16 16" class="bi bi-facebook">
                        <path d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951z"></path>
                    </svg></li>
                <li class="list-inline-item me-4"><svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor" viewBox="0 0 16 16" class="bi bi-twitter">
                        <path d="M5.026 15c6.038 0 9.341-5.003 9.341-9.334 0-.14 0-.282-.006-.422A6.685 6.685 0 0 0 16 3.542a6.658 6.658 0 0 1-1.889.518 3.301 3.301 0 0 0 1.447-1.817 6.533 6.533 0 0 1-2.087.793A3.286 3.286 0 0 0 7.875 6.03a9.325 9.325 0 0 1-6.767-3.429 3.289 3.289 0 0 0 1.018 4.382A3.323 3.323 0 0 1 .64 6.575v.045a3.288 3.288 0 0 0 2.632 3.218 3.203 3.203 0 0 1-.865.115 3.23 3.23 0 0 1-.614-.057 3.283 3.283 0 0 0 3.067 2.277A6.588 6.588 0 0 1 .78 13.58a6.32 6.32 0 0 1-.78-.045A9.344 9.344 0 0 0 5.026 15z"></path>
                    </svg></li>
                <li class="list-inline-item"><svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor" viewBox="0 0 16 16" class="bi bi-instagram">
                        <path d="M8 0C5.829 0 5.556.01 4.703.048 3.85.088 3.269.222 2.76.42a3.917 3.917 0 0 0-1.417.923A3.927 3.927 0 0 0 .42 2.76C.222 3.268.087 3.85.048 4.7.01 5.555 0 5.827 0 8.001c0 2.172.01 2.444.048 3.297.04.852.174 1.433.372 1.942.205.526.478.972.923 1.417.444.445.89.719 1.416.923.51.198 1.09.333 1.942.372C5.555 15.99 5.827 16 8 16s2.444-.01 3.298-.048c.851-.04 1.434-.174 1.943-.372a3.916 3.916 0 0 0 1.416-.923c.445-.445.718-.891.923-1.417.197-.509.332-1.09.372-1.942C15.99 10.445 16 10.173 16 8s-.01-2.445-.048-3.299c-.04-.851-.175-1.433-.372-1.941a3.926 3.926 0 0 0-.923-1.417A3.911 3.911 0 0 0 13.24.42c-.51-.198-1.092-.333-1.943-.372C10.443.01 10.172 0 7.998 0h.003zm-.717 1.442h.718c2.136 0 2.389.007 3.232.046.78.035 1.204.166 1.486.275.373.145.64.319.92.599.28.28.453.546.598.92.11.281.24.705.275 1.485.039.843.047 1.096.047 3.231s-.008 2.389-.047 3.232c-.035.78-.166 1.203-.275 1.485a2.47 2.47 0 0 1-.599.919c-.28.28-.546.453-.92.598-.28.11-.704.24-1.485.276-.843.038-1.096.047-3.232.047s-2.39-.009-3.233-.047c-.78-.036-1.203-.166-1.485-.276a2.478 2.478 0 0 1-.92-.598 2.48 2.48 0 0 1-.6-.92c-.109-.281-.24-.705-.275-1.485-.038-.843-.046-1.096-.046-3.233 0-2.136.008-2.388.046-3.231.036-.78.166-1.204.276-1.486.145-.373.319-.64.599-.92.28-.28.546-.453.92-.598.282-.11.705-.24 1.485-.276.738-.034 1.024-.044 2.515-.045v.002zm4.988 1.328a.96.96 0 1 0 0 1.92.96.96 0 0 0 0-1.92zm-4.27 1.122a4.109 4.109 0 1 0 0 8.217 4.109 4.109 0 0 0 0-8.217zm0 1.441a2.667 2.667 0 1 1 0 5.334 2.667 2.667 0 0 1 0-5.334z"></path>
                    </svg></li>
            </ul>
            -->
            <p class="mb-0">Copyright © 2022 Brand</p>
        </div>
    </footer>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
</body>

</html>