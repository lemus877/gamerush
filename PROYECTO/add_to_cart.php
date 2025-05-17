<?php
include 'includes/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("SELECT * FROM cart WHERE user_id=? AND product_id=?");
$stmt->execute([$user_id, $product_id]);
$item = $stmt->fetch();

if ($item) {
    $pdo->prepare("UPDATE cart SET quantity = quantity + 1 WHERE id = ?")->execute([$item['id']]);
} else {
    $pdo->prepare("INSERT INTO cart (user_id, product_id) VALUES (?, ?)")->execute([$user_id, $product_id]);
}

header('Location: index.php');
?>
