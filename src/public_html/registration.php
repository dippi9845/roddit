<?php

if (!isset($_POST['nickname'])) {
    die('{"Errore" : "No nickname provided"}');
}

if (!isset($_POST['email'])) {
    die('{"Errore" : "No email provided"}');
}

if (!isset($_POST['password'])) {
    die('{"Errore" : "No password provided"}');
}

if (!isset($_POST['pass_conf'])) {
    die('{"Errore" : "No nickanme provided"}');
}

$salt = uniqid();
$passw = password_hash($_POST['password']."Sono Bello".$salt); // TODO: Create a function in php module for same hashing everywhere
$conf = password_hash($_POST['pass_conf']."Sono Bello".$salt);

if ( $passw != $conf) {
    die('{"Errore" : "Two passowrds are different"}');
}

if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) {
    die('{"Errore" : "Email provided is not a valid email"}');
}

if (strlen($_POST['nickname']) > 64) {
    die('{"Errore" : "Nickname provided is too long, (more than 64 characters)"}');
}

if (!preg_match("/[0-9a-z_]/", $_POST['nickname'])) {
    die('{"Errore" : "Nickname can only contains letters and numbers"}');
}
?>