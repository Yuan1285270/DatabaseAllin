<?php
session_start();
require_once 'db.php';
$db = $conn;

// 撈出所有商品
$stmt = $db->query("SELECT name, image, description, class FROM products");
$products = $stmt->fetch_all(MYSQLI_ASSOC);

// 分類陣列
$luggage = [];
$accessories = [];
$travel = [];

foreach ($products as $p) {
  switch ($p['class']) {
    case '行李箱':
      $luggage[] = $p;
      break;
    case '配件':
      $accessories[] = $p;
      break;
    case '旅遊周邊':
      $travel[] = $p;
      break;
  }
}
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
  <meta charset="UTF-8">
  <title>商品介紹 | 歐印 ALL-EN</title>
  <link rel="stylesheet" href="style.css" />
  <script src="product_intro.js" defer></script>
  <style>
    .product-container {
      display: flex;
      padding: 20px;
    }
    .category-menu {
      width: 150px;
      display: flex;
      flex-direction: column;
      gap: 10px;
    }
    .category-menu button {
      padding: 10px;
      font-size: 16px;
      border: none;
      background-color: #eee;
      cursor: pointer;
      border-radius: 6px;
      transition: 0.3s;
    }
    .category-menu button.active {
      background-color: #1976d2;
      color: white;
    }

    .product-content {
      flex: 1;
      padding-left: 30px;
    }

    .category-block {
      display: none;
      flex-wrap: wrap;
      gap: 20px;
    }

    .category-block.active {
      display: flex;
      flex-wrap: wrap;
    }

    .product-card {
      background: white;
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      padding: 16px;
      width: calc(33.33% - 20px);
      box-sizing: border-box;
      text-align: center;
      transition: transform 0.2s ease;
    }

    .product-card:hover {
      transform: translateY(-4px);
    }

    .product-card img {
      width: 100%;
      max-height: 200px;
      object-fit: contain;
      margin-bottom: 10px;
      border-radius: 8px;
    }

    .product-card h3 {
      font-size: 16px;
      margin: 10px 0 6px;
    }

    .product-card p {
      font-size: 14px;
      color: #555;
    }

    .wrapper {
      max-width: 1100px;
      margin: 0 auto;
      padding: 0 20px;
    }

    @media (max-width: 768px) {
      .product-card {
        width: calc(50% - 20px);
      }
    }

    @media (max-width: 500px) {
      .product-container {
        flex-direction: column;
      }
      .product-card {
        width: 100%;
      }
    }
  </style>
</head>
<body>
  <?php include("common/header.php"); ?>
  <div class="wrapper">
    <main class="product-container">
      <aside class="category-menu">
        <button onclick="showCategory('luggage')" class="active">行李箱</button>
        <button onclick="showCategory('accessories')">配件</button>
        <button onclick="showCategory('travel')">旅遊周邊</button>
      </aside>

      <section class="product-content">
        <!-- 行李箱 -->
        <div id="luggage" class="category-block active">
          <h2 style="width: 100%;">行李箱</h2>
          <?php foreach ($luggage as $p): ?>
            <div class="product-card">
              <img src="uploads/<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>">
              <h3><?= htmlspecialchars($p['name']) ?></h3>
              <p><?= htmlspecialchars($p['description']) ?></p>
            </div>
          <?php endforeach; ?>
        </div>

        <!-- 配件 -->
        <div id="accessories" class="category-block">
          <h2 style="width: 100%;">配件</h2>
          <?php foreach ($accessories as $p): ?>
            <div class="product-card">
              <img src="uploads/<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>">
              <h3><?= htmlspecialchars($p['name']) ?></h3>
              <p><?= htmlspecialchars($p['description']) ?></p>
            </div>
          <?php endforeach; ?>
        </div>

        <!-- 旅遊周邊 -->
        <div id="travel" class="category-block">
          <h2 style="width: 100%;">旅遊周邊</h2>
          <?php foreach ($travel as $p): ?>
            <div class="product-card">
              <img src="uploads/<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>">
              <h3><?= htmlspecialchars($p['name']) ?></h3>
              <p><?= htmlspecialchars($p['description']) ?></p>
            </div>
          <?php endforeach; ?>
        </div>
      </section>
    </main>
    </div>

  <?php include("common/ai_widget.php"); ?>
  <?php include("common/footer.php"); ?>
</body>
</html>
