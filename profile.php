<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Fetch user info
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch user tweets
$stmt = $pdo->prepare("SELECT * FROM tweets WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$tweets = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch follower and following counts
$followers = $pdo->prepare("SELECT COUNT(*) FROM follows WHERE following_id = ?");
$followers->execute([$_SESSION['user_id']]);
$follower_count = $followers->fetchColumn();

$following = $pdo->prepare("SELECT COUNT(*) FROM follows WHERE follower_id = ?");
$following->execute([$_SESSION['user_id']]);
$following_count = $following->fetchColumn();

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $bio = trim($_POST['bio']);
    $stmt = $pdo->prepare("UPDATE users SET bio = ? WHERE id = ?");
    $stmt->execute([$bio, $_SESSION['user_id']]);
    header('Location: profile.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Twitter Clone</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Arial', sans-serif; }
        body { background-color: #f0f2f5; color: #333; }
        .container { max-width: 800px; margin: 0 auto; padding: 20px; }
        .header { background-color: #1da1f2; color: white; padding: 15px; text-align: center; }
        .profile-header { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); text-align: center; margin-bottom: 20px; }
        .profile-header img { width: 100px; height: 100px; border-radius: 50%; object-fit: cover; margin-bottom: 10px; }
        .profile-header .username { font-size: 24px; font-weight: bold; color: #1da1f2; }
        .profile-header .bio { color: #555; margin: 10px 0; }
        .profile-header .stats { display: flex; justify-content: center; gap: 20px; margin-top: 10px; }
        .edit-profile { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .edit-profile textarea { width: 100%; border: 1px solid #ddd; border-radius: 5px; padding: 10px; resize: none; }
        .edit-profile button { background-color: #1da1f2; color: white; border: none; padding: 10px 20px; border-radius: 20px; cursor: pointer; font-weight: bold; margin-top: 10px; }
        .edit-profile button:hover { background-color: #0d8bdc; }
        .tweet { background: white; padding: 15px; border-radius: 10px; margin: 10px 0; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .tweet .timestamp { color: #777; font-size: 14px; }
        .nav { margin: 20px 0; text-align: center; }
        .nav a { color: #1da1f2; text-decoration: none; font-weight: bold; margin: 0 10px; }
        .nav a:hover { text-decoration: underline; }
        @media (max-width: 600px) { .container { padding: 10px; } .profile-header img { width: 80px; height: 80px; } }
    </style>
</head>
<body>
    <div class="header">
        <h1>Profile</h1>
    </div>
    <div class="container">
        <div class="nav">
            <a href="javascript:goToDashboard()">Home</a>
            <a href="javascript:goToLogout()">Logout</a>
        </div>
        <div class="profile-header">
            <img src="<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile">
            <div class="username"><?php echo htmlspecialchars($user['username']); ?></div>
            <div class="bio"><?php echo htmlspecialchars($user['bio'] ?? 'No bio yet'); ?></div>
            <div class="stats">
                <div><strong><?php echo $follower_count; ?></strong> Followers</div>
                <div><strong><?php echo $following_count; ?></strong> Following</div>
            </div>
        </div>
        <div class="edit-profile">
            <form method="POST">
                <textarea name="bio" placeholder="Update your bio" rows="4"><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                <button type="submit" name="update_profile">Update Profile</button>
            </form>
        </div>
        <div class="tweets">
            <?php foreach ($tweets as $tweet): ?>
                <div class="tweet">
                    <p><?php echo htmlspecialchars($tweet['content']); ?></p>
                    <div class="timestamp"><?php echo $tweet['created_at']; ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <script>
        function goToDashboard() { window.location.href = 'dashboard.php'; }
        function goToLogout() { window.location.href = 'logout.php'; }
    </script>
</body>
</html>
