<?php 

function getUserNameByID($conn, $userID) {
    $sql = "SELECT Nickname FROM users WHERE ID = {$userID}";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    return $row['Nickname'];
}

?>
