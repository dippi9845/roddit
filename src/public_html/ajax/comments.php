<?php
header('Content-Type: application/json');

$data = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'].'/../setup.json'));
$conn = new mysqli("localhost", $data->dbName, $data->dbPassword, $data->dbUserName);

if ($conn->connect_error) {
    die("{ \"Error\": \"" . $conn->connect_error . "\" }");
}

if (!isset($_GET['post'])) {
    die("{ \"Error\": \"No ID\" }");
}

$postID = $_GET['post'];

$stmt = $conn->prepare(
"SELECT comment.User, comment.Text, users.ProfileImagePath as ProfileImage
FROM comment
INNER JOIN users ON comment.User = users.Nickname
WHERE `entityType`='Post' AND `entityID`=?
");

$stmt->bind_param("i", $postID);
$stmt->execute();
$result = $stmt->get_result();
$comments = $result->fetch_all(MYSQLI_ASSOC);

$stmt->close();
$conn->close();
echo json_encode($comments);
?>