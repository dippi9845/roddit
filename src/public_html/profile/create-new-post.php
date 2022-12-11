<?php

function isFormValid() {
    return isset($_POST['title']);
}
function main($data) {
    if (!isFormValid()) {
        echo("<br/>Invalid form");
        return false;
    }
}
include_once($_SERVER['DOCUMENT_ROOT'].'/profile/globals.php');

$file = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/setup.json');
$data = json_decode($file, false);

main($data);

?>
