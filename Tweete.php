<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['content'])) {
        $content = trim($_POST['content']);
        if (!empty($content) && strlen($content) <= 280) {
            $stmt = $pdo->prepare("INSERT INTO tweets (user_id, content) VALUES (?, ?)");
            $stmt->execute([$_SESSION['user_id'], $content]);
        }
    } elseif (isset($_POST['delete_tweet_id'])) {
        $tweet_id = $_POST['delete_tweet_id'];
        $stmt = $pdo->prepare("DELETE FROM tweets WHERE id = ? AND user_id = ?");
        $stmt->execute([$tweet_id, $_SESSION['user_id']]);
    }
}

header('Location: dashboard.php');
exit;
?>
