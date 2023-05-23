<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    echo json_encode(["error" => "User not logged in"]);
    exit();
}

require_once "config.php";

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $user_id = $_SESSION["user_id"];
    $post_id = $_GET["post_id"];

    // Perform necessary validation and sanitization
    $user_id = filter_var($user_id, FILTER_VALIDATE_INT);
    $post_id = filter_var($post_id, FILTER_VALIDATE_INT);

    if ($user_id === false) {
        echo json_encode(["error" => "Invalid user ID"]);
        exit();
    }

    if ($post_id === false) {
        echo json_encode(["error" => "Invalid post ID"]);
        exit();
    }

    // Check if the user has already liked the post
    $sql = "SELECT * FROM likes WHERE user_id = '$user_id' AND post_id = '$post_id'";
    $result = $conn->query($sql);
    $liked = ($result->num_rows > 0);

    // Count the total number of likes for the post
    $sql = "SELECT COUNT(*) AS like_count FROM likes WHERE post_id = '$post_id'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $like_count = $row["like_count"];

    echo json_encode(["liked" => $liked, "likeCount" => $like_count]);

    $conn->close();
}
?>
