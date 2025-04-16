<?php
if (empty($_SERVER['HTTP_REFERER'])) {
    header('Location: index.php');
    exit;
}

$base_path = dirname(__DIR__) . '/core/';
$file = $_GET['filename'] ?? '';
$file = basename($file);
$file_to_execute = $base_path . $file;

if (!file_exists($file_to_execute)) {
    echo "<h3 style='color: red;'>❌ File not found: " . htmlspecialchars($file_to_execute) . "</h3>";
    exit;
}

ob_start();
include $file_to_execute;
ob_end_flush();
exit;
?>
