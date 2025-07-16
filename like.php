<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tweet_id'])) {
    $tweet_id = $_POST['tweet_id'];
    $user_id = $_SESSION['user_id'];
    
    // Check if already liked
    $stmt = $pdo->prepare("SELECT * FROM likes WHERE user_id = ? AND tweet_id = ?");
    $stmt->execute([$user_id, $tweet_id]);
    
    if ($stmt->fetch()) {
        // Unlike
        $stmt = $pdo->prepare("DELETE FROM likes WHERE user_id = ? AND tweet_id = ?");
        $stmt->execute([$user_id, $tweet_id]);
    } else {
        // Like
        $stmt = $pdo->prepare("INSERT INTO likes (user_id, tweet_id) VALUES (?, ?)");
        $stmt->execute([$user_id, $tweet_id]);
    }
}

header('Location: dashboard.php');
exit;
?>
