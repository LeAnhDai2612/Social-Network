<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('../vendor/autoload.php');

use Cloudinary\Configuration\Configuration;

define('DB_CONFIG', [
    'host' => 'localhost',
    'db' => 'socialnetwork_db',
    'user' => 'root',
    'pass' => '',
]);

// Cấu hình Cloudinary
Configuration::instance([
    'cloud' => [
        'cloud_name' => '',
        'api_key' => '',
        'api_secret' => '',
    ],
    'url' => [
        'secure' => true
    ]
]);

// ✅ Kết nối database PDO
try {
    $conn = new PDO(
        "mysql:host=" . DB_CONFIG['host'] . ";dbname=" . DB_CONFIG['db'],
        DB_CONFIG['user'],
        DB_CONFIG['pass']
    );
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("❌ Lỗi kết nối CSDL: " . $e->getMessage());
}
?>
