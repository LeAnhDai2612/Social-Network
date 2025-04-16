<?php
session_name('USER_SESSION');

session_start();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quên mật khẩu · Social Network</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <main class="d-flex justify-content-center align-items-center vh-100 bg-light">
        <div class="card p-4 shadow-sm" style="min-width: 360px;">
            <h3 class="text-center mb-3">Quên mật khẩu</h3>
            <form action="../core/send_otp.php?type=reset" method="POST">
                <div class="mb-3">
                    <label for="email" class="form-label">Email hoặc SĐT</label>
                    <input type="text" name="reset_email" id="email" class="form-control" required>
                </div>
                <button class="btn btn-primary w-100" type="submit">Gửi mã OTP</button>
            </form>
            <div class="text-center mt-3">
                <a href="login.php">← Quay lại đăng nhập</a>
            </div>
        </div>
    </main>
</body>
</html>
