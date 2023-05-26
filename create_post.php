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

    // Upload the image file if it exists
    $image_path = "";
    if ($_FILES["image"]["size"] > 0) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Perform necessary checks on the uploaded file
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check === false) {
            echo "File is not an image.";
            exit();
        }

        if ($_FILES["image"]["size"] > 500000) {
            echo "File is too large. Maximum size allowed is 500KB.";
            exit();
        }

        // Generate a unique name for the image file
        $image_name = uniqid() . "." . $imageFileType;

        // Move the uploaded file to the desired location
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_dir . $image_name)) {
            $image_path = $target_dir . $image_name;
        } else {
            echo "Error uploading the file.";
            exit();
        }
    }

    // Insert post data into the database
    $insertSql = "INSERT INTO posts (user_id, content, image_path) VALUES ('$user_id', '$content', '$image_path')";

    if ($conn->query($insertSql) === true) {
        echo "Post created successfully";
    } else {
        echo "Error: " . $insertSql . "<br>" . $conn->error;
    }

    // $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Post</title>
    <!-- Add Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1>Create Post</h1>
        <form method="POST" action="create_post.php" enctype="multipart/form-data">
            <textarea name="content" placeholder="Enter your post content" class="form-control"></textarea>
            <br>
            <div class="form-group">
                <label for="image">Upload Image:</label>
                <input type="file" name="image" id="image">
            </div>
            <br>
            <input type="submit" value="Create" class="btn btn-primary">
        </form>

        <h1>My Posts</h1>
        <?php
        $user_id = $_SESSION["user_id"];

// Rest of the code...
$sql = "SELECT * FROM posts WHERE user_id = '$user_id' ORDER BY created_at DESC";

$result = $conn->query($sql);
if ($result !== null && $result->num_rows > 0) while ($row = $result->fetch_assoc()) {
    $postId = $row["id"];
    $content = $row["content"];
    $created_at = $row["created_at"];
    $image_path = $row["image_path"];

    echo "<div class='card'>";
    if (!empty($image_path)) {
        echo "<img src='$image_path' class='card-img-top' alt='Post Image'>";
    }
    echo "<div class='card-body'>";
    echo "<p>$content</p>";
    echo "<p>Created at: $created_at</p>";
    echo "<form method='POST' action='create_post.php'>";
    echo "<input type='hidden' name='delete_post' value='$postId'>";
    echo "<input type='submit' value='Delete' class='btn btn-danger'>";
    echo "</form>";
    echo "<form method='POST' action='edit_post.php'>";
    echo "<input type='hidden' name='edit_post' value='$postId'>";
    echo "<input type='submit' value='Edit' class='btn btn-primary'>";
    echo "</form>";
    echo "</div>";
    echo "</div>";
}
 else {
    echo "No posts found.";
}



?>

    </div>

    <!-- Add Bootstrap JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>
