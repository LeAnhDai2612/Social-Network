<?php
require_once("db_functions.php");
$conn = connect_to_db();
session_name('USER_SESSION');

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: register.php');
    exit;
}

function validateField($field, $name, $minLength, $maxLength = false, $numeric = false, $email = false)
{
    global $errors, $conn;

    if (!isset($_POST[$field]) || empty($_POST[$field])) {
        $errors[] = "$name is required.";
        return;
    }

    $value = trim($_POST[$field]);

    if (in_array($field, ['email', 'phone_number', 'username']) && does_value_exist($conn, 'users_table', $field, $value)) {
        $errors[] = "$name already exists. Please choose a different $name.";
        return;
    }

    if ($numeric) {
        if (!is_numeric($value) || strlen($value) < $minLength || strlen($value) > $maxLength) {
            $errors[] = "$name must be numeric and between $minLength and $maxLength digits.";
            return;
        }
    }

    if ($email && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid $name format.";
        return;
    }

    if ($field === 'username' && !preg_match('/^[a-zA-Z0-9._]+$/', $value)) {
        $errors[] = "$name may only contain letters, numbers, periods, and underscores.";
        return;
    }

    if ($maxLength) {
        if (strlen($value) < $minLength || strlen($value) > $maxLength) {
            $errors[] = "$name must be between $minLength and $maxLength characters.";
            return;
        }
    } else {
        if (strlen($value) < $minLength) {
            $errors[] = "$name must be at least $minLength characters long.";
            return;
        }
    }
}

$errors = [];

validateField('email', 'Email', 1, 255, false, true);
validateField('phone_number', 'Phone number', 3, 15, true);
validateField('full_name', 'Full name', 3, 50);
validateField('username', 'Username', 1, 30);
validateField('password', 'Password', 3);

if (!empty($errors)) {
    echo json_encode($errors);
    return;
}

// Gom thông tin thành 1 session "pending_user"
$_SESSION['pending_user'] = [
    'email' => strtolower($_POST['email']),
    'phone_number' => $_POST['phone_number'],
    'full_name' => $_POST['full_name'],
    'username' => strtolower($_POST['username']),
    'hashed_password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
    'role' => 'user' // Thêm dòng này
];


// Sau khi validate, chuyển sang bước gửi OTP
header('Location: ../core/send_otp.php');
exit;
