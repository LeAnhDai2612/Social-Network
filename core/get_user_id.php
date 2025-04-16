<?php
if (session_status() === PHP_SESSION_NONE) {
    session_name('USER_SESSION');
    session_start();
}


function execute()
{
  $userId = $_SESSION['user_id'];

  if (!$userId) {
    return null;
  }

  return $userId;
}
?>