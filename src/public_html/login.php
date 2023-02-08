<!DOCTYPE html>
<html lang="en">

<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/profile/globals.php');

if (isUserLoggedIn(true)) {
    header('Location: /index.php');
}

?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Roddit</title>
    <link rel="stylesheet" href="/assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/Navbar-Centered-Brand-icons.css">
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
    <section class="position-relative py-4 py-xl-5">
        <div class="container">
            <div class="row mb-5">
                <div class="col-md-8 col-xl-6 text-center mx-auto">
                    <h2>Log in</h2>
                </div>
            </div>
            <div class="row d-flex justify-content-center">
                <div class="col-md-6 col-xl-4">
                    <div class="card mb-5">
                        <div class="card-body d-flex flex-column align-items-center">
                            <div class="bs-icon-xl bs-icon-circle bs-icon-primary bs-icon my-4">
                                <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor" viewBox="0 0 16 16" class="bi bi-person">
                                    <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z"></path>
                                </svg>
                            </div>
                            <form class="text-center" action="/profile/login.php" method="post">
                                <div class="mb-3"><input class="form-control" type="email" name="email" placeholder="Email" required /></div>
                                <div class="mb-3"><input class="form-control" type="password" name="password" placeholder="Password" required /></div>
                                <div class="form-check"><input class="form-check-input" type="checkbox" name="remember" id="formCheck-1" /><label class="form-check-label d-xxl-flex justify-content-xxl-start" for="formCheck-1">Remeber me</label></div>
                                <div class="mb-3"><button class="btn btn-primary d-block w-100" type="submit">Login</button></div>
                                <div class="mb-3"><a class="link-dark" href="/registration.php">New here? Register</a></div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
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
    <script src="/assets/bootstrap/js/bootstrap.min.js"></script>
</body>

</html>
