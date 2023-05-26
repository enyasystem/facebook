<?php
require_once "config.php";
include('notifications/notification.php');

session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit();
}

$username = $_SESSION["username"];
$user_id = $_SESSION["user_id"];

// Retrieve friend requests for the logged-in user
$sql = "SELECT users.* FROM users JOIN friend_requests ON users.id = friend_requests.sender_id WHERE friend_requests.receiver_id = '$user_id'";
$result = $conn->query($sql);

$friendRequests = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $friendRequests[] = $row;
    }
}

// Retrieve unread messages count for the logged-in user
$messageCountSql = "SELECT COUNT(*) AS message_count FROM messages WHERE receiver_id = '$user_id' AND is_read = 0";
$messageCountResult = $conn->query($messageCountSql);
$messageCount = 0;
if ($messageCountResult->num_rows == 1) {
    $messageCountRow = $messageCountResult->fetch_assoc();
    $messageCount = $messageCountRow['message_count'];
}

// Retrieve user's posts from the database
$sql = "SELECT * FROM posts WHERE user_id = '$user_id' ORDER BY created_at DESC";
$result = $conn->query($sql);

$posts = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Retrieve comments for each post
        $post_id = $row['id'];
        $commentSql = "SELECT comments.*, users.username FROM comments JOIN users ON comments.user_id = users.id WHERE comments.post_id = '$post_id'";
        $commentResult = $conn->query($commentSql);
        $comments = [];
        if ($commentResult->num_rows > 0) {
            while ($commentRow = $commentResult->fetch_assoc()) {
                $comments[] = $commentRow;
            }
        }
        $row['comments'] = $comments;

        // Retrieve likes for each post
        $likeSql = "SELECT COUNT(*) AS like_count FROM likes WHERE post_id = '$post_id'";
        $likeResult = $conn->query($likeSql);
        if ($likeResult->num_rows == 1) {
            $likeRow = $likeResult->fetch_assoc();
            $row['likes'] = $likeRow['like_count'];
        } else {
            $row['likes'] = 0;
        }

        $posts[] = $row;
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Social Media App - Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <script src="js/main.js"></script>
    <style>
    body {
        background-color: #f8f9fa;
    }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-md navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">
                Social Media App
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="profile.php">Profile</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="friends.php">Friends</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="messages.php">Messages
                            <?php if ($messageCount > 0): ?>
                            <span class="badge badge-pill badge-primary"><?php echo $messageCount; ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="edit_profile.php">Edit Profile</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="create_post.php">Create Post</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="search.php">Search</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-4">
        <h2>Welcome, <?php echo $username; ?></h2>
        <h4>Friend Requests</h4>
        <?php if (count($friendRequests) > 0): ?>
        <ul class="list-group">
            <?php foreach ($friendRequests as $request): ?>
            <li class="list-group-item">
                <a href="profile.php?id=<?php echo $request['id']; ?>"><?php echo $request['username']; ?></a>
                wants to be your friend.
            </li>
            <?php endforeach; ?>
        </ul>
        <?php else: ?>
        <p>No friend requests at the moment.</p>
        <?php endif; ?>

        <?php
// session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit();
}

require_once "config.php";

// Handle post deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete_post"])) {
    // Deletion code...
}

// Handle post editing
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["edit_post"])) {
    // Editing code...
}

// Handle post creation
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["content"])) {
    // Creation code...
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Post</title>
    <!-- Add Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <style>
        .options {
            display: none;
        }

        .show-options {
            display: inline-block;
            margin-left: 10px;
            cursor: pointer;
        }
    </style>
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

        $sql = "SELECT * FROM posts WHERE user_id = '$user_id' ORDER BY created_at DESC";
        $result = $conn->query($sql);

        if ($result !== null && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
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
                echo "<div class='options'>";
                echo "<form method='POST' action='create_post.php'>";
                echo "<input type='hidden' name='delete_post' value='$postId'>";
                echo "<input type='submit' value='Delete' class='btn btn-danger'>";
                echo "</form>";
                echo "<form method='POST' action='edit_post.php'>";
                echo "<input type='hidden' name='edit_post' value='$postId'>";
                echo "<input type='submit' value='Edit' class='btn btn-primary'>";
                echo "</form>";
                echo "</div>";
                echo "<div class='show-options' onclick='toggleOptions(this)'>Show Options</div>";
                echo "<div class='like-button'>Like</div>";
                echo "<div class='comment-box'>Comment Box</div>";
                echo "</div>";
                echo "</div>";
            }
        } else {
            echo "No posts found.";
        }
        ?>
    </div>

    <!-- Add Bootstrap JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script>
        function toggleOptions(button) {
            var optionsDiv = button.parentNode.querySelector('.options');
            optionsDiv.style.display = optionsDiv.style.display === 'none' ? 'block' : 'none';
        }
    </script>
</body>
</html>


    </div>
</body>

</html>
