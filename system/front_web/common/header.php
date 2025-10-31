<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
?>
<header>
  <div class="logo">歐印 ALL-EN</div>
  <div class="top-buttons">
    <?php if (isset($_SESSION['name'])): ?>
      <span class="welcome-msg">歡迎，<?= htmlspecialchars($_SESSION['name']) ?> 會員</span>
      <a href="my_orders.php" class="btn">訂單資訊</a>
      <a href="cart.php" class="btn">購物車</a>
      <a href="logout.php" class="btn">登出</a>
    <?php else: ?>
      <a href="user.php" class="btn">會員登入</a>
      <a href="cart.php" class="btn">購物車</a>
    <?php endif; ?>
  </div>
</header>

<nav>
  <a href="index.php">首頁</a>
  <a href="product.php">訂購商品</a>
  <a href="brand.php">關於歐印</a>
  <a href="warranty.php">維護保固</a>
  <a href="javascript:void(0);" class="btn" onclick="toggleChat()">AI智慧搜尋</a>
</nav>

<!--開啟XMApp->Apache, MYSQL-->
<!--https://localhost/allin_web/index.php-->
<!--https://localhost/phpadamin-->