<!DOCTYPE html>
<html lang="en">

<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/../scripts/globals.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/../scripts/user-getters.php');

if (!isUserLoggedIn(true)) {
    header('Location: /login.php');
}

if (!isset($_GET['query'])) {
    $_GET['query'] = "";
}

$file = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/../setup.json');
$data = json_decode($file, false);

$conn = new mysqli("localhost", $data->dbName, $data->dbPassword, $data->dbUserName);

?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Roddit</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato">
    <link rel="stylesheet" href="assets/css/Navbar-Centered-Brand-icons.css">
    <link rel="stylesheet" href="assets/css/index.css">
    <link rel="stylesheet" href="assets/css/post.css">
    <link rel="stylesheet" href="assets/css/icon-colors.css">
    <link rel="icon" type="image/x-icon" href="fav.ico">
</head>

<body>
    <nav class="navbar navbar-light navbar-expand-md py-3">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img onclick="window.location='/';" src="fav.ico" alt="" width="50px">
                <span onclick="window.location='/';">Roddit</span>
            </a>
            <button data-bs-toggle="collapse" class="navbar-toggler" data-bs-target="#navcol-4">
                <span class="visually-hidden">Toggle navigation</span>
                <span class="navbar-toggler-icon">
                </span>
            </button>
            <div class="collapse navbar-collapse flex-grow-0 order-md-first" id="navcol-4">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <form method="get"><input value="<?= $_GET['query'] ?>" class="form-control" type="search" id="search" name="query" placeholder="Search" autocomplete="off"></form>
                    </li>
                </ul>
                <div class="d-md-none my-2">
                </div>
            </div>
            <div>
                <div class="dropdown" style="width: fit-content;">
                    <ul class="dropdown-menu" style="margin-top: 37px; max-height: 350px; overflow-y: scroll;" id="notification-list">
                    </ul>
                </div>
                <button id="noti-drop" class="btn btn-primary dropdown-toggle position-relative" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="position-absolute top-0 start-100 translate-middle p-2 bg-danger border border-light rounded-circle">
                    </span>

                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-bell-fill" viewBox="0 0 16 16">
                        <path d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2zm.995-14.901a1 1 0 1 0-1.99 0A5.002 5.002 0 0 0 3 6c0 1.098-.5 6-2 7h14c-1.5-1-2-5.902-2-7 0-2.42-1.72-4.44-4.005-4.901z"/>
                    </svg>
                </button>
                
                <a class="btn btn-primary" href="profile.php" role="button">My Profile</a>
            </div>
        </div>
    </nav>

    <div class="container" id="posts-container">
        
    </div>

    <div id="modal-comment" class="modal fade" >
        <div class="modal-lg modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Comments</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div id="modal-comment-body" class="modal-body">
            <div style="overflow-y: scroll; max-height: 800px;">
           </div>
           <form class="row row-cols-lg-auto g-3 align-items-center" style="margin-top: 25px;">
                <input type="hidden" id="post-id-for-comment" name="post-id">
                <div class="mb-3">
                    <label for="comment-textarea" class="form-label">Comment</label>
                    <textarea class="form-control" style="width: 500px;" id="comment-textarea" rows="3"></textarea>
                </div>

                <div class="col-12">
                    <button id="send-comment" class="btn btn-success">Commenta</button>
                </div>
            </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
            </div>
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
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.3.0/jquery.form.min.js" integrity="sha384-qlmct0AOBiA2VPZkMY3+2WqkHtIQ9lSdAsAn5RUJD/3vA5MKDgSGcdmIv4ycVxyn" crossorigin="anonymous"></script>
    <script src="assets/js/btn-ajax-form.js"></script>
    <script src="assets/js/comments-ajax.js"></script>
    <script src="assets/js/post-loader.js"></script>
    <script src="assets/js/noti-ajax.js"></script>
</body>

</html>
