<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}
session_name('USER_SESSION');

session_start();
require_once("db_functions.php");
$pdo = connect_to_db();

$errors = array();

if (!isset($_SESSION['user_id'])) {
    echo "User session not found.";
    exit;
}

$user_id = $_SESSION['user_id'];

// Validate profile picture
if (isset($_FILES['profile_picture_picker']) && !empty($_FILES['profile_picture_picker']['name'])) {
    $allowed_extensions = array("jpg", "jpeg", "png", "bmp");
    $pfp_file_ext = strtolower(pathinfo($_FILES['profile_picture_picker']['name'], PATHINFO_EXTENSION));

    if (!in_array($pfp_file_ext, $allowed_extensions)) {
        $errors[] = "Invalid file extension. Only JPG, JPEG, PNG, and BMP files are allowed.";
    } else {
        $upload_path = '../public_html/uploads/';
        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0755, true);
        }
        $file_name = uniqid('avatar_', true) . '.' . $pfp_file_ext;
        $file_path = $upload_path . $file_name;

        if (!move_uploaded_file($_FILES['profile_picture_picker']['tmp_name'], $file_path)) {
            $errors[] = "Failed to upload the profile picture.";
        }
    }
}

// Validate display name
if (isset($_POST['user_display_name']) && !empty($_POST['user_display_name'])) {
    $user_display_name = trim($_POST['user_display_name']);
    if (strlen($user_display_name) < 1 || strlen($user_display_name) > 30) {
        $errors[] = "Display name must be between 1 and 30 characters long.";
    }
} else {
    $errors[] = "Display name is required.";
}

// Validate bio
$bio = "";
if (isset($_POST['bio']) && !empty($_POST['bio'])) {
    $bio = trim($_POST['bio']);
    if (strlen($bio) > 150) {
        $errors[] = "Bio must not exceed 150 characters.";
    }
}

if (!empty($errors)) {
    foreach ($errors as $error) {
        echo $error . "<br>";
    }
    exit;
}

// Update user profile
$stmt = $pdo->prepare("UPDATE users_table SET display_name = ?, bio = ?" . (isset($file_name) ? ", profile_picture_path = ?" : "") . " WHERE id = ?");
$params = isset($file_name)
    ? [$user_display_name, $bio, 'uploads/' . $file_name, $user_id]
    : [$user_display_name, $bio, $user_id];

if ($stmt->execute($params)) {
    $_SESSION['user_display_name'] = $user_display_name;
    $_SESSION['user_bio'] = $bio;
    if (isset($file_name)) {
        $_SESSION['user_profile_picture_path'] = 'uploads/' . $file_name;
    }

    header('Location: index.php');
    exit;
} else {
    echo "Something went wrong while updating your profile.";
}
