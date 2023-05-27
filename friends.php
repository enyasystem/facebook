<?php
// Include the configuration file
require_once "config.php";

// Start the session
session_start();

// Redirect to the login page if user is not logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit();
}

// Retrieve the user ID and username from the session
$user_id = $_SESSION["user_id"];
$username = $_SESSION["username"];

// Retrieve the user's friends from the database
$sql = "SELECT users.* FROM users JOIN friends ON users.id = friends.friend_id WHERE friends.user_id = '$user_id'";
$result = $conn->query($sql);

$friends = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $friends[] = $row;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Social Media App - Friends</title>
    <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Include custom CSS -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Include the navigation bar -->
    <?php include 'nav.php'; ?>

    <div class="container mt-4">
        <h2>Friends</h2>
        <?php if (count($friends) > 0) : ?>
            <ul class="list-group">
                <?php foreach ($friends as $friend) : ?>
                    <li class="list-group-item">
                        <a href="profile.php?id=<?php echo $friend['id']; ?>">
                            <?php echo $friend['username']; ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else : ?>
            <p>No friends found.</p>
        <?php endif; ?>
    </div>

    <!-- Include Bootstrap JavaScript -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
