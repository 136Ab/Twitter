<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if (!isset($_GET['tweet_id'])) {
    header('Location: dashboard.php');
    exit;
}

$tweet_id = $_GET['tweet_id'];

// Fetch tweet
$stmt = $pdo->prepare("SELECT t.*, u.username FROM tweets t JOIN users u ON t.user_id = u.id WHERE t.id = ?");
$stmt->execute([$tweet_id]);
$tweet = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$tweet) {
    header('Location: dashboard.php');
    exit;
}

// Fetch comments
$stmt = $pdo->prepare("SELECT c.*, u.username FROM comments c JOIN users u ON c.user_id = u.id WHERE c.tweet_id = ? ORDER BY c.created_at");
$stmt->execute([$tweet_id]);
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle comment posting
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['content'])) {
    $content = trim($_POST['content']);
    if (!empty($content) && strlen($content) <= 280) {
        $stmt = $pdo->prepare("INSERT INTO comments (user_id, tweet_id, content) VALUES (?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $tweet_id, $content]);
        header('Location: comment.php?tweet_id=' . $tweet_id);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comments - Twitter Clone</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Arial', sans-serif; }
        body { background-color: #f0f2f5; color: #333; }
        .container { max-width: 800px; margin: 0 auto; padding: 20px; }
        .header { background-color: #1da1f2; color: white; padding: 15px; text-align: center; }
        .tweet { background: white; padding: 15px; border-radius: 10px; margin: 20px 0; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .tweet .username { font-weight: bold; color: #1da1f2; }
        .tweet .timestamp { color: #777; font-size: 14px; }
        .comment-box { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); margin: 20px 0; }
        .comment-box textarea { width: 100%; border: 1px solid #ddd; border-radius: 5px; padding: 10px; resize: none; font-size: 16px; }
        .comment-box button { background-color: #1da1f2; color: white; border: none; padding: 10px 20px; border-radius: 20px; cursor: pointer; font-weight: bold; margin-top: 10px; }
        .comment-box button:hover { background-color: #0d8bdc; }
        .comment { background: #f9f9f9; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .comment .username { font-weight: bold; color: #1da1f2; }
        .comment .timestamp { color: #777; font-size: 12px; }
        .nav { margin: 20px 0; text-align: center; }
        .nav a { color: #1da1f2; text-decoration: none; font-weight: bold; }
        .nav a:hover { text-decoration: underline; }
        @media (max-width: 600px) { .container { padding: 10px; } }
    </style>
</head>
<body>
    <div class="header">
        <h1>Comments</h1>
    </div>
    <div class="container">
        <div class="nav">
            <a href="javascript:goToDashboard()">Back to Dashboard</a>
        </div>
        <div class="tweet">
            <div class="username"><?php echo htmlspecialchars($tweet['username']); ?></div>
            <p><?php echo htmlspecialchars($tweet['content']); ?></p>
            <div class="timestamp"><?php echo $tweet['created_at']; ?></div>
        </div>
        <div class="comment-box">
            <form method="POST">
                <textarea name="content" placeholder="Add a comment..." rows="3" maxlength="280"></textarea>
                <button type="submit">Comment</button>
            </form>
        </div>
        <div class="comments">
            <?php foreach ($comments as $comment): ?>
                <div class="comment">
                    <div class="username"><?php echo htmlspecialchars($comment['username']); ?></div>
                    <p><?php echo htmlspecialchars($comment['content']); ?></p>
                    <div class="timestamp"><?php echo $comment['created_at']; ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <script>
        function goToDashboard() { window.location.href = 'dashboard.php'; }
    </script>
</body>
</html>
