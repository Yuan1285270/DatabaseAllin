<?php
require_once 'db.php';
$db = $conn;

if (!isset($_GET['id'])) {
  die("❌ 找不到商品 ID");
}

$product_id = $_GET['id'];
$stmt = $db->prepare("SELECT name, image, description, price, storage FROM products WHERE product_id = ?");
$stmt->bind_param("s", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
  die("❌ 商品不存在");
}
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
  <meta charset="UTF-8" />
  <title><?= htmlspecialchars($product['name']) ?> - 商品詳情</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <?php include("common/header.php"); ?>

  <main style="padding: 40px; max-width: 800px; margin: auto;">
    <h2><?= htmlspecialchars($product['name']) ?></h2>
    <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" style="max-width: 100%; border-radius: 12px;">
    <div style="margin-top: 30px; padding: 30px; background-color: #fefefe; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); position: relative;">

      <!-- 商品介紹文字 -->
      <p style="font-size: 17px; color: #333; line-height: 1.9; margin-bottom: 25px;">
        <?= nl2br(htmlspecialchars($product['description'])) ?>
      </p>

      <!-- 價格與庫存 -->
      <p style="font-size: 18px; font-weight: bold; color: #d32f2f; margin-bottom: 8px;">
        💰 價格：<span style="font-size: 20px;"><?= $product['price'] ?> 元</span>
      </p>
      <p style="font-size: 16px; color: #1976d2; margin-bottom: 60px;">
        📦 庫存量：<strong><?= $product['storage'] ?> 件</strong>
      </p>

      <!-- 加入購物車按鈕 -->
      <?php if (isset($_SESSION['email'])): ?>
        <form action="add_to_cart.php" method="POST" style="position: absolute; bottom: 20px; right: 20px;">
          <input type="hidden" name="product_id" value="<?= htmlspecialchars($product_id) ?>">
          <button type="submit" style="padding: 10px 20px; background-color: #1976d2; color: white; border: none; border-radius: 6px; font-size: 16px; cursor: pointer;">
            🛒 加入購物車
          </button>
        </form>
      <?php else: ?>
        <p style="position: absolute; bottom: 20px; right: 20px; color: red; font-weight: bold;">
          請先 <a href="user.php" style="color: #1976d2; text-decoration: underline;">登入</a> 才能加入購物車
        </p>
      <?php endif; ?>
    </div>
  </main>
  <?php include("common/ai_widget.php"); ?>
  <?php include("common/footer.php"); ?>
</body>
</html>
