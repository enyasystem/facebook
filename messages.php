<?php
// messages.php

// Check if the user is logged in
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Include the necessary files
require_once 'config.php';
require_once 'functions.php';

// Retrieve the user's messages
$user_id = $_SESSION['user_id'];
$messages = getMessages($user_id);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Social Media App - Messages</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1>Messages</h1>

    <div class="messages">
        <?php foreach ($messages as $message): ?>
            <div class="message">
                <p><?php echo $message['sender']; ?></p>
                <p><?php echo $message['content']; ?></p>
                <p><?php echo $message['timestamp']; ?></p>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="new-message">
        <h2>New Message</h2>
        <form action="send_message.php" method="POST">
            <input type="text" name="recipient" placeholder="Recipient" required>
            <textarea name="content" placeholder="Message content" required></textarea>
            <button type="submit">Send</button>
        </form>
    </div>

    <a href="dashboard.php">Back to Dashboard</a>

</body>
</html>
