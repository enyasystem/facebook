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
    <link rel="stylesheet" href="css/style.css">
    <script src="js/main.js"></script>
</head>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
    </style>
<body>
<div class="container">
    <h1>Welcome, <?php echo $username; ?></h1>
<nav class="pl-5">
    <a href="profile.php" class="profile-link">View Profile</a>
    <a href="create_post.php" class="profile-link">Create Post</a>
    <a href="messages.php" class="profile-link">Messages</a>
    <a href="http://localhost/facebook/notifications/notification.php" class="profile-link">Notifications</a>
    <a href="logout.php">Log out</a>
</nav>
    <!-- Existing code -->


    <!-- POST -->
    <h2>Your Posts</h2>
    <?php if (!empty($posts)) : ?>

    <ul>
        <?php foreach ($posts as $post) : ?>
        <li>
            <p><?php echo $post['content']; ?></p>
            <p><?php echo $post['likes']; ?> Likes</p>
            <ul>
                
                <?php
                        // Check if the 'comment' key is set in the array
// Check if the 'comment' key is set in the array
if (isset($_POST['comment'])) {
    $comment = $_POST['comment'];
  
    // Perform necessary validation and sanitization on $comment
    $comment = trim($comment); // Remove leading/trailing whitespace
    $comment = htmlspecialchars($comment); // Convert special characters to HTML entities
  
    // Check if the comment is empty
    if (empty($comment)) {
      echo "Comment is required.";
    } else {
      // Save the comment to the database or perform any other actions
  
      // Example: Insert the comment into the 'comments' table
      $sql = "INSERT INTO comments (post_id, user_id, comment) VALUES ('$post_id', '$user_id', '$comment')";
  
      if ($conn->query($sql) === true) {
        // Comment saved successfully
        echo "Comment added successfully!";
      } else {
        // Error saving the comment
        echo "Error: " . $sql . "<br>" . $conn->error;
      }
    }
  } else {
    // echo "Comment is required.";
  }
  
  
                        ?>
            </ul>
            <form action="add_comment.php" method="POST">
                <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                <?php
                 // Display comments
foreach ($comments as $comment) {
    // ...

    echo "<p>{$comment['content']}</p>";
    
}
                ?>
                <input type="text" name="comment" placeholder="Add a comment">
                <button type="submit">Comment</button>

                <!-- Code for displaying the post comments -->
<div class="comments-section">
  <h3>Comments</h3>

  <?php

if (isset($_POST['comment'])) {
    $comment = $_POST['comment'];
    // Rest of your code that uses the $comment variable
  } else {
    // echo "Comment is required.";
  }
  // Fetch comments for the post from the database
  $sql = "SELECT * FROM comments WHERE post_id = '$post_id'";
  $result = $conn->query($sql);

  if ($result->num_rows > 0) {
    // Comments exist for the post
    while ($row = $result->fetch_assoc()) {
      $commentId = $row['id'];
      $commentUser = $row['user_id'];
    //   $commentContent = $row['comment'];

      // Fetch the user information for the comment author
      $userSql = "SELECT * FROM users WHERE id = '$commentUser'";
      $userResult = $conn->query($userSql);
      $userRow = $userResult->fetch_assoc();
      $commentAuthor = $userRow['username'];

      // Output the comment
      echo "<div class='comment'>";
    //   echo "<p><strong>$username:</strong> $commentId</p>";
      echo "</div>";
    }
  } else {
    // No comments exist for the post
    echo "<p>No comments yet.</p>";
  }

 
  ?>
</div>

<!-- Code for comment form -->
            </form>
            <form action="like_post.php" method="POST">
                <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                <button type="submit">Like</button>
            </form>
        </li>
        <?php endforeach; ?>
    </ul>
    <?php else : ?>
    <p>No posts yet.</p>
    <?php endif; ?>

    <h2>Friend Requests</h2>
    <?php if (!empty($friendRequests)) : ?>
    <ul>
        <?php foreach ($friendRequests as $friendRequest) : ?>
        <li><?php echo $friendRequest['username']; ?> <a
                href="accept_friend_request.php?sender_id=<?php echo $friendRequest['id']; ?>">Accept</a></li>
        <?php endforeach; ?>
    </ul>
    <?php else : ?>
    <p>No friend requests.</p>
    <?php endif; ?>

    <h2>Notifications</h2>
    <?php if ($messageCount > 0) : ?>
    <p>You have <?php echo $messageCount; ?> unread message(s).</p>
    <?php else : ?>
    <p>No new notifications.</p>
    <?php endif; ?>
    <!-- Add more content and functionality to the dashboard -->
</div>

</body>

</html>
