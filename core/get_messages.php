<?php
require_once 'db_functions.php';
session_name('USER_SESSION');

session_start();

$conn = connect_to_db();

$me = $_SESSION['user_id'];
$other = $_GET['user_id'] ?? null;

if (!$other) {
    echo json_encode(['success' => false, 'messages' => []]);
    exit();
}
$update = $conn->prepare("
    UPDATE messages_table
    SET is_read = 1
    WHERE sender_id = ? AND receiver_id = ? AND is_read = 0
");
$update->execute([$other, $me]);
$stmt = $conn->prepare("
    SELECT id, sender_id, receiver_id, content, media_path, media_type, created_at FROM messages_table
    WHERE (
        sender_id = ? AND receiver_id = ? AND deleted_by_sender = 0
    ) OR (
        sender_id = ? AND receiver_id = ? AND deleted_by_receiver = 0
    )
    ORDER BY created_at ASC
");
$stmt->execute([$me, $other, $other, $me]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($messages as &$msg) { 
    
    if (!empty($msg['media_path'])) {
            $msg['media_url'] = $msg['media_path']; 
        } else {
            $msg['media_url'] = null;
        }
    }
echo json_encode(['success' => true, 'messages' => $messages]);
