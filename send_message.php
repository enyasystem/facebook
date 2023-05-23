<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit();
}

require_once "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sender_id = $_SESSION["user_id"];
    $receiver_id = $_POST["receiver_id"];
    $message = $_POST["message"];

    // Perform necessary validation and sanitization
    $sender_id = filter_var($sender_id, FILTER_VALIDATE_INT);
    $receiver_id = filter_var($receiver_id, FILTER_VALIDATE_INT);
    $message = trim($message);

    if ($sender_id === false || $receiver_id === false) {
        echo "Invalid sender or receiver ID.";
        exit();
    }

    if (empty($message)) {
        echo "Message is required.";
        exit();
    }

    // Sanitize the message to prevent HTML/script injection
    $message = htmlspecialchars($message);

    // Insert message data into the database
    $sql = "INSERT INTO messages (sender_id, receiver_id, message) VALUES ('$sender_id', '$receiver_id', '$message')";

    if ($conn->query($sql) === true) {
        echo "Message sent successfully";

        // Mark the message as read
        $message_id = $conn->insert_id;
        $markReadSql = "UPDATE messages SET is_read = 1 WHERE id = '$message_id'";
        $conn->query($markReadSql);
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>
