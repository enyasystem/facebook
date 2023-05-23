<?php
// functions.php

require_once 'config.php';

// Function to retrieve messages for a user
function getMessages($user_id) {
    global $conn;
    
    // Prepare and execute the SQL statement to retrieve messages for the user
    $stmt = $conn->prepare("SELECT * FROM messages WHERE sender_id = ? OR recipient = ? ORDER BY timestamp DESC");
    $stmt->bind_param("ss", $user_id, $user_id);
    $stmt->execute();
    
    // Fetch the result set
    $result = $stmt->get_result();
    
    // Create an array to store the messages
    $messages = array();
    
    // Iterate over the result set and fetch each message
    while ($row = $result->fetch_assoc()) {
        $message = array(
            'sender_id' => $row['sender_id'],
            'recipient_id' => $row['recipient_id'],
            'content' => $row['message_content'],
            'timestamp' => $row['timestamp']
        );
        $messages[] = $message;
    }
    
    // Return the messages array
    return $messages;
}

?>
