<?php
function get_formatted_time_ago($created_at)
{
    date_default_timezone_set("Asia/Ho_Chi_Minh");
    $created_timestamp = strtotime($created_at);
    $current_timestamp = time();
    $time_diff = $current_timestamp - $created_timestamp;

    if ($time_diff < 0) {
        return "Mới đăng!";
    }

    if ($time_diff < 60) {
        return "$time_diff giây trước";
    } elseif ($time_diff < 3600) {
        $minutes = floor($time_diff / 60);
        return "$minutes phút trước";
    } elseif ($time_diff < 86400) {
        $hours = floor($time_diff / 3600);
        return "$hours giờ trước";
    } elseif ($time_diff < 604800) {
        $days = floor($time_diff / 86400);
        return "$days ngày trước";
    } else {
        return date("d/m/20y", $created_timestamp);
    }
}

function get_dropdown_menu_item($icon_class, $text, $post_id)
{
    $custom_class_name = '';
    $delete_modal_attributes = '';
    $link_href = '';

    if ($text === 'Theo dõi' || $text === 'Bỏ theo dõi') {
        $custom_class_name = 'post-follow-button';
    }

    if ($text === 'Xóa') {
        $custom_class_name = 'delete-post-button';
        $delete_modal_attributes = "data-bs-toggle='modal' data-bs-target='#modal-confirm-delete-post'";
    }

    if ($text === 'Copy link') {
        $custom_class_name = 'post-copy-link-button';
    }

    if ($text === 'Sửa') {
        $custom_class_name = 'post-edit-button';
    }

    if ($text === 'Đi đến bài viết') {
        $custom_class_name = 'go-to-post-button';
        $link_href = 'href="index.php?post_id=' . $post_id . '"';
    }
    
    

    if ($text === 'Báo cáo bài viết') {
        $custom_class_name = 'report-post-btn';
    }

    return "
        <a $link_href class='$custom_class_name dropdown-item px-2 py-1 d-flex rounded align-items-center mb-1' data-post-id='$post_id' $delete_modal_attributes>
            <li class='d-flex w-100 gap-2 align-items-center rounded'>
                <i class='$icon_class d-flex align-items-center justify-content-center'></i>
                <p class='m-0'>$text</p>
            </li>
        </a>
    ";
}



function get_dropdown_menu_items($is_current_user, $post_id, $is_post_page, $is_user_following_poster = false)
{
    $delete_menu_item = get_dropdown_menu_item('bi bi-trash', 'Xóa', $post_id);
    $edit_menu_item = get_dropdown_menu_item('bi bi-pencil-square', 'Sửa', $post_id);

    if ($is_user_following_poster) {
        $follow_menu_item = get_dropdown_menu_item('bi bi-person-dash', 'Bỏ theo dõi', $post_id);
    } else {
        $follow_menu_item = get_dropdown_menu_item('bi bi-person-plus', 'Theo dõi', $post_id);
    }

    $go_to_post_menu_item = get_dropdown_menu_item('bi bi-box-arrow-up-right', 'Đi đến bài viết', $post_id);
    $copy_link_menu_item = get_dropdown_menu_item('bi bi-link-45deg', 'Copy link', $post_id);

    $dropdown_menu_items = $is_current_user
    ? $delete_menu_item . $edit_menu_item
    : $follow_menu_item . get_dropdown_menu_item('bi bi-flag', 'Báo cáo bài viết', $post_id);


    $dropdown_menu_items .= $is_post_page
        ? $copy_link_menu_item
        : $go_to_post_menu_item . $copy_link_menu_item;

    return $dropdown_menu_items;
}
?>
