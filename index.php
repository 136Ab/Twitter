<?php
session_start();

// Check if user is logged in
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
} else {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Twitter Clone</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }
        body {
            background-color: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #333;
        }
        .loading-container {
            text-align: center;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .loading-container h1 {
            color: #1da1f2;
            margin-bottom: 20px;
        }
        .loading-container p {
            color: #555;
        }
        @media (max-width: 600px) {
            .loading-container {
                padding: 20px;
                margin: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="loading-container">
        <h1>Twitter Clone</h1>
        <p>Redirecting...</p>
    </div>
    <script>
        // Fallback JavaScript redirection
        if (window.location.pathname.includes('index.php')) {
            <?php if (isset($_SESSION['user_id'])): ?>
                window.location.href = 'dashboard.php';
            <?php else: ?>
                window.location.href = 'login.php';
            <?php endif; ?>
        }
    </script>
</body>
</html>
