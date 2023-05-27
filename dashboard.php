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

// Retrieve unread notifications count for the logged-in user
$notificationCountSql = "SELECT COUNT(*) AS notification_count FROM notifications WHERE receiver_id = '$user_id' AND is_read = 0";
$notificationCountResult = $conn->query($notificationCountSql);
$notificationCount = 0;
if ($notificationCountResult->num_rows == 1) {
    $notificationCountRow = $notificationCountResult->fetch_assoc();
    $notificationCount = $notificationCountRow['notification_count'];
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

    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
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
                        <a class="nav-link" href="notifications.php">Notifications
                            <?php if ($notificationCount > 0): ?>
                                <span class="badge badge-pill badge-primary"><?php echo $notificationCount; ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="edit_profile.php">Edit Profile</a>
                    </li>
                    <!-- Search form -->
                    <form method="GET" action="search.php" class="form-inline my-2 my-lg-0">
                        <input class="form-control mr-sm-2" type="search" name="query" placeholder="Search" aria-label="Search">
                        <button class="btn btn-outline-primary my-2 my-sm-0" type="submit">Search</button>
                    </form>
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

        // Handle comment submission
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit_comment"])) {
            // Comment submission code...
        }

        // Handle like submission
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit_like"])) {
            // Like submission code...
        }
        ?>

        <div class="row mt-4">
            <div class="col-md-8">
                <h4>Your Posts</h4>
                <?php if (count($posts) > 0): ?>
                    <?php foreach ($posts as $post): ?>
                        <div class="card mb-4">
                            <div class="card-header">
                                <a href="profile.php?id=<?php echo $user_id; ?>"><?php echo $username; ?></a>
                            </div>
                            <div class="card-body">
                                <p><?php echo $post['content']; ?></p>
                                <?php if ($post['image']): ?>
                                    <img src="<?php echo $post['image']; ?>" alt="Post Image" class="img-fluid mb-3">
                                <?php endif; ?>
                                <p class="text-muted"><?php echo $post['created_at']; ?></p>
                                <div class="row">
                                    <div class="col">
                                        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                            <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                                            <button type="submit" name="delete_post" class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                    </div>
                                    <div class="col">
                                        <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#commentModal-<?php echo $post['id']; ?>">Comment</button>
                                    </div>
                                    <div class="col">
                                        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                            <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                                            <button type="submit" name="submit_like" class="btn btn-success btn-sm"><i class="fas fa-thumbs-up"></i> <?php echo $post['likes']; ?></button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <h6>Comments</h6>
                                <?php if (count($post['comments']) > 0): ?>
                                    <?php foreach ($post['comments'] as $comment): ?>
                                        <div class="card mb-2">
                                            <div class="card-body">
                                                <p><?php echo $comment['comment']; ?></p>
                                                <p class="text-muted"><?php echo $comment['username']; ?></p>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p>No comments yet.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <!-- Comment Modal -->
                        <div class="modal fade" id="commentModal-<?php echo $post['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="commentModalLabel-<?php echo $post['id']; ?>" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="commentModalLabel-<?php echo $post['id']; ?>">Add a comment</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                            <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                                            <div class="form-group">
                                                <textarea class="form-control" name="comment" rows="3" placeholder="Write a comment"></textarea>
                                            </div>
                                            <button type="submit" name="submit_comment" class="btn btn-primary">Submit</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>You haven't made any posts yet.</p>
                <?php endif; ?>
            </div>
            <div class="col-md-4">
                <h4>Recent Activity</h4>
                <?php foreach ($activities as $activity): ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <p><?php echo $activity['message']; ?></p>
                            <p class="text-muted"><?php echo $activity['timestamp']; ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <!-- Bootstrap JavaScript -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>

</html>
