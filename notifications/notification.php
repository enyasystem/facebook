<?php
require_once('notification_functions.php');

// Get the user's notifications
$user_id = 1; // Replace with the actual user ID
$notifications = getNotifications($user_id);

// Display the notifications
foreach ($notifications as $notification) {
    echo '<div class="notification">';
    echo '<p class="notification-text">' . $notification['message'] . '</p>';
    echo '</div>';
}
?>
