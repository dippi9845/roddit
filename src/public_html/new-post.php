<!DOCTYPE html>
<html lang="en">

<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/profile/globals.php');

if (!isUserLoggedIn(true)) {
    header('Location: /login.php');
}
?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Roddit</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato">
    <link rel="stylesheet" href="assets/css/new-post.css">
    <link rel="icon" type="image/x-icon" href="fav.ico">
</head>

<body>
<nav class="navbar navbar-light navbar-expand-md py-3">
        <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="fav.ico" alt="" width="50px">
                <span onclick="window.location='/';">Roddit</span>
            </a>
            <button data-bs-toggle="collapse" class="navbar-toggler" data-bs-target="#navcol-4">
                <span class="visually-hidden">Toggle navigation</span>
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse flex-grow-0 order-md-first" id="navcol-4">
                <div class="d-md-none my-2">
                    <a class="btn btn-light me-2" href="profile.php">My Profile</a>
                </div>
            </div>
            <div class="d-none d-md-block">
                <a class="btn btn-light me-2" href="profile.php">My Profile</a>
            </div>
        </div>
    </nav>
    <div class="container">
        <div class="row">
            <div class="col">
                <h1 class="text-center">Create new post</h1>
                <form class="mx-auto" action="/profile/create-new-post.php" method="post" enctype="multipart/form-data">
                    <input class="form-control form-element font-weight-bold bold-input my-2" type="text" name="title" placeholder="Post Title" required />
                    <textarea class="form-control form-element my-2" name="text" placeholder="Post Text" required></textarea>
                    <input class="form-control form-element my-2" name="file" type="file" accept="image/*" />
                    <button class="btn btn-primary form-element my-2" type="submit">Create post</button>
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
