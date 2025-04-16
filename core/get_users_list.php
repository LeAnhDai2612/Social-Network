<?php
require_once 'db_functions.php';
if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json');

$current_user_id = $_SESSION['user_id'] ?? null;
$pdo = connect_to_db();

if (!$current_user_id) {
    echo json_encode(["result" => []]);
    exit;
}

$stmt = $pdo->query("SELECT id, username, display_name, profile_picture_path FROM users_table");
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

$filtered = array_filter($results, function ($user) use ($pdo, $current_user_id) {
    return !is_user_blocked($pdo, $current_user_id, $user['id']) &&
           !is_user_blocked($pdo, $user['id'], $current_user_id);
});

echo json_encode(["result" => array_values($filtered)]);
exit;
