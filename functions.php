<?php
require_once "config.php";

function getMessages($userId) {
    global $conn;
    $messages = array();

    $query = "SELECT * FROM messages WHERE receiver_id = ? ORDER BY created_at DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }

    $stmt->free_result();
    $stmt->close();

    return $messages;
}

function sendMessage($senderId, $receiverId, $message) {
    global $conn;

    $query = "INSERT INTO messages (sender_id, receiver_id, message, created_at) VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iis", $senderId, $receiverId, $message);
    $stmt->execute();
    $stmt->close();
}

function getUserById($userId) {
    global $conn;

    $query = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    return $user;
}

function getPostById($postId) {
    global $conn;

    $query = "SELECT * FROM posts WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $postId);
    $stmt->execute();
    $result = $stmt->get_result();
    $post = $result->fetch_assoc();
    $stmt->close();

    return $post;
}

function getCommentsByPostId($postId) {
    global $conn;
    $comments = array();

    $query = "SELECT comments.*, users.username FROM comments JOIN users ON comments.user_id = users.id WHERE comments.post_id = ? ORDER BY comments.created_at ASC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $postId);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $comments[] = $row;
    }

    $stmt->close();

    return $comments;
}

function getLikesCountByPostId($postId) {
    global $conn;

    $query = "SELECT COUNT(*) AS like_count FROM likes WHERE post_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $postId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $likeCount = $row['like_count'];
    $stmt->close();

    return $likeCount;
}

function isLikedByUser($postId, $userId) {
    global $conn;

    $query = "SELECT COUNT(*) AS count FROM likes WHERE post_id = ? AND user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $postId, $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $count = $row['count'];
    $stmt->close();

    return ($count > 0);
}

function likePost($postId, $userId) {
    global $conn;

    $query = "INSERT INTO likes (post_id, user_id) VALUES (?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $postId, $userId);
    $stmt->execute();
    $stmt->close();
}

function unlikePost($postId, $userId) {
    global $conn;

    $query = "DELETE FROM likes WHERE post_id = ? AND user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $postId, $userId);
    $stmt->execute();
    $stmt->close();
}

function createComment($postId, $userId, $comment) {
    global $conn;

    $query = "INSERT INTO comments (post_id, user_id, comment, created_at) VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iis", $postId, $userId, $comment);
    $stmt->execute();
    $stmt->close();
}
