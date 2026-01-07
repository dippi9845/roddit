<?php
function drawUserCard($userID, $userName, $userProfilePicture, $isFollowing, $followersCount, $followingCount) {
?>

    <div class="d-flex justify-content-start">
        <div class="p-2"><a href="<?= "/profile.php?user=$userID" ?>"><img src="<?= $userProfilePicture ?>" alt="..." class="img-thumbnail card-user-picutre"></a></div>
        <div class="p-2">
            <div class="row mx-4"><a href="<?= "/profile.php?user=$userID" ?>" class="link-dark text-decoration-none"><?= $userName ?></a></div>
            <div class="row my-4 mx-4"><p><b><?= $followersCount ?></b> followers &emsp; <b><?= $followingCount ?></b> following</p></div>
        </div>
        <div class="p-2">
            <?php if ($userID == $_SESSION['userID']) {
            } elseif ($isFollowing) { ?>
                <form id="unfollow-form" action="ajax/unfollow.php" method="post">
                    <input type="hidden" name="unfollowedUser" value="<?= $userID ?>">
                    <button type="submit" name="unfollow-submit" class="btn btn-primary btn-ajax-form">Unfollow</button>
                </form>
            <?php } else { ?>
                <form id="follow-form" action="ajax/follow.php" method="post">
                    <input type="hidden" name="followedUser" value="<?= $userID ?>">
                    <button type="submit" name="follow-submit" class="btn btn-primary btn-ajax-form">Follow</button>
                </form>
            <?php } ?>
        </div>
    </div>
<?php
}
?>
