<?php
function drawPost($postID, $creatorID, $creatorName, $creatorProfilePicture, $title, $text, $likes, $isLikedByMe, $comments, $pathToImage = null) {
?>
<div class="row">
    <div class="col">
        <div class="card" style="margin-top: 25px;">
            <div class="card-body" style="margin-top: 0px;">
            
                <div class="d-flex flex-row">
                    <div class="p-2"><a href="<?= "/profile.php?user=$creatorID" ?>"><img src="<?= $creatorProfilePicture ?>" alt="..." class="img-thumbnail post-profile-picutre"></a></div>
                    <div class="p-2"><a href="<?= "/profile.php?user=$creatorID" ?>" class="link-dark text-decoration-none"><?= $creatorName ?></a></div>
                </div>
                
                <h4 class="card-title"><?= $title ?></h4>
                <p class="card-text"> <?= $text ?> </p>
                <?php
                if ($pathToImage) {
                ?>
                    <img src=' <?= $pathToImage ?> ' class='card-img-top post-img img-fluid' alt='...'>
                <?php } ?>
                
                <div class="d-flex p-2">
                    <?php if ($isLikedByMe) {?>
                        <form id="post-dislike-form-<?= $postID ?>" action="profile/dislike-post.php" method="post">
                            <input type="hidden" name="postID" value="<?= $postID ?>">
                            <button type="submit" class="btn btn-light btn-ajax-form">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" class="bi bi-heart-fill red-heart" viewBox="0 0 16 16">
                                    <path d="M8 1.314C12.438-3.248 23.534 4.735 8 15-7.534 4.736 3.562-3.248 8 1.314z"></path>
                                </svg><?= " ".$likes ?>
                            </button>
                        </form>
                    <?php } else { ?>
                        <form id="post-like-form-<?= $postID ?>" action="profile/like-post.php" method="post">
                            <input type="hidden" name="postID" value="<?= $postID ?>">
                            <button type="submit" class="btn btn-light btn-ajax-form">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-heart" viewBox="0 0 16 16">
                                    <path d="m8 2.748-.717-.737C5.6.281 2.514.878 1.4 3.053c-.523 1.023-.641 2.5.314 4.385.92 1.815 2.834 3.989 6.286 6.357 3.452-2.368 5.365-4.542 6.286-6.357.955-1.886.838-3.362.314-4.385C13.486.878 10.4.28 8.717 2.01L8 2.748zM8 15C-7.333 4.868 3.279-3.04 7.824 1.143c.06.055.119.112.176.171a3.12 3.12 0 0 1 .176-.17C12.72-3.042 23.333 4.867 8 15z"/>
                                </svg><?= " ".$likes ?>
                        </button>
                        </form>
                    <?php } ?>
                    <a class="card-link" href="#">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chat-fill" viewBox="0 0 16 16">
                        <path d="M8 15c4.418 0 8-3.134 8-7s-3.582-7-8-7-8 3.134-8 7c0 1.76.743 3.37 1.97 4.6-.097 1.016-.417 2.13-.771 2.966-.079.186.074.394.273.362 2.256-.37 3.597-.938 4.18-1.234A9.06 9.06 0 0 0 8 15z"/>
                        </svg></a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
}
?>
