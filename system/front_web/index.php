<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

require 'db.php';
$db = $conn;

// Session 管理
$expire_seconds = 1800;
if (isset($_SESSION['user_id'], $_SESSION['user_start_time']) && time() - $_SESSION['user_start_time'] > $expire_seconds) {
    session_unset();
    session_destroy();
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 'guest_' . substr(md5(uniqid('', true)), 0, 8);
    $_SESSION['user_start_time'] = time();
}
$user_id = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>歐印 ALL-EN</title>
  <link rel="stylesheet" href="style.css">
  <style>
    #chat-button {
      position: fixed;
      bottom: 20px;
      right: 20px;
      background-color: #1976d2;
      color: white;
      border: none;
      border-radius: 50%;
      width: 56px;
      height: 56px;
      font-size: 24px;
      cursor: pointer;
      z-index: 9999;
      box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }

    #chat-frame {
      display: none;
      position: fixed;
      bottom: 80px;
      right: 20px;
      width: 380px;
      height: 500px;
      border: none;
      border-radius: 16px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.3);
      z-index: 9998;
    }

    @media (max-width: 500px) {
      #chat-frame {
        width: 95vw;
        height: 90vh;
        bottom: 5vh;
        right: 2.5vw;
      }
    }
  </style>
</head>
<body>
  <?php include("common/header.php"); ?>

  <main>
    <!-- 🎨 News 圖片區 -->
    <section class="news-banner">
      <img src="images/news.jpg" alt="最新消息" />
    </section>

    <!-- 📢 最新消息 -->
    <section class="news-section">
      <h2>📢 最新消息</h2>
      <ul class="news-list">
        <?php
        require_once 'db.php';
        $stmt = $conn->prepare("SELECT title, content, image_url, publish_date FROM news WHERE status = '已發佈' ORDER BY publish_date DESC LIMIT 3");
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()):
        ?>
          <li style="margin-bottom: 12px; display: flex; align-items: center; gap: 12px;">
            <?php if (!empty($row['image_url'])): ?>
              <img src="<?= htmlspecialchars($row['image_url']) ?>" alt="最新消息圖片" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
            <?php endif; ?>
            <div>
              <strong><?= htmlspecialchars($row['title']) ?></strong> <?= htmlspecialchars($row['content']) ?><br>
              <small style="color: #888;"><?= htmlspecialchars($row['publish_date']) ?></small>
            </div>
          </li>
        <?php endwhile; ?>
      </ul>

      <p class="go-order">
        🚀 <a href="product.php">立即前往「訂購商品」頁面，搶先選購熱銷商品！</a>
      </p>
    </section>

    <!-- 🌐 社群連線 -->
    <section class="social-links">
      <h3>📱 追蹤我們</h3>
      <div class="social-icons">
        <a href="https://www.facebook.com/legendwalkertaiwan/" class="social fb">Facebook</a>
        <a href="https://www.instagram.com/all_en.boutique/" class="social ig">Instagram</a>
        <a href="https://line.me/R/ti/p/_idjyEv115" class="social line">LINE</a>
        <a href="https://www.youtube.com/user/legendwalker6000" class="social yt" target="_blank">YouTube</a>
      </div>
    </section>
  </main>

  <!-- 🤖 AI 小視窗 -->
  <iframe id="chat-frame" src="http://140.134.53.57/~D1285270/chat.php"></iframe>
  <button id="chat-button" onclick="toggleChat()">💬</button>

  <script>
    let chatOpen = false;
    function toggleChat() {
      const iframe = document.getElementById("chat-frame");
      if (chatOpen) {
        iframe.style.display = "none";
      } else {
        iframe.style.display = "block";
      }
      chatOpen = !chatOpen;
    }

    function checkHashForChat() {
      if (window.location.hash === "#chat-frame") {
        const iframe = document.getElementById("chat-frame");
        if (iframe && iframe.style.display !== "block") {
          toggleChat(); // 開啟小視窗
        }
        iframe.scrollIntoView({ behavior: "smooth" });
      }
    }

    document.addEventListener("DOMContentLoaded", checkHashForChat);
    window.addEventListener("hashchange", checkHashForChat);
  </script>

  <footer class="site-footer">
    &copy; LEGEND WALKER 全台獨家代理<br>
    歐印精品 all-en Boutique 電話 04-2291-4216 郵件 info@all-en.com.tw<br>
    Copyright 2011 all-en Taiwan Ltd. All rights reserved
  </footer>
</body>
</html>
