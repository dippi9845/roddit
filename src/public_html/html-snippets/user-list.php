<?php
function drawUserList($title, $users) {
?>
    <div class="modal fade" id="<?= $title ?>" tabindex="-1" role="dialog" aria-labelledby="<?= $title ?>Title" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="<?= $title ?>Title"> <?= $title; ?> </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="list-group">
                    <?php foreach($users as $user) { ?>
                    <a href="<?php echo(parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH)."?user=$user[ID]") ?>" class="list-group-item list-group-item-action"><?php echo($user['Nickname']); ?></a>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    </div>
<?php
}
?>
