<?php
session_start();

require_once "config.php";

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION["user_id"];

// Fetch current user data
$sql = "SELECT * FROM users WHERE id = '$user_id'";
$result = $conn->query($sql);

if ($result->num_rows == 1) {
    $row = $result->fetch_assoc();
    $username = $row["username"];
    $email = $row["email"];
} else {
    // Handle the case when the user is not found
    // You can redirect to an error page or display an appropriate message
    echo "User not found.";
    exit();
}

// Initialize error and success messages
$error = "";
$success = "";

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Retrieve form data
    $newUsername = $_POST["username"];
    $newEmail = $_POST["email"];
    $oldPassword = $_POST["old_password"];
    $newPassword = $_POST["new_password"];

    // Verify old password
    if (password_verify($oldPassword, $row["password"])) {
        // Check if the new password is empty
        if ($newPassword !== "") {
            // Check if the new password meets the requirements
            if (preg_match('/^(?=.*[a-zA-Z])(?=.*\d)[a-zA-Z\d]{8,}$/', $newPassword)) {
                // Update username, email, and password
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $updateSql = "UPDATE users SET username = '$newUsername', email = '$newEmail', password = '$hashedPassword' WHERE id = '$user_id'";

                if ($conn->query($updateSql) === TRUE) {
                    // Update the session variable with the new profile data
                    $_SESSION["username"] = $newUsername;

                    // Display success message
                    $success = "Profile updated successfully!";
                } else {
                    // Handle the case when the update operation fails
                    // You can redirect to an error page or display an appropriate message
                    echo "Error updating profile: " . $conn->error;
                }
            } else {
                // Display an error message when the new password does not meet the requirements
                $error = "The new password must contain at least 8 characters, including letters and numbers.";
            }
        } else {
            // Display an error message when the new password is empty
            $error = "New password cannot be empty.";
        }
    } else {
        // Display an error message when the old password is incorrect
        $error = "Incorrect old password.";
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Profile</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h2>Edit Profile</h2>
        <?php if (!empty($error)) { ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error; ?>
            </div>
        <?php } ?>
        <?php if (!empty($success)) { ?>
            <div class="alert alert-success" role="alert">
                <?php echo $success; ?>
            </div>
        <?php } ?>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" class="form-control" id="username" name="username" value="<?php echo $username; ?>">
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo $email; ?>">
            </div>
            <div class="form-group">
                <label for="old_password">Old Password:</label>
                <input type="password" class="form-control" id="old_password" name="old_password" required>
            </div>
            <div class="form-group">
                <label for="new_password">New Password:</label>
                <input type="password" class="form-control" id="new_password" name="new_password">
            </div>
            <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
