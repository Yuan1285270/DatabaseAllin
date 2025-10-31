<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require 'db.php';
require_once 'Parsedown.php';
$Parsedown = new Parsedown();

$db = $conn;

$expire_seconds = 1800;

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

$stmt = $db->prepare("SELECT question, ai_response FROM ai_chat WHERE user_id = ? ORDER BY created_at ASC");
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>AI 聊天室</title>
  <style>
    body {
      background: #e3f2fd;
      font-family: 'Segoe UI', 'Noto Sans TC', Arial, sans-serif;
      margin: 0;
      padding: 0;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
      justify-content: center;
    }
    .chatbox-container {
      background: #f8fbff;
      max-width: 540px;
      margin: 48px auto 0 auto;
      border-radius: 18px;
      box-shadow: 0 2px 24px 2px #b3d8fd66;
      padding: 0 0 12px 0;
      display: flex;
      flex-direction: column;
      min-height: 530px;
      border: 1.5px solid #bbdefb;
    }
    h2 {
      text-align: center;
      color: #1976d2;
      letter-spacing: 2px;
      margin: 20px 0 10px 0;
      font-size: 1.45em;
    }
    .chat-window {
      flex: 1;
      height: 360px;
      overflow-y: auto;
      background: #e3f2fd;
      border-radius: 15px;
      margin: 0 22px 14px 22px;
      padding: 18px 10px 10px 10px;
      box-shadow: 0 1.5px 8px 0 #b3d8fd33;
      border: 1px solid #90caf9;
    }
    .chat-bubble {
      display: inline-block;
      margin: 10px 0;
      padding: 11px 15px;
      border-radius: 22px;
      max-width: 76%;
      word-break: break-all;
      font-size: 1.09em;
      line-height: 1.68;
      box-shadow: 0 2px 10px #90caf933;
      box-sizing: border-box;
      position: relative;
      background: #fff;
      transition: background 0.12s;
    }
    .user {
      background: #90caf9;
      color: #114488;
      margin-left: 22%;
      margin-right: 0;
      text-align: right;
      float: right;
      clear: both;
      border-bottom-right-radius: 6px;
      border-bottom-left-radius: 22px;
      border-top-right-radius: 18px;
      border-top-left-radius: 22px;
      box-shadow: 0 2px 10px #90caf950;
    }
    .ai {
      background: #ffffff;
      color: #283346;
      margin-left: 0;
      margin-right: 22%;
      text-align: left;
      float: left;
      clear: both;
      border-bottom-left-radius: 6px;
      border-bottom-right-radius: 22px;
      border-top-right-radius: 22px;
      border-top-left-radius: 18px;
    }
    .ai strong, .ai b { font-weight: bold; color: #1976d2;}
    .ai em, .ai i { font-style: italic; color: #7e57c2;}
    .ai a { color: #039be5; text-decoration: underline; }
    .ai code { background: #e3f2fd; padding: 2px 6px; border-radius: 4px; font-size: 95%; font-family: 'JetBrains Mono', 'Consolas', monospace;}
    .chat-form {
      display: flex;
      flex-direction: column;
      align-items: stretch;
      margin: 0 18px;
      gap: 5px;
    }
    textarea {
      font-size: 1.09em;
      padding: 8px 10px;
      border-radius: 8px;
      border: 1.3px solid #90caf9;
      resize: none;
      box-sizing: border-box;
      min-height: 50px;
      margin-bottom: 6px;
      background: #f0f8ff;
      transition: border 0.15s;
    }
    textarea:focus { border: 1.3px solid #1976d2; outline: none; }
    .btns-row {
      display: flex;
      flex-direction: row;
      justify-content: space-between;
      gap: 9px;
    }
    input[type="submit"] {
      background: #1976d2;
      color: white;
      border: none;
      padding: 7px 28px;
      border-radius: 16px;
      font-size: 1.03em;
      cursor: pointer;
      transition: background 0.19s;
      font-weight: 600;
      box-shadow: 0 1px 6px #90caf977;
      margin-top: 0;
    }
    input[type="submit"]:hover {
      background: #1152a6;
    }
    .reset-btn {
      background: #e0eafc;
      color: #1976d2;
      border: none;
      padding: 7px 14px;
      border-radius: 16px;
      font-size: 0.99em;
      cursor: pointer;
      transition: background 0.19s;
      font-weight: 500;
      box-shadow: 0 1px 5px #b3d8fd44;
      margin-left: 0;
      text-decoration: none;
      display: inline-block;
      text-align: center;
      line-height: normal;
    }
    .reset-btn:hover {
      background: #bbdefb;
    }
    @media (max-width: 700px) {
      .chatbox-container {
        max-width: 96vw; margin: 0; min-height: 92vh;
      }
      .chat-window {
        height: 220px;
        margin: 0 3vw 10px 3vw;
        padding: 10px 2vw;
      }
    }
  </style>
</head>
<body>
  <div class="chatbox-container">
    <h2>AI 小聊窗</h2>
    <div class="chat-window" id="chatWindow">
      <?php while($row = $result->fetch_assoc()): ?>
        <div class="chat-bubble user"><?= htmlspecialchars($row['question']) ?></div>
        <div class="chat-bubble ai"><?= $Parsedown->text($row['ai_response']) ?></div>
      <?php endwhile; ?>
    </div>
    <form method="POST" action="process_chat.php" class="chat-form">
      <textarea name="question" rows="3" placeholder="輸入你的問題..." required></textarea>
      <div class="btns-row">
        <input type="submit" value="送出">
        <a href="reset_chat.php" class="reset-btn"
           onclick="return confirm('確定要清空這個使用者的所有對話嗎？');">🗑 清除對話</a>
      </div>
    </form>
  </div>
  <script>
    window.onload = function() {
      var chatWindow = document.getElementById('chatWindow');
      if (chatWindow) chatWindow.scrollTop = chatWindow.scrollHeight;
    };
  </script>
</body>
</html>
