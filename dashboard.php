<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Fetch tweets
$stmt = $pdo->prepare("
    SELECT t.*, u.username, u.profile_picture 
    FROM tweets t 
    JOIN users u ON t.user_id = u.id 
    WHERE t.user_id = ? OR t.user_id IN (
        SELECT following_id FROM follows WHERE follower_id = ?
    )
    ORDER BY t.created_at DESC
");
$stmt->execute([$_SESSION['user_id'], $_SESSION['user_id']]);
$tweets = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Twitter Clone</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Arial', sans-serif; }
        body { background-color: #f0f2f5; color: #333; }
        .container { max-width: 800px; margin: 0 auto; padding: 20px; }
        .header { background-color: #1da1f2; color: white; padding: 15px; text-align: center; position: sticky; top: 0; z-index: 100; }
        .nav { margin: 20px 0; display: flex; justify-content: space-around; }
        .nav a { color: #1da1f2; text-decoration: none; font-weight: bold; }
        .nav a:hover { text-decoration: underline; }
        .tweet-box { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); margin: 20px 0; }
        .tweet-box textarea { width: 100%; border: 1px solid #ddd; border-radius: 5px; padding: 10px; resize: none; font-size: 16px; }
        .tweet-box button { background-color: #1da1f2; color: white; border: none; padding: 10px 20px; border-radius: 20px; cursor: pointer; font-weight: bold; margin-top: 10px; }
        .tweet-box button:hover { background-color: #0d8bdc; }
        .tweet { background: white; padding: 15px; border-radius: 10px; margin: 10px 0; box-shadow: 0 1px 3px rgba(0,0,0,0.1); display: flex; gap: 15px; }
        .tweet img { width: 50px; height: 50px; border-radius: 50%; object-fit: cover; }
        .tweet-content { flex: 1; }
        .tweet-content .username { font-weight: bold; color: #1da1f2; }
        .tweet-content .timestamp { color: #777; font-size: 14px; }
        .tweet-actions { margin-top: 10px; display: flex; gap: 20px; }
        .tweet-actions button { background: none; border: none; cursor: pointer; color: #555; }
        .tweet-actions button:hover { color: #1da1f2; }
        @media (max-width: 600px) { .container { padding: 10px; } .tweet img { width: 40px; height: 40px; } }
    </style>
</head>
<body>
    <div class="header">
        <h1>Twitter Clone</h1>
    </div>
    <div class="container">
        <div class="nav">
            <a href="javascript:goToProfile()">Profile</a>
            <a href="javascript:goToLogout()">Logout</a>
        </div>
        <div class="tweet-box">
            <form action="tweet.php" method="POST">
                <textarea name="content" placeholder="What's happening?" rows="4" maxlength="280"></textarea>
                <button type="submit">Tweet</button>
            </form>
        </div>
        <div class="tweets">
            <?php foreach ($tweets as $tweet): ?>
                <div class="tweet">
                    <img src="<?php echo htmlspecialchars($tweet['profile_picture']); ?>" alt="Profile">
                    <div class="tweet-content">
                        <span class="username"><?php echo htmlspecialchars($tweet['username']); ?></span>
                        <span class="timestamp"><?php echo $tweet['created_at']; ?></span>
                        <p><?php echo htmlspecialchars($tweet['content']); ?></p>
                        <div class="tweet-actions">
                            <form action="like.php" method="POST" style="display:inline;">
                                <input type="hidden" name="tweet_id" value="<?php echo $tweet['id']; ?>">
                                <button type="submit">Like</button>
                            </form>
                            <button onclick="goToComment(<?php echo $tweet['id']; ?>)">Comment</button>
                            <?php if ($tweet['user_id'] == $_SESSION['user_id']): ?>
                                <form action="tweet.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="delete_tweet_id" value="<?php echo $tweet['id']; ?>">
                                    <button type="submit">Delete</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <script>
        function goToProfile() { window.location.href = 'profile.php'; }
        function goToLogout() { window.location.href = 'logout.php'; }
        function goToComment(tweetId) { window.location.href = 'comment.php?tweet_id=' + tweetId; }
    </script>
</body>
</html>
