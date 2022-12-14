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
</head>

<body>
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
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
</body>

</html>
