<?php
header('Content-Type: application/json');
$input_data = json_decode(file_get_contents('php://input'), true);
$user_input = $input_data['input'] ?? '';

if (empty($user_input)) {
    echo json_encode(['success' => false, 'error' => 'Không có nội dung đầu vào.']);
    exit;
}

$data = array("query" => $user_input);
$apiUrl = 'https://59e4-42-118-117-77.ngrok-free.app/chat';

$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json'
));
curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
curl_setopt($ch, CURLOPT_FORBID_REUSE, true);
// curl_setopt($ch, CURLOPT_TIMEOUT, 45); // Tăng timeout nếu cần

$response = curl_exec($ch);
$error_message = null;
$ai_response = null;
$success = false;

if (curl_errno($ch)) {
    $error_message = 'Lỗi cURL: ' . curl_error($ch);
} else {
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $response_data = json_decode($response, true);

    if ($httpCode !== 200) {
        $error_message = "Lỗi từ API AI: " . ($response_data["error"] ?? $response ?: "Không rõ (HTTP $httpCode)");
    } elseif (isset($response_data['response'])) {
        $ai_response = $response_data['response'];
        $success = true;
    } else {
         $error_message = "Phản hồi không hợp lệ từ AI.";
    }
}
curl_close($ch);

if ($success) {
    echo json_encode(['success' => true, 'response' => $ai_response]);
} else {
    echo json_encode(['success' => false, 'error' => $error_message ?: "Lỗi không xác định."]);
}
exit;
?>