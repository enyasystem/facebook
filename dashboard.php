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

        <h4>Your Posts</h4>
        <?php if (count($posts) > 0): ?>
        <?php foreach ($posts as $post): ?>
        <div class="card mb-3">
            <div class="card-body">
                <p class="card-text"><?php echo $post['content']; ?></p>
                <p class="card-text">
                    <small class="text-muted">Posted by <?php echo $username; ?> at
                        <?php echo $post['created_at']; ?></small>
                </p>
                <p class="card-text">
                    <small class="text-muted">Likes: <?php echo $post['likes']; ?></small>
                </p>
                <form method="POST" action="like_post.php">
                    <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                    <button type="submit" class="btn btn-primary">Like</button>
                </form>
            </div>
            <div class="card-footer">
                <h6>Comments</h6>
                <?php if (count($post['comments']) > 0): ?>
                <?php foreach ($post['comments'] as $comment): ?>
                <p>
                    <strong><?php echo $comment['username']; ?>:</strong>
                    <?php if (isset($comment['comment'])) {
                echo $comment['comment'];
            } ?>
                </p>
                <?php endforeach; ?>
                <?php else: ?>
                <p>No comments yet.</p>
                <?php endif; ?>

                <form method="POST" action="add_comment.php">
                    <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                    <div class="form-group">
                        <input type="text" name="comment" class="form-control" placeholder="Add a comment">
                    </div>
                    <button type="submit" class="btn btn-primary">Add Comment</button>
                </form>
            </div>
        </div>
        <?php endforeach; ?>
        <?php else: ?>
        <p>No posts to display.</p>
        <?php endif; ?>
    </div>
</body>

</html>
