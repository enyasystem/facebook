<?php
// edit_profile.php

// Include config file
require_once "config.php";

// Initialize the session
session_start();

// Check if the user is logged in, otherwise redirect to the login page
if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit();
}

// Get the user ID from the session
$user_id = $_SESSION["user_id"];

// Fetch the user's current profile data from the database
$sql = "SELECT * FROM users WHERE id = '$user_id'";
$result = $conn->query($sql);

if ($result->num_rows == 1) {
    $row = $result->fetch_assoc();
    $username = $row['username'];
    $email = $row['email'];
    // You can fetch other profile fields here as needed
} else {
    // Handle the case when the user is not found in the database
    // You can redirect to an error page or display an appropriate message
    echo "User not found.";
    exit();
}

// Initialize error and success messages
$error = "";
$success = "";

// Process the form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve the submitted form data
    $newUsername = $_POST["username"];
    $newEmail = $_POST["email"];
    $oldPassword = $_POST["old_password"];
    $newPassword = $_POST["new_password"];
    // You can retrieve and update other profile fields here as needed

    // Verify the old password
    $passwordSql = "SELECT password FROM users WHERE id = '$user_id'";
    $passwordResult = $conn->query($passwordSql);

    if ($passwordResult->num_rows == 1) {
        $passwordRow = $passwordResult->fetch_assoc();
        $hashedPassword = $passwordRow["password"];

        if (password_verify($oldPassword, $hashedPassword)) {
            // Old password is correct, proceed with the update

            // Perform the update operation in the database
            $updateSql = "UPDATE users SET username = '$newUsername', email = '$newEmail' WHERE id = '$user_id'";

            if ($conn->query($updateSql) === TRUE) {
                // Update the session variables with the new profile data
                $_SESSION["username"] = $newUsername;

                // Check if a new password is provided
                if (!empty($newPassword)) {
                    // Hash the new password
                    $hashedNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);

                    // Update the password in the database
                    $updatePasswordSql = "UPDATE users SET password = '$hashedNewPassword' WHERE id = '$user_id'";
                    $conn->query($updatePasswordSql);

                    // Display success message
                    $success = "Password updated successfully!";
                }

                // Redirect to the profile page after successful update
                header("Location: profile.php");
                exit();
            } else {
                // Handle the case when the update operation fails
                // You can redirect to an error page or display an appropriate message
                echo "Error updating profile: " . $conn->error;
            }
        } else {
            // Display an error message when the old password is incorrect
            $error = "Invalid old password.";
        }
    } else {
        // Handle the case when the user is not found in the database
        // You can redirect to an error page or display an appropriate message
        echo "User not found.";
        exit();
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Profile</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="container">
        <h2>Edit Profile</h2>
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
            <script>
                Swal.fire({
                    title: 'Success',
                    text: '<?php echo $success; ?>',
                    icon: 'success',
                    confirmButtonText: 'OK'
                });
            </script>
        <?php endif; ?>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" class="form-control" id="username" name="username" value="<?php echo $username; ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo $email; ?>" required>
            </div>
            <div class="form-group">
                <label for="old_password">Old Password:</label>
                <input type="password" class="form-control" id="old_password" name="old_password" required>
            </div>
            <div class="form-group">
                <label for="new_password">New Password:</label>
                <input type="password" class="form-control" id="new_password" name="new_password">
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</body>
</html>
