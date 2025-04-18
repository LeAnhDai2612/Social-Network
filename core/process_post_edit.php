<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('Asia/Ho_Chi_Minh');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}
session_name('USER_SESSION');

session_start();

require_once("db_functions.php");
$pdo = connect_to_db();

$errors = array();

if (isset($_POST['post_caption']) && !empty($_POST['post_caption'])) {
    $caption = trim($_POST['post_caption']);
    if (strlen($caption) > 2200) {
        $errors[] = "Caption must not exceed 2,200 characters.";
    }
}

if (!empty($errors)) {
    foreach ($errors as $error) {
        echo $error . "<br>";
    }
    exit;
}

$post_id = $_POST['post_modal_post_id'];
$result = update_post($pdo, $post_id, $caption);

if ($result) {
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
} else {
    echo "Something went wrong while updating the post";
    exit;
}
?>