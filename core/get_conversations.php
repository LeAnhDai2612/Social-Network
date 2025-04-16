<?php
require_once 'db_functions.php';
session_name('USER_SESSION');

session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Chưa đăng nhập']);
    exit();
}

$conn = connect_to_db();
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT 
        u.id,
        u.full_name AS name,
        u.profile_picture_path,
        (
            SELECT content FROM messages_table m 
            WHERE (m.sender_id = u.id AND m.receiver_id = ?) 
               OR (m.sender_id = ? AND m.receiver_id = u.id)
            ORDER BY created_at DESC LIMIT 1
        ) AS last_message,
        (
            SELECT MAX(created_at) FROM messages_table m
            WHERE (m.sender_id = u.id AND m.receiver_id = ?) 
               OR (m.sender_id = ? AND m.receiver_id = u.id)
        ) AS last_time
    FROM users_table u
    WHERE u.id != ? AND EXISTS (
        SELECT 1 FROM messages_table m 
        WHERE (m.sender_id = u.id AND m.receiver_id = ?) 
           OR (m.sender_id = ? AND m.receiver_id = u.id)
    )
    ORDER BY last_time DESC
");



$stmt->execute([$user_id, $user_id, $user_id, $user_id, $user_id, $user_id, $user_id]);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($users);
