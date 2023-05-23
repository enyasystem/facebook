// add_comment.php

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
    $content = $_POST["comment"]; // Updated: Match the name attribute of the textarea input field

    // Perform necessary validation and sanitization
    $user_id = filter_var($user_id, FILTER_VALIDATE_INT);
    $post_id = filter_var($post_id, FILTER_VALIDATE_INT);
    $content = trim($content);

    if ($user_id === false) {
        echo "Invalid user ID.";
        exit();
    }

    if ($post_id === false) {
        echo "Invalid post ID.";
        exit();
    }

    if (empty($content)) {
        echo "Comment content is required.";
        exit();
    }

    // Insert comment data into the database
    $sql = "INSERT INTO comments (user_id, post_id, content) VALUES ('$user_id', '$post_id', '$content')";

    if ($conn->query($sql) === true) {
        header("Location: view_post.php?post_id=$post_id");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>

