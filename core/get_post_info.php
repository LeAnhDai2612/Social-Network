<?php
require_once("db_functions.php");
date_default_timezone_set('Asia/Ho_Chi_Minh');
function execute($params)
{
    $pdo = connect_to_db();

    $params = json_decode($params, true);

    $post_id = $params['post_id'];

    return get_post($pdo, $post_id);
}
?>