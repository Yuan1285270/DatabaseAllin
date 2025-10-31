<?php
session_start();
include 'db.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);

$email = $_GET['email'] ?? null;

if ($email) {
    $_SESSION['user_email'] = $email;

    // 檢查 email 是否已經存在
    $stmt = $conn->prepare("SELECT member_id FROM members WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        // ✅ 沒有此 email → 產生新的 member_id（像 001、002...）
        $id_query = $conn->query("SELECT MAX(CAST(member_id AS UNSIGNED)) AS max_id FROM members");
        $row = $id_query->fetch_assoc();
        $last_id = intval($row['max_id']);
        $member_id = str_pad($last_id + 1, 3, "0", STR_PAD_LEFT);  // 自動產生像 '006'

        // 設定初始資料
        $name = "Google 使用者";
        $password = null;
        $phone = "";
        $buying_history = "";
        $level = 1;
        $admin_id = '001';

        // 寫入資料表
        $stmt2 = $conn->prepare("
            INSERT INTO members (member_id, name, email, password, phone, buying_history, level, registration_date, admin_id)
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), ?)
        ");
        $stmt2->bind_param("ssssssis", $member_id, $name, $email, $password, $phone, $buying_history, $level, $admin_id);
        $stmt2->execute();

        echo "✅ 新用戶已加入（ID: <strong>$member_id</strong>，Email: <strong>" . htmlspecialchars($email) . "</strong>)";
    } else {
        // 已有帳號
        $stmt->bind_result($existing_id);
        $stmt->fetch();
        echo "👋 歡迎回來（ID: <strong>$existing_id</strong>，Email: <strong>" . htmlspecialchars($email) . "</strong>)";
    }
} else {
    echo "⚠️ 沒有收到 email 資料，請重新登入。";
}
?>
