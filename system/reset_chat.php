<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require 'db.php';
$db = $conn;

if (!isset($_SESSION['user_id'])) {
    die("⚠️ 找不到使用者 ID，無法清除紀錄。");
}

$user_id = $_SESSION['user_id'];

// 刪除該使用者的所有對話紀錄
$stmt = $db->prepare("DELETE FROM ai_chat WHERE user_id = ?");
$stmt->bind_param("s", $user_id);
$stmt->execute();

// 清除 session 並重新產生一個新身份
session_unset();
session_destroy();
session_start();
$new_id = 'guest_' . substr(md5(uniqid('', true)), 0, 8);
$_SESSION['user_id'] = $new_id;
$_SESSION['user_start_time'] = time();

// 回到聊天畫面
header("Location: chat.php");
exit;
