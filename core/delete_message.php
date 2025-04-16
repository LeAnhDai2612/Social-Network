<?php
require_once 'db_functions.php';
if (session_status() === PHP_SESSION_NONE) {
    session_name('USER_SESSION');
    session_start();
}

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$message_id = $input['message_id'] ?? null;
$mode = $input['mode'] ?? null;
$user_id = $_SESSION['user_id'] ?? null;

if (!$message_id || !$mode || !$user_id) {
    echo json_encode(['success' => false, 'error' => 'Thiếu dữ liệu']);
    exit();
}

$conn = connect_to_db();

// Lấy thông tin người gửi/nhận của tin nhắn
$stmt = $conn->prepare("SELECT sender_id, receiver_id FROM messages_table WHERE id = ?");
$stmt->execute([$message_id]);
$message = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$message) {
    echo json_encode(['success' => false, 'error' => 'Không tìm thấy tin nhắn']);
    exit();
}

if ($mode === 'me') {
    if ($user_id == $message['sender_id']) {
        $conn->prepare("UPDATE messages_table SET deleted_by_sender = 1 WHERE id = ?")->execute([$message_id]);
    } elseif ($user_id == $message['receiver_id']) {
        $conn->prepare("UPDATE messages_table SET deleted_by_receiver = 1 WHERE id = ?")->execute([$message_id]);
    }
} elseif ($mode === 'both') {
    $conn->prepare("DELETE FROM messages_table WHERE id = ?")->execute([$message_id]);
}

echo json_encode(['success' => true]);
