<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit();
}

require_once "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION["user_id"];
    $post_id = $_POST["post_id"];

    // Perform necessary validation and sanitization
    $user_id = filter_var($user_id, FILTER_VALIDATE_INT);
    $post_id = filter_var($post_id, FILTER_VALIDATE_INT);

    if ($user_id === false) {
        echo "Invalid user ID.";
        exit();
    }

    if ($post_id === false) {
        echo "Invalid post ID.";
        exit();
    }

    // Delete the like from the database
    $sql = "DELETE FROM likes WHERE user_id = '$user_id' AND post_id = '$post_id'";

    if ($conn->query($sql) === true) {
        header("Location: view_post.php?");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>
