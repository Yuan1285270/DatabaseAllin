<?php
require_once 'db.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 取得查詢條件
$reporter_name = $_POST['reporter_name'] ?? '';
$reporter_phone = $_POST['reporter_phone'] ?? '';

// 查詢 repair_request + repair 關聯資料
$sql = "SELECT r.repair_id, r.request_date, r.status, r.problem_description, r.product_id, r.member_id, 
               rr.receive_address, rr.pickup_date, rr.product_code, rr.product_size, rr.product_color
        FROM repair_request rr
        JOIN repair r ON rr.repair_id = r.repair_id
        WHERE rr.reporter_name = ? AND rr.reporter_phone = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $reporter_name, $reporter_phone);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
  <meta charset="UTF-8">
  <title>維修查詢結果 | 歐印 all-en</title>
  <link rel="stylesheet" href="style.css" />
  <style>
    .result-box {
      max-width: 900px;
      margin: 40px auto;
      padding: 20px;
      background: #fff;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    h2 {
      color: #2980b9;
      text-align: center;
      margin-bottom: 20px;
    }
    .repair-entry {
      border: 1px solid #ccc;
      border-radius: 8px;
      padding: 15px;
      margin-bottom: 20px;
    }
    .repair-entry p {
      margin: 6px 0;
    }
    .empty-msg {
      text-align: center;
      color: #999;
      margin-top: 40px;
    }
  </style>
</head>
<body>
  <?php include("common/header.php"); ?>

  <main class="result-box">
    <h2>🧾 維修通報查詢結果</h2>

    <?php if ($result->num_rows > 0): ?>
      <?php while ($row = $result->fetch_assoc()): ?>
        <div class="repair-entry">
          <p><strong>維修單號：</strong><?= htmlspecialchars($row['repair_id']) ?></p>
          <p><strong>送修日期：</strong><?= htmlspecialchars($row['request_date']) ?></p>
          <p><strong>維修狀態：</strong><?= getStatusText($row['status']) ?></p>
          <p><strong>產品代碼 / 規格 / 顏色：</strong> <?= $row['product_code'] ?> / <?= $row['product_size'] ?> / <?= $row['product_color'] ?></p>
          <p><strong>狀況描述：</strong><?= nl2br(htmlspecialchars($row['problem_description'])) ?></p>
          <p><strong>預計收件地址：</strong><?= htmlspecialchars($row['receive_address']) ?></p>
          <p><strong>可派收日期：</strong><?= htmlspecialchars($row['pickup_date']) ?></p>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p class="empty-msg">❌ 查無維修資料，請確認姓名與電話是否正確。</p>
    <?php endif; ?>
  </main>

  <?php include("common/ai_widget.php"); ?>
  <?php include("common/footer.php"); ?>
</body>
</html>

<?php
// 狀態對應文字
function getStatusText($code) {
  switch ($code) {
    case 0: return "待處理";
    case 1: return "處理中";
    case 2: return "已完成";
    case 3: return "已取消";
    default: return "未知狀態";
  }
}
?>
