<?php
// delete_comment.php

require_once 'config.php';

// Check if the comment ID is provided
if (isset($_GET['comment_id'])) {
    $comment_id = $_GET['comment_id'];
    
    // Prepare and execute the SQL statement to delete the comment
    $stmt = $conn->prepare("DELETE FROM comments WHERE comment_id = ?");
    $stmt->bind_param("i", $comment_id);
    $stmt->execute();
    
    // Check if the comment was deleted successfully
    if ($stmt->affected_rows > 0) {
        // Redirect back to the post or wherever you want after deleting the comment
        header("Location: view_post.php?post_id={$_GET['post_id']}");
        exit();
    } else {
        // Handle deletion failure
        echo "Failed to delete comment.";
    }
} else {
    // Handle missing comment ID
    echo "Comment ID not provided.";
}
