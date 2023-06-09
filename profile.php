<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit();
}

$username = $_SESSION["username"];
$user_id = $_SESSION["user_id"];


require_once "config.php";
require_once "nav.php";

// Retrieve user's friends from the database
$sql = "SELECT users.* FROM users JOIN friendships ON (users.id = friendships.user1_id OR users.id = friendships.user2_id) WHERE (friendships.user1_id = '$user_id' OR friendships.user2_id = '$user_id') AND users.id <> '$user_id'";
// $result = $conn->query($sql);

$friends = [];
// if ($result->num_rows > 0) {
//     while ($row = $result->fetch_assoc()) {
//         $friendships[] = $row;
//     }
// }

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Social Media App - Profile</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script src="js/main.js"></script>
</head>
<body>
    <div class="container">
        <h1> <?php echo $username; ?>'s Profile</h1>

        <a href="dashboard.php">Back to Dashboard</a>

        <h2>Profile Details</h2>
        <!-- Add your code to display user information and other profile details here -->

        <h2>Posts</h2>
        <!-- Existing code -->

        <h2>Friends</h2>
        <?php if (!empty($friends)) : ?>
            <ul>
                <?php foreach ($friends as $friend) : ?>
                    <li>
                        <?php echo $friend['username']; ?>
                        <form action="send_message.php" method="POST">
                            <input type="hidden" name="receiver_id" value="<?php echo $friend['id']; ?>">
                            <input type="text" name="message" placeholder="Message">
                            <button type="submit" class="btn btn-primary">Send Message</button>
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else : ?>
            <p>No friends yet.</p>
        <?php endif; ?>
    </div>
</body>
</html>
