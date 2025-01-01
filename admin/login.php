<?php
session_start();

// Constant username and password
$valid_username = "admin__";
$valid_password = "admin__999";

// Login process
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Compare username and password with constant values
    if ($username === $valid_username && $password === $valid_password) {
        $_SESSION['admin'] = $username;
        header('Location: exam_form.php'); // Redirect after successful login
    } else {
        $error = "Invalid username or password.";
    }
}
?>

<div class="login-box">
    <?php if (isset($error)): ?>
        <div class="alert alert-danger">
            <?= $error; ?>
        </div>
    <?php endif; ?>
    <form action="login.php" method="POST">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" class="form-control" id="username" name="username" placeholder="Enter your username" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Log In</button>
    </form>
</div>
