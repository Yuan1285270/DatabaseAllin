<?php
session_start();
require_once 'db.php';
$db = $conn;

// 若尚未登入，導向登入頁
if (!isset($_SESSION['email'])) {
  header("Location: user.php");
  exit;
}

// 取得會員 ID
$email = $_SESSION['email'];
$stmt = $db->prepare("SELECT member_id FROM members WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
  die("❌ 找不到會員資料");
}
$member_id = $user['member_id'];

// 撈出該會員的購物車內容（JOIN 產品名稱與圖片）
$sql = "SELECT p.name, p.image, p.price, c.quantity 
        FROM cart c 
        JOIN products p ON c.product_id = p.product_id 
        WHERE c.member_id = ?";
$stmt = $db->prepare($sql);
$stmt->bind_param("s", $member_id);
$stmt->execute();
$cart_items = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>購物車 | 歐印商店</title>
  <link rel="stylesheet" href="style.css" />
  <link rel="stylesheet" href="cart.css" />
</head>
<body>
  <?php include("common/header.php"); ?>

  <main class="cart-box">
    <h2 class="cart-title">🛒 購物車</h2>

    <div class="cart-content">
      <?php if ($cart_items->num_rows > 0): ?>
        <?php $total_price = 0; ?>
        <ul id="cart-list">
          <?php while ($item = $cart_items->fetch_assoc()): ?>
            <?php $subtotal = $item['price'] * $item['quantity']; ?>
            <?php $total_price += $subtotal; ?>
            <li class="cart-item">
              <img src="http://140.134.53.57<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" style="width: 120px; height: auto; border-radius: 8px;" />
              <div class="item-info">
                <h3><?= htmlspecialchars($item['name']) ?></h3>
                <p>數量：<?= $item['quantity'] ?></p>
                <p>單價：<?= $item['price'] ?> 元</p>
                <p>小計：<?= $subtotal ?> 元</p>
              </div>
            </li>
          <?php endwhile; ?>
        </ul>

        <!-- 🔢 總計金額顯示 -->
        <div class="cart-total" style="text-align:right; font-size:18px; margin-top: 20px;">
          <strong>總計金額：</strong> <?= $total_price ?> 元
        </div>
      <?php else: ?>
        <p id="empty-message" class="empty">
          您尚未選購商品，請前往 <a href="product.php">商品頁面</a> 購買您喜歡的商品。
        </p>
      <?php endif; ?>
    </div>

    <?php if ($cart_items->num_rows > 0): ?>
      <div class="cart-actions">
        <form action="place_order.php" method="post" style="display: flex; flex-direction: column; gap: 12px; max-width: 500px; margin: auto;">
          <label for="shipment_address"><strong>📮 收件地址：</strong></label>
          <input type="text" name="shipment_address" id="shipment_address" required placeholder="請輸入完整地址" style="padding: 8px; border-radius: 6px; border: 1px solid #ccc;">

          <label for="discount_code"><strong>🎟️ 折扣碼（選填）：</strong></label>
          <input type="text" name="discount_code" id="discount_code" placeholder="請輸入折扣碼（若有）" style="padding: 8px; border-radius: 6px; border: 1px solid #ccc;">

          <label for="problem_description"><strong>📝 備註（選填）：</strong></label>
          <textarea name="problem_description" id="problem_description" rows="3" placeholder="如需特殊說明，可填寫此欄…" style="padding: 8px; border-radius: 6px; border: 1px solid #ccc;"></textarea>

          <button type="submit" style="padding: 10px; background-color: #1976d2; color: white; border: none; border-radius: 6px; font-size: 16px;">
            ✅ 立即下單
          </button>
        </form>
      </div>
    <?php endif; ?>
    
    <section class="faq-section">
      <h3>❓ 常見問題</h3>
      <ul>
        <li>如何結帳？</li>
        <li>是否可以退貨？</li>
        <li>商品多久出貨？</li>
        <li>可選擇超商取貨嗎？</li>
      </ul>
    </section>
  </main>
  <?php include("common/ai_widget.php"); ?>
  <?php include("common/footer.php"); ?>
</body>
</html>
