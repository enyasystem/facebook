// accept_friend_request.php
<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit();
}

require_once "config.php";

$sender_id = $_GET["sender_id"];
$receiver_id = $_SESSION["user_id"];

// Perform necessary validation and sanitization

// Insert friend relationship into the database
$sql = "INSERT INTO friends (user1_id, user2_id) VALUES ('$sender_id', '$receiver_id')";

if ($conn->query($sql) === true) {
    // Delete the friend request
    $deleteRequestSql = "DELETE FROM friend_requests WHERE sender_id = '$sender_id' AND receiver_id = '$receiver_id'";
    $conn->query($deleteRequestSql);

    echo "Friend request accepted";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
