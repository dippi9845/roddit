<?php
/**
 * Draws a modal with a list of users
 * @param string $title The title of the modal
 * @param array $users The list of users to display
 */
function drawUserList($title, $users) {
?>
    <div class="modal fade" id="<?= $title ?>" tabindex="-1" role="dialog" aria-labelledby="<?= $title ?>Title" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="<?= $title ?>Title"> <?= $title; ?> </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
