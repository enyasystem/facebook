<!DOCTYPE html>
<html>
<head>
    <title>VYBZ Social</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.7.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <script src="js/main.js"></script>
</head>
<body>
    <div class="container">
        <h1>Welcome to VYBZ Social</h1>

        <div class="registration-form">
            <!-- Existing registration form code -->

            <h2>Log In</h2>
            <form action="login.php" method="POST">
                <div class="mb-3">
                    <input type="email" name="email" class="form-control" placeholder="Email" required>
                </div>
                <div class="mb-3">
                    <input type="password" name="password" class="form-control" placeholder="Password" >
                </div>
                <button type="submit" class="btn btn-primary">Log In</button>
            </form>
        </div>

        <div class="registration-form">
            <h2>Create an Account</h2>
            <form action="register.php" method="POST">
                <div class="mb-3">
                    <input type="text" name="username" class="form-control" placeholder="Username" required>
                </div>
                <div class="mb-3">
                    <input type="email" name="email" class="form-control" placeholder="Email" required>
                </div>
                <div class="mb-3">
                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                </div>
                <button type="submit" class="btn btn-primary">Register</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.7.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
