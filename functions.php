<?php
// functions.php

require_once 'config.php';

// Function to retrieve messages for a user
function getMessages($user_id) {
    global $conn;
    
    // Prepare and execute the SQL statement to retrieve messages for the user
    $query = $conn->prepare('SELECT * FROM messages WHERE receiver_id = ?');
    $query->bind_param('s', $recipient);
    $query->execute();
    

    
    
    
    // Fetch the result set
    $stmt = $conn->prepare('SELECT * FROM messages WHERE recipient_id = ?');
    $stmt->bind_param('s', $recipient);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $stmt->free_result(); // Free up the memory from the first query
    
    // Now you can execute the second query
    $secondStmt = $conn->prepare('SELECT * FROM other_table');
    $secondStmt->execute();
    $secondResult = $secondStmt->get_result();
    
    // Process the results as needed
    
    $stmt->close();
    $secondStmt->close();
    
    
    
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
