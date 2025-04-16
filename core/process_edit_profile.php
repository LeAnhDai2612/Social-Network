<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../public_html/index.php');
    exit;
}

session_name('USER_SESSION');
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../public_html/login.php');
    exit;
}

require_once("db_functions.php");
require_once("utility_functions.php");

$pdo = connect_to_db();
$errors = array();
$user_id = $_SESSION['user_id'];


$full_name = null;
if (isset($_POST['full_name'])) {
    $full_name = trim($_POST['full_name']);
    if (strlen($full_name) > 100) {
        $errors['full_name'] = "Họ và tên không được vượt quá 100 ký tự.";
    }
}

$user_display_name = null;
if (isset($_POST['user_display_name']) && !empty(trim($_POST['user_display_name']))) {
    $user_display_name = trim($_POST['user_display_name']);
    if (strlen($user_display_name) < 1 || strlen($user_display_name) > 50) {
        $errors['display_name'] = "Tên hiển thị phải từ 1 đến 50 ký tự.";
    }
} else {
    $errors['display_name'] = "Tên hiển thị là bắt buộc.";
}

$username = null;
if (isset($_POST['username']) && !empty(trim($_POST['username']))) {
    $username = trim($_POST['username']);
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $errors['username'] = "Tên người dùng chỉ được chứa chữ cái (a-z, A-Z), số (0-9) và dấu gạch dưới (_).";
    }
    if (strlen($username) < 3 || strlen($username) > 30) {
        $errors['username'] = "Tên người dùng phải từ 3 đến 30 ký tự.";
    }
    if ($username !== ($_SESSION['user_username'] ?? '')) {
        if (is_username_taken($pdo, $username, $user_id)) {
            $errors['username'] = "Tên người dùng này đã được sử dụng. Vui lòng chọn tên khác.";
        }
    }
} else {
    $errors['username'] = "Tên người dùng là bắt buộc.";
}

$bio = null;
if (isset($_POST['bio'])) {
    $bio = trim($_POST['bio']);
    if (strlen($bio) > 200) {
        $errors['bio'] = "Giới thiệu không được vượt quá 200 ký tự.";
    }
}

$new_image_file = $_FILES['profile_picture_picker'] ?? null;
$is_image_updated = $new_image_file && !empty($new_image_file['name']) && $new_image_file['error'] === UPLOAD_ERR_OK;
$new_profile_picture_path = null;
$new_image_public_id = null; // Thêm biến cho public_id nếu dùng Cloudinary

if ($is_image_updated) {
    // Thay thế bằng logic upload của bạn (ví dụ: Cloudinary)
    $target_dir_cloudinary = 'socialnetwork/profile-pictures'; // Thư mục trên Cloudinary
    $current_public_id = get_image_public_id_from_user($pdo, $user_id); // Lấy public_id cũ nếu có

    $upload_result = upload_image_to_cloudinary($new_image_file, $target_dir_cloudinary, $current_public_id);

    if ($upload_result && !empty($upload_result['secure_url'])) {
        $new_profile_picture_path = $upload_result['secure_url'];
        $new_image_public_id = $upload_result['public_id'] ?? null;
        // Không cần xóa ảnh cũ trên Cloudinary nếu dùng cùng public_id (overwrite=true)
    } else {
         $errors['profile_picture'] = "Lỗi khi tải ảnh lên Cloudinary.";
         $is_image_updated = false;
    }

    /* // Logic upload cục bộ (nếu không dùng Cloudinary)
    $target_dir = "../public_html/uploads/profile_pictures/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    $imageFileType = strtolower(pathinfo($new_image_file["name"], PATHINFO_EXTENSION));
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($imageFileType, $allowed_types)) {
        $errors['profile_picture'] = "Chỉ cho phép ảnh JPG, JPEG, PNG & GIF.";
        $is_image_updated = false;
    } else {
        $new_filename = $user_id . '_' . time() . '.' . $imageFileType;
        $target_file = $target_dir . $new_filename;
        $check = getimagesize($new_image_file["tmp_name"]);

        if($check !== false) {
            if ($new_image_file["size"] > 5000000) { // Giới hạn 5MB
                 $errors['profile_picture'] = "Kích thước ảnh quá lớn (tối đa 5MB).";
                 $is_image_updated = false;
            } else {
                if (move_uploaded_file($new_image_file["tmp_name"], $target_file)) {
                    $new_profile_picture_path = "uploads/profile_pictures/" . $new_filename;
                    $old_picture = $_SESSION['user_profile_picture_path'] ?? null;
                    if ($old_picture && $old_picture !== 'images/default_avatar.png' && file_exists('../public_html/' . $old_picture)) {
                         @unlink('../public_html/' . $old_picture); // @ để ẩn lỗi nếu không xóa được
                    }
                } else {
                    $errors['profile_picture'] = "Lỗi khi di chuyển tệp ảnh tải lên.";
                    $is_image_updated = false;
                }
            }
        } else {
            $errors['profile_picture'] = "Tệp không phải là ảnh hợp lệ.";
            $is_image_updated = false;
        }
    }
    */
}


if (!empty($errors)) {
    $_SESSION['edit_profile_errors'] = $errors;
    $_SESSION['edit_profile_old_input'] = [
        'full_name' => $_POST['full_name'] ?? '',
        'user_display_name' => $_POST['user_display_name'] ?? '',
        'username' => $_POST['username'] ?? '',
        'bio' => $_POST['bio'] ?? '',
    ];
    header("Location: ../public_html/edit_profile.php");
    exit;
}

$current_full_name = $_SESSION['user_full_name'] ?? '';
$current_display_name = $_SESSION['user_display_name'] ?? '';
$current_username = $_SESSION['user_username'] ?? '';
$current_bio = $_SESSION['user_bio'] ?? '';


$is_full_name_updated = ($full_name !== null && $full_name !== $current_full_name);
$is_display_name_updated = ($user_display_name !== null && $user_display_name !== $current_display_name);
$is_username_updated = ($username !== null && $username !== $current_username);
$is_bio_updated = ($bio !== null && $bio !== $current_bio);


if (!$is_image_updated && !$is_full_name_updated && !$is_display_name_updated && !$is_username_updated && !$is_bio_updated) {
    $_SESSION['edit_profile_success'] = "Không có thay đổi nào được thực hiện."; // Thêm thông báo nhẹ
    header("Location: " . ($_POST['redirect_url'] ?? '../public_html/edit_profile.php'));
    exit;
}

$update_data = [];
if ($is_full_name_updated) {
    $update_data['full_name'] = $full_name;
}
if ($is_display_name_updated) {
    $update_data['display_name'] = $user_display_name;
}
if ($is_username_updated) {
    $update_data['username'] = $username;
}
if ($is_bio_updated) {
    $update_data['bio'] = $bio;
}
if ($is_image_updated && $new_profile_picture_path !== null) {
    $update_data['profile_picture_path'] = $new_profile_picture_path;
    if ($new_image_public_id !== null) { // Chỉ cập nhật nếu dùng Cloudinary và có public_id mới
         $update_data['image_public_id'] = $new_image_public_id;
    }
}

$update_successful = false;
if (!empty($update_data)) {
    try {
         $update_successful =  update_user_profile($pdo, $user_id, $update_data);
    } catch (Exception $e) {
         $errors['database'] = $e->getMessage();
         $update_successful = false;
    }

} elseif ($is_image_updated && $new_profile_picture_path === null) {
     $update_successful = false;
     if (empty($errors['profile_picture'])) { // Thêm lỗi chung nếu chưa có lỗi cụ thể
         $errors['profile_picture'] = "Xảy ra lỗi không xác định khi xử lý ảnh.";
     }
} else {
    $update_successful = true;
}


if ($update_successful) {
    if ($is_full_name_updated) {
        $_SESSION['user_full_name'] = $full_name;
    }
    if ($is_display_name_updated) {
        $_SESSION['user_display_name'] = $user_display_name;
    }
    if ($is_username_updated) {
        $_SESSION['user_username'] = $username;
    }
    if ($is_bio_updated) {
        $_SESSION['user_bio'] = $bio;
    }
    if ($is_image_updated && $new_profile_picture_path !== null) {
        $_SESSION['user_profile_picture_path'] = $new_profile_picture_path;
    }

    $_SESSION['edit_profile_success'] = "Hồ sơ đã được cập nhật thành công!";
    unset($_SESSION['edit_profile_errors']);
    unset($_SESSION['edit_profile_old_input']);

} else {
    if (empty($_SESSION['edit_profile_errors'])) { // Nếu chưa có lỗi từ validation/upload/DB exception
        if(!isset($errors['database'])) { // Chỉ thêm lỗi chung nếu chưa có lỗi DB cụ thể
            $errors['database'] = "Đã xảy ra lỗi khi cập nhật hồ sơ. Vui lòng thử lại.";
        }
    }
     $_SESSION['edit_profile_errors'] = $errors; // Lưu lại lỗi vào session
    $_SESSION['edit_profile_old_input'] = [
        'full_name' => $_POST['full_name'] ?? '',
        'user_display_name' => $_POST['user_display_name'] ?? '',
        'username' => $_POST['username'] ?? '',
        'bio' => $_POST['bio'] ?? '',
    ];
}

$redirect_to = $_POST['redirect_url'] ?? '../public_html/edit_profile.php';
header("Location: " . $redirect_to);
exit;
?>