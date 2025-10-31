<?php
require_once DIR . '/vendor/autoload.php';
require_once 'db.php'; // ⬅️ 連線到資料庫
session_start();

$client = new Google_Client();
$client->setClientId('...');
$client->setClientSecret('...');
$client->setRedirectUri('https://google-login-dw05.onrender.com/google_callback.php');

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    if (!isset($token['error'])) {
        $client->setAccessToken($token);

        $oauth2 = new Google_Service_Oauth2($client);
        $userinfo = $oauth2->userinfo->get();
        $email = $userinfo->email;

        // 查詢會員是否存在
        $stmt = $pdo->prepare("SELECT name FROM members WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            // ✅ 已註冊，登入成功
            $_SESSION['email'] = $email;
            $_SESSION['name'] = $user['name'];
            header("Location: index.php");
        } else {
            // ⛔ 未註冊，導向補填頁
            $_SESSION['google_email'] = $email; // 暫存 email
            header("Location: google_register.php");
        }
        exit;
    } else {
        echo "錯誤：" . htmlspecialchars($token['error_description']);
    }
} else {
    echo "沒有收到認證碼。";
}
?>
