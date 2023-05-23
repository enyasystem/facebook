<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit();
}

require_once "config.php";

// Handle post deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete_post"])) {
    $post_id = $_POST["delete_post"];

    // Perform necessary validation and sanitization
    $post_id = filter_var($post_id, FILTER_VALIDATE_INT);

    if ($post_id === false) {
        echo "Invalid post ID.";
        exit();
    }

    // Delete the post from the database
    $deleteSql = "DELETE FROM posts WHERE id = $post_id";

    if ($conn->query($deleteSql) === true) {
        echo "Post deleted successfully";
    } else {
        echo "Error: " . $deleteSql . "<br>" . $conn->error;
    }

    $conn->close();
    exit();
}

// Handle post editing
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["edit_post"])) {
    $post_id = $_POST["edit_post"];
    $new_content = $_POST["new_content"];

    // Perform necessary validation and sanitization
    $post_id = filter_var($post_id, FILTER_VALIDATE_INT);
    $new_content = trim($new_content);

    if ($post_id === false) {
        echo "Invalid post ID.";
        exit();
    }

    if (empty($new_content)) {
        echo "Content is required.";
        exit();
    }

    // Update the post in the database
    $updateSql = "UPDATE posts SET content = '$new_content' WHERE id = $post_id";

    if ($conn->query($updateSql) === true) {
        echo "Post updated successfully";
    } else {
        echo "Error: " . $updateSql . "<br>" . $conn->error;
    }

    $conn->close();
    exit();
}

// Handle post creation
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["content"])) {
    $user_id = $_SESSION["user_id"];
    $content = $_POST["content"];

    // Perform necessary validation and sanitization
    $user_id = filter_var($user_id, FILTER_VALIDATE_INT);
    $content = trim($content);

    if ($user_id === false) {
        echo "Invalid user ID.";
        exit();
    }

    if (empty($content)) {
        echo "Content is required.";
        exit();
    }

    // Insert post data into the database
    $insertSql = "INSERT INTO posts (user_id, content) VALUES ('$user_id', '$content')";

    if ($conn->query($insertSql) === true) {
        echo "Post created successfully";
    } else {
        echo "Error: " . $insertSql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Post</title>
</head>
<body>
    <h1>Create Post</h1>
    <form method="POST" action="create_post.php">
        <textarea name="content" placeholder="Enter your post content"></textarea>
        <br>
        <input type="submit" value="Create">
    </form>

    <h1>My Posts</h1>
    <?php
    // Display user's posts
    $userId = $_SESSION["user_id"];
    $selectSql = "SELECT * FROM posts WHERE user_id = $userId ORDER BY created_at DESC";
    $result = $conn->query($selectSql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $postId = $row["id"];
            $content = $row["content"];
            $created_at = $row["created_at"];

            echo "<div>";
            echo "<p>$content</p>";
            echo "<p>Created at: $created_at</p>";
            echo "<form method='POST' action='create_post.php'>";
            echo "<input type='hidden' name='delete_post' value='$postId'>";
            echo "<input type='submit' value='Delete'>";
            echo "</form>";
            echo "<form method='POST' action='edit_post.php'>";
            echo "<input type='hidden' name='edit_post' value='$postId'>";
            echo "<input type='submit' value='Edit'>";
            echo "</form>";
            echo "</div>";
        }
    } else {
        echo "No posts found.";
    }

    $conn->close();
    ?>
</body>
</html>
