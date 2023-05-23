<?php
require_once('notification_functions.php');

// Get the notification ID from the request
$notification_id = $_POST['notification_id']; // Assuming it's sent via POST

// Mark the notification as read
markNotificationAsRead($notification_id);

// Redirect the user back to the notifications page
header('Location: notification.php');
?>
