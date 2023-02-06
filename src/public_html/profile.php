<!DOCTYPE html>
<html lang="en">

<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/profile/globals.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/profile/post-handling.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/profile/user-getters.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/html-snippets/post.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/html-snippets/user-list.php');

if (!isUserLoggedIn(true)) {
    header('Location: /login.php');
}

$file = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/../setup.json');
$data = json_decode($file, false);

if (isset($_GET['user'])) {
    $visitedUser = $_GET['user'];
} else {
    $visitedUser = $_SESSION['userID'];
}

$conn = new mysqli("localhost", $data->dbName, $data->dbPassword, $data->dbUserName);

if (!userExists($conn, $visitedUser)) {
    header('Location: /404.html');
}
?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Roddit</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato">
    <link rel="stylesheet" href="assets/css/Navbar-Centered-Brand-icons.css">
    <link rel="stylesheet" href="assets/css/post.css">
    <link rel="stylesheet" href="assets/css/profile.css">
    <link rel="stylesheet" href="assets/css/icon-colors.css">
    <style>
        .modal {
            position: absolute;
            float: left;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-light navbar-expand-md py-3">
        <div class="container"><a class="navbar-brand d-flex align-items-center" href="#"><span class="bs-icon-sm bs-icon-rounded bs-icon-primary d-flex justify-content-center align-items-center me-2 bs-icon"><svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor" viewBox="0 0 16 16" class="bi bi-bezier">
                        <path fill-rule="evenodd" d="M0 10.5A1.5 1.5 0 0 1 1.5 9h1A1.5 1.5 0 0 1 4 10.5v1A1.5 1.5 0 0 1 2.5 13h-1A1.5 1.5 0 0 1 0 11.5v-1zm1.5-.5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-1zm10.5.5A1.5 1.5 0 0 1 13.5 9h1a1.5 1.5 0 0 1 1.5 1.5v1a1.5 1.5 0 0 1-1.5 1.5h-1a1.5 1.5 0 0 1-1.5-1.5v-1zm1.5-.5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-1zM6 4.5A1.5 1.5 0 0 1 7.5 3h1A1.5 1.5 0 0 1 10 4.5v1A1.5 1.5 0 0 1 8.5 7h-1A1.5 1.5 0 0 1 6 5.5v-1zM7.5 4a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-1z"></path>
                        <path d="M6 4.5H1.866a1 1 0 1 0 0 1h2.668A6.517 6.517 0 0 0 1.814 9H2.5c.123 0 .244.015.358.043a5.517 5.517 0 0 1 3.185-3.185A1.503 1.503 0 0 1 6 5.5v-1zm3.957 1.358A1.5 1.5 0 0 0 10 5.5v-1h4.134a1 1 0 1 1 0 1h-2.668a6.517 6.517 0 0 1 2.72 3.5H13.5c-.123 0-.243.015-.358.043a5.517 5.517 0 0 0-3.185-3.185z"></path>
                    </svg></span><span onclick="window.location='/';">Roddit</span></a><button data-bs-toggle="collapse" class="navbar-toggler" data-bs-target="#navcol-4"><span class="visually-hidden">Toggle navigation</span><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse flex-grow-0 order-md-first" id="navcol-4">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link active" href="settings.php">Settings</a></li>
                </ul>
                <div class="d-md-none my-2"><button class="btn btn-light me-2" type="button">Button</button><button class="btn btn-primary" type="button">Button</button></div>
            </div>
            <div class="d-none d-md-block"><button onclick="window.location='new-post.php';" class="btn btn-light me-2" type="button">New Post</button><button class="btn btn-light me-2" type="button" onclick="window.location='profile/logout.php';">Log out</button></div>
        </div>
    </nav>
    
    <div class="container">
        <div class="row">
            <div class="col-4">
                <img src="<?= getUserProfilePicture($conn, $visitedUser) ?>" alt="..." class="img-thumbnail profile-picutre" >
            </div>
            <div class="col-8">
                <div class="row ms-auto">
                    <div class="col"><p><?= getUserNameByID($conn, $visitedUser) ?></p></div>
                    <div class="col">
                        <?php if ($visitedUser == $_SESSION['userID']) {
                        } elseif (isFollowing($conn, $visitedUser, $_SESSION['userID'])) { ?>
                            <form id="unfollow-form" action="profile/unfollow.php" method="post">
                                <input type="hidden" name="unfollowedUser" value="<?= $visitedUser ?>">
                                <button type="submit" name="unfollow-submit" class="btn btn-primary btn-ajax-form">Unfollow</button>
                            </form>
                        <?php } else { ?>
                            <form id="follow-form" action="profile/follow.php" method="post">
                                <input type="hidden" name="followedUser" value="<?= $visitedUser ?>">
                                <button type="submit" name="follow-submit" class="btn btn-primary btn-ajax-form">Follow</button>
                            </form>
                        <?php } ?>
                    </div>
                </div>
                <div class="row ms-auto my-1">
                    <div class="col">
                        <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#Followers"><b><?= getUserFollowerCount($conn, $visitedUser) ?></b> followers</button>
                        <?php drawUserList("Followers", getUserFollowers($conn, $visitedUser)) ?>
                    </div>
                    <div class="col">
                        <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#Following"><b><?= getUserFollowingCount($conn, $visitedUser) ?></b> following</button>
                        <?php drawUserList("Following", getFollowingUsers($conn, $visitedUser)) ?>
                    </div>
                </div>
                <div class="row ms-auto my-1">
                    <div class="col">Biografia</div>
                </div>
            </div>
        </div>
        <?php
        $posts = getUsersPosts($conn, $visitedUser);
        
        foreach ($posts as $post) {
            drawPost($post['ID'], $visitedUser, $post['Nickname'], getUserProfilePicture($conn, $visitedUser), $post['Title'], $post['Text'], $post['Likes'], isLiked($conn, $post['ID'], $_SESSION['userID']), $post['Comments'], $post['PathToFile']);
        }
        $conn->close();
        ?>
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
           <form class="row row-cols-lg-auto g-3 align-items-center" style="margin-top: 25px;" action="">
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
    
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.3.0/jquery.form.min.js" integrity="sha384-qlmct0AOBiA2VPZkMY3+2WqkHtIQ9lSdAsAn5RUJD/3vA5MKDgSGcdmIv4ycVxyn" crossorigin="anonymous"></script>
    <script src="assets/js/btn-ajax-form.js"></script>
    <script src="assets/js/comments-ajax.js"></script>
</body>

</html>
