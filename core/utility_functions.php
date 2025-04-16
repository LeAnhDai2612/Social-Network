<?php
function get_base_url()
{
    return strtolower(substr($_SERVER["SERVER_PROTOCOL"], 0, strpos($_SERVER["SERVER_PROTOCOL"], '/'))) . '://' . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER["PHP_SELF"]), '/\\') . '/';
}

function redirect_if_not_logged_in()
{
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit();
    }
}
function get_active_page()
{
    $active_page = '';

    if (basename($_SERVER['PHP_SELF']) === 'index.php') {
        $active_page = 'feed';
    } elseif (basename($_SERVER['PHP_SELF']) === 'edit_profile.php') {
        $active_page = 'settings';
    } elseif (basename($_SERVER['PHP_SELF']) === 'user_profile.php' && isset($_GET['user_id']) && $_GET['user_id'] == $_SESSION['user_id']) {
        $active_page = 'profile';
    }

    return $active_page;
}

function add_transformation_parameters($original_url, $transformations) {
    if (empty($original_url) || empty($transformations) || !is_string($original_url) || !is_string($transformations)) {
        return $original_url;
    }

    $upload_marker = '/upload/';
    $upload_pos = strpos($original_url, $upload_marker);

    if ($upload_pos === false) {
        error_log("Cloudinary transformation error: '/upload/' not found in URL: " . $original_url);
        return $original_url;
    }

    $base_part = substr($original_url, 0, $upload_pos);
    $public_id_part = substr($original_url, $upload_pos + strlen($upload_marker));

    if(empty($public_id_part)) {
         error_log("Cloudinary transformation error: Public ID part is empty after '/upload/' in URL: " . $original_url);
        return $original_url;
    }

    if (substr($transformations, -1) !== '/') {
        $transformations .= '/';
    }
    if (substr($public_id_part, 0, 1) === '/') {
         $public_id_part = substr($public_id_part, 1);
    }

    $transformed_url = $base_part . $upload_marker . $transformations . $public_id_part;

    return $transformed_url;
}

?>