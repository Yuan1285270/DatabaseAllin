<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require 'db.php';
$db = $conn;

$expire_seconds = 1800;

// Session 管理
if (isset($_SESSION['user_id'], $_SESSION['user_start_time'])) {
    if (time() - $_SESSION['user_start_time'] > $expire_seconds) {
        session_unset();
        session_destroy();
        session_start();
    }
}

if (!isset($_SESSION['user_id'])) {
    $user_id = 'guest_' . substr(md5(uniqid('', true)), 0, 8);
    $_SESSION['user_id'] = $user_id;
    $_SESSION['user_start_time'] = time();
} else {
    $user_id = $_SESSION['user_id'];
}

// 取得問題
$question = $_POST['question'] ?? '';
if (empty(trim($question))) {
    die("請輸入問題！");
}

// 預設的系統說明
$default_system_prompt = <<<EOT
你是「歐印精品」的客服助理，我們只賣行李箱與旅遊用品。

請嚴格遵守以下回應原則：
1. 不要回答商品是否有賣。
2. 若有提供參考網址，請引導客戶「可以參考這個頁面看看」，而不是說「我們有賣」或「我們沒有」。
3. 禁止捏造產品資訊、商品名稱、品牌、或任何未提供的網址。
4. 回覆語氣請簡潔、溫和、友善。
5. 若有網址，請用以下格式附上：👉 相關連結：http://140.134.53.57/~D1285270/front_web/product.php（請勿使用 Markdown 或括號包住網址）
6. 回覆語言使用繁體中文。

請務必嚴格遵守以上原則，避免誤導客戶。
EOT;

// 從資料庫抓出所有可用資源讓 Gemini 自己判斷要不要使用
$resource_list = [];
$stmt = $db->prepare("SELECT destination, url, response_text FROM ai_prompt WHERE enabled = 1");
$stmt->execute();
$res = $stmt->get_result();

while ($row = $res->fetch_assoc()) {
    $entry = '';
    if (!empty($row['response_text'])) {
        $entry .= $row['response_text'] . "\n";
    }
    if (!empty($row['url'])) {
        $entry .= "👉 相關連結：" . $row['url'];
    }
    if (!empty($entry)) {
        $resource_list[] = "【" . $row['destination'] . "】\n" . $entry;
    }
}
$stmt->close();

$custom_prompt_text = '';
if (!empty($resource_list)) {
    $custom_prompt_text = "\n\n【可參考的資訊與連結】請根據客戶問題，自行判斷是否引用下列內容：\n" . implode("\n\n", $resource_list);
}

$system_prompt = $default_system_prompt . $custom_prompt_text;

$messages = [];
$messages[] = ["role" => "user", "parts" => [["text" => "以下是你的角色與回應原則：\n\n" . $system_prompt]]];

// 範例對話
$messages[] = ["role" => "user", "parts" => [["text" => "請問有推薦的登機箱嗎？"]]];
$messages[] = ["role" => "model", "parts" => [["text" => "您可以參考這個登機箱專區看看，👉 相關連結：http://140.134.53.57/~D1285270/front_web/product.php"]]];
$messages[] = ["role" => "user", "parts" => [["text" => "你們賣筆電嗎？"]]];
$messages[] = ["role" => "model", "parts" => [["text" => "很抱歉，我們只販售行李箱與旅遊用品，無法回答此問題。"]]];

// 過去對話補入 messages
$stmt = $db->prepare("SELECT question, ai_response FROM ai_chat WHERE user_id = ? ORDER BY created_at ASC");
$stmt->bind_param("s", $user_id);
$stmt->execute();
$res = $stmt->get_result();

while ($row = $res->fetch_assoc()) {
    $messages[] = ["role" => "user",  "parts" => [["text" => $row['question']]]];
    $messages[] = ["role" => "model", "parts" => [["text" => $row['ai_response']]]];
}
$stmt->close();

// 加入本次提問
$messages[] = ["role" => "user", "parts" => [["text" => $question]]];

// 發送 Gemini API 請求
$api_key = "AIzaSyBcsYfczYaTjIxBiqXZBc_oXyBzTtUDyVE";
$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=$api_key";
$payload = json_encode(["contents" => $messages]);

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
    CURLOPT_POSTFIELDS => $payload
]);

$response = curl_exec($ch);
if (!$response) {
    die("❌ Gemini API 請求失敗：" . curl_error($ch));
}
curl_close($ch);

$data = json_decode($response, true);
if (!isset($data['candidates'][0]['content']['parts'][0]['text'])) {
    die("❌ Gemini 沒有回應或格式錯誤。<pre>" . print_r($data, true) . "</pre>");
}

$reply = $data['candidates'][0]['content']['parts'][0]['text'];

// 儲存對話
$stmt = $db->prepare("INSERT INTO ai_chat (user_id, question, ai_response) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $user_id, $question, $reply);
$stmt->execute();
$stmt->close();

header("Location: chat.php");
exit;
