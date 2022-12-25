<?php

function drawPost($title, $text, $likes, $comments, $pathToImage = null) {
?>
<div class="row">
    <div class="col">
        <div class="card" style="margin-top: 25px;">
            <div class="card-body" style="margin-top: 0px;">
                <h4 class="card-title"><?php echo($title) ?></h4>
                <p class="card-text"> <?php echo($text); ?> </p>
                <?php
                if ($pathToImage) {
                ?>
                    <img src=' <?php echo($pathToImage); ?> ' class='card-img-top post-img img-fluid' alt='...'>
                <?php
                }
                ?>
                <a class="card-link" href="#"> <?php echo($likes); ?> Likes</a> <a class="card-link" href="#">Comments</a>
            </div>
        </div>
    </div>
</div>
<?php
}

?>
