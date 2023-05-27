<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit();
}

require_once "config.php";
require_once "nav.php";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["edit_post"])) {
    $post_id = $_POST["edit_post"];

    // Perform necessary validation and sanitization
    $post_id = filter_var($post_id, FILTER_VALIDATE_INT);

    if ($post_id === false) {
        echo "Invalid post ID.";
        exit();
    }

    // Retrieve the post from the database
    $selectSql = "SELECT * FROM posts WHERE id = $post_id";
    $result = $conn->query($selectSql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $content = $row["content"];
    } else {
        echo "Post not found.";
        exit();
    }

    $conn->close();
} else if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_post"])) {
    $post_id = $_POST["update_post"];
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
} else {
    echo "Invalid request.";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Post</title>
</head>
<body>
    <h1>Edit Post</h1>
    <form method="POST" action="edit_post.php">
        <input type="hidden" name="update_post" value="<?php echo $post_id; ?>">
        <textarea name="new_content"><?php echo $content; ?></textarea>
        <br>
        <input type="submit" value="Update">
    </form>
</body>
</html>
