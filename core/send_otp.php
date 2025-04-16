<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';
require_once 'db_functions.php';
session_name('USER_SESSION');

session_start();

$conn = connect_to_db();

// ======== 1. Gửi OTP khi đăng ký tài khoản ========
if (isset($_SESSION['pending_user'])) {
    $user = $_SESSION['pending_user'];
    $email = $user['email'];
    $full_name = $user['full_name'] ?? 'User';

    $otp_code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    $expires_at = date("Y-m-d H:i:s", strtotime("+5 minutes"));

    $stmt = $conn->prepare("DELETE FROM otp_table WHERE email = ?");
    $stmt->execute([$email]);

    $stmt = $conn->prepare("INSERT INTO otp_table (email, otp_code, expires_at) VALUES (?, ?, ?)");
    $stmt->execute([$email, $otp_code, $expires_at]);

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = '';  // Thay bằng email thật
        $mail->Password = '';   // App password
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';

        $mail->setFrom('', 'Social App');
        $mail->addAddress($email, $full_name);
        $mail->isHTML(true);
        $mail->Subject = 'Xác thực tài khoản của bạn';
        $mail->Body = "<p>Xin chào <strong>$full_name</strong>,</p>
                       <p>Mã xác thực OTP của bạn là: <strong>$otp_code</strong></p>
                       <p>Mã có hiệu lực trong 5 phút.</p>";

        $mail->send();

        // Gửi từ đăng ký
        if (!isset($_GET['resend'])) {
            header("Location: ../public_html/verify_otp.php");
        } else {
            header("Location: verify_otp.php?resend=success");
        }
        exit;
    } catch (Exception $e) {
        $error_msg = urlencode("Mailer Error: " . $mail->ErrorInfo);
        header("Location: verify_otp.php?error=$error_msg");
        exit;
    }
}

// ======== 2. Gửi OTP để Reset Mật Khẩu ========
if (isset($_POST['reset_email'])) {
    $input = trim($_POST['reset_email']);

    $stmt = $conn->prepare("SELECT * FROM users_table WHERE email = ? OR phone_number = ?");
    $stmt->execute([$input, $input]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        header("Location: login.php?error=Không tìm thấy tài khoản.");
        exit;
    }

    $email = $user['email'];
    $full_name = $user['full_name'] ?? 'User';

    $otp_code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    $expires_at = date("Y-m-d H:i:s", strtotime("+5 minutes"));

    $stmt = $conn->prepare("DELETE FROM otp_table WHERE email = ?");
    $stmt->execute([$email]);

    $stmt = $conn->prepare("INSERT INTO otp_table (email, otp_code, expires_at) VALUES (?, ?, ?)");
    $stmt->execute([$email, $otp_code, $expires_at]);

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = '';
        $mail->Password = '';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';

        $mail->setFrom('', 'Social App');
        $mail->addAddress($email, $full_name);
        $mail->isHTML(true);
        $mail->Subject = 'Mã OTP đặt lại mật khẩu';
        $mail->Body = "<p>Xin chào <strong>$full_name</strong>,</p>
                       <p>Mã OTP để đặt lại mật khẩu của bạn là: <strong>$otp_code</strong></p>
                       <p>Mã có hiệu lực trong 5 phút.</p>";

        $mail->send();

        $_SESSION['reset_target_email'] = $email;
        header("Location: login.php?reset_otp_sent=1");
        exit;
    } catch (Exception $e) {
        $error_msg = urlencode("Lỗi gửi OTP: " . $mail->ErrorInfo);
        header("Location: login.php?error=$error_msg");
        exit;
    }
}

// Nếu không có dữ liệu hợp lệ thì quay về login
header("Location: login.php?error=Không xác định được yêu cầu.");
exit;
