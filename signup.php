<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    
    if (strlen($username) >= 3 && filter_var($email, FILTER_VALIDATE_EMAIL) && strlen($password) >= 6) {
        try {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$username, $email, $hashed_password]);
            header('Location: login.php');
            exit;
        } catch (PDOException $e) {
            $error = "Username or email already taken";
        }
    } else {
        $error = "Invalid input. Ensure username is 3+ characters, email is valid, and password is 6+ characters.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Twitter Clone</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Arial', sans-serif; }
        body { background-color: #f0f2f5; display: flex; justify-content: center; align-items: center; height: 100vh; }
        .signup-container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
        h2 { text-align: center; color: #1da1f2; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; color: #333; }
        .form-group input { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 16px; }
        button { width: 100%; background-color: #1da1f2; color: white; border: none; padding: 12px; border-radius: 20px; cursor: pointer; font-weight: bold; }
        button:hover { background-color: #0d8bdc; }
        .error { color: red; text-align: center; margin-bottom: 15px; }
        .login-link { text-align: center; margin-top: 15px; }
        .login-link a { color: #1da1f2; text-decoration: none; }
        .login-link a:hover { text-decoration: underline; }
        @media (max-width: 600px) { .signup-container { padding: 20px; max-width: 90%; } }
    </style>
</head>
<body>
    <div class="signup-container">
        <h2>Sign Up</h2>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Sign Up</button>
        </form>
        <div class="login-link">
            <p>Already have an account? <a href="javascript:goToLogin()">Login</a></p>
        </div>
    </div>
    <script>
        function goToLogin() { window.location.href = 'login.php'; }
    </script>
</body>
</html>
