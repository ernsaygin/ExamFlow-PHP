<?php
include './config/database.php'; // Includes PDO connection
session_start(); // Start session

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve data from the form
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Hash the password using SHA-1
    $hashed_password = sha1($password);

    // Find the user in the database and verify the password
    try {
        $stmt = $db->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
        $stmt->execute([$username, $hashed_password]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // If the user exists, log them in
        if ($user) {

            // Store user information in the session
            $_SESSION['id'] = $user['id'];
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            header('Location: questions.php');
        } else {
            echo "Invalid username or password.";
        }
    } catch (PDOException $e) {
        echo "An error occurred: " . $e->getMessage();
    }
}
?>

<main class="main container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h2 class="mb-4">Log In</h2>
            <form action="login.php" method="post">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary my-4">Log In</button>
            </form>
        </div>
    </div>
</main>
