<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit();
}

require_once "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $post_id = $_POST["post_id"];

    // Perform necessary validation and sanitization
    $post_id = filter_var($post_id, FILTER_VALIDATE_INT);

    if ($post_id === false) {
        echo "Invalid post ID.";
        exit();
    }

    // Delete the post from the database
    $sql = "DELETE FROM posts WHERE id = '$post_id'";

    if ($conn->query($sql) === true) {
        echo "Post deleted successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>
