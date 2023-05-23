<//?php
require_once "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];

    // Perform necessary validation and sanitization

    // Insert user data into the database
    $sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$password')";

    if ($conn->query($sql) === true) {
        echo "Registration successful";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>



<?php
require_once "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];

    // Perform necessary validation and sanitization
    $username = trim($username);
    $email = filter_var($email, FILTER_VALIDATE_EMAIL);

    // Check if the username is empty
    if (empty($username)) {
        echo "Username is required.";
        exit();
    }

    // Check if the email is empty or invalid
    if (empty($email) || $email === false) {
        echo "Invalid email address.";
        exit();
    }

    // Check if the password is empty or doesn't meet requirements (e.g., minimum length)
    if (empty($password) || strlen($password) < 8) {
        echo "Password must be at least 8 characters long.";
        exit();
    }

    // Check if the username or email is already taken
    $checkUsernameSql = "SELECT * FROM users WHERE username = '$username'";
    $checkEmailSql = "SELECT * FROM users WHERE email = '$email'";

    $usernameResult = $conn->query($checkUsernameSql);
    $emailResult = $conn->query($checkEmailSql);

    if ($usernameResult->num_rows > 0) {
        echo "Username is already taken.";
        exit();
    }

    if ($emailResult->num_rows > 0) {
        echo "Email is already taken.";
        exit();
    }

    // Insert user data into the database
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT); // Hash the password

    $sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$hashedPassword')";

    if ($conn->query($sql) === true) {
        echo "User registered successfully";
    } else {
        echo "Error";
    }

    // $conn->close();
}
?>
