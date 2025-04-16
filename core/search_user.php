<?php
require_once 'db_functions.php';
session_start();

$pdo = connect_to_db();
$current_user_id = $_SESSION['user_id'] ?? null;
$query = $_GET['q'] ?? '';

$stmt = $pdo->prepare("SELECT id, username, display_name, profile_picture_path FROM users_table 
                       WHERE username LIKE ? OR display_name LIKE ?");
$stmt->execute(["%$query%", "%$query%"]);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Lọc người đã bị chặn hoặc chặn mình
$filtered = array_filter($results, function ($user) use ($pdo, $current_user_id) {
    return !is_user_blocked($pdo, $current_user_id, $user['id']) &&
           !is_user_blocked($pdo, $user['id'], $current_user_id);
});

echo json_encode(array_values($filtered));
