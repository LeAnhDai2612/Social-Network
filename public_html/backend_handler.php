<?php
if (session_status() === PHP_SESSION_NONE) {
    session_name('USER_SESSION');
    session_start();
}

$fileName = $_GET['file'] ?? null;
$params = $_GET['params'] ?? null;

$response = ['result' => ['success' => false, 'error' => 'Thiếu dữ liệu đầu vào']];

if ($fileName) {
    $filePath = '../core/' . basename($fileName);

    if (file_exists($filePath)) {
        include $filePath;

        if (function_exists('execute')) {
            $result = $params ? execute($params) : execute();
            $response = ['result' => $result];
        } else {
            $response = ['result' => ['success' => false, 'error' => 'Hàm execute không tồn tại']];
        }
    } else {
        $response = ['result' => ['success' => false, 'error' => 'File không tồn tại']];
    }
}

header('Content-Type: application/json');
echo json_encode($response);
?>
