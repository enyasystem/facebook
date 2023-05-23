<?php
// Connect to the database
// require_once "http://localhost/facebook/config.php";


// Function to retrieve the user's notifications from the database
function getNotifications($user_id) {
    global $conn;

    // Prepare and execute the SQL statement to retrieve notifications
    $stmt = $conn->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    // Fetch the result set
    $result = $stmt->get_result();

    // Create an array to store the notifications
    $notifications = array();

    // Iterate over the result set and fetch each notification
    while ($row = $result->fetch_assoc()) {
        $notification = array(
            'id' => $row['id'],
            'message' => $row['message'],
            'created_at' => $row['created_at'],
            'is_read' => $row['is_read']
        );
        $notifications[] = $notification;
    }

    // Return the notifications array
    return $notifications;
}

// Function to mark a notification as read
function markNotificationAsRead($notification_id) {
    global $conn;

    // Prepare and execute the SQL statement to update the notification status
    $stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE id = ?");
    $stmt->bind_param("i", $notification_id);
    $stmt->execute();
}
?>
