<?php
function destroy_cookie($conn) {
    $sql = "DELETE FROM cookies WHERE Token = ?";
    $stmt =  $conn->prepare($sql);
    $stmt->bind_param("s", json_decode($_COOKIE['roddit'])->selector);
    $stmt->execute();
    $stmt->close();

    setcookie("roddit", '', time() - 3600, '/');
}

if (isset($_COOKIE['roddit'])) {
    $file = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/../setup.json');
    $data = json_decode($file, false);
    $conn = new mysqli("localhost", $data->dbName, $data->dbPassword, $data->dbUserName);

    destroy_cookie($conn);

    $conn->close();
}

session_start();
session_destroy();
header("Location: login.php");
?>
