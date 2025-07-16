<?php
$host = 'localhost'; // Replace with your actual host
$dbname = 'dbycszby3a5wkv';
$username = 'uaozeqcbxyhyg';
$password = 'f4kld3wzz1v3';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
