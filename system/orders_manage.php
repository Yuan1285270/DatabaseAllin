<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'db.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

if ($search !== '') {
  $stmt = $conn->prepare("SELECT o.*, m.name AS member_name FROM orders o
                          JOIN members m ON o.member_id = m.member_id
                          WHERE m.name LIKE CONCAT('%', ?, '%') OR o.order_id LIKE CONCAT('%', ?, '%')
                          ORDER BY o.submission_date DESC");
  $stmt->bind_param("ss", $search, $search);
  $stmt->execute();
  $result = $stmt->get_result();
} else {
  $sql = "SELECT o.*, m.name AS member_name FROM orders o
          JOIN members m ON o.member_id = m.member_id
          ORDER BY o.submission_date DESC";
  $result = $conn->query($sql);
}
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
  <meta charset="UTF-8">
  <title>訂單總覽</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    html, body {
      margin: 0;
      padding: 0;
    }
    body {
      padding-top: 100px; /* 推開 navbar */
      font-family: "Microsoft JhengHei", sans-serif;
    }
  </style>
</head>

<body>
  <?php include 'navbar.php'; ?>

  <div class="container">
    <h2 class="mb-4">訂單總覽</h2>
    <form method="get" class="mb-4">
      <div class="input-group">
        <input type="text" name="search" class="form-control" placeholder="輸入會員名稱或訂單編號"
               value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
        <button type="submit" class="btn btn-primary">搜尋</button>
      </div>
    </form>

    <?php if (isset($_GET['search']) && $search !== ''): ?>
      <p class="text-muted">🔍 目前搜尋：<strong><?= htmlspecialchars($search) ?></strong></p>
    <?php endif; ?>

    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
      <div class="alert alert-success">✅ 已成功刪除訂單！</div>
    <?php endif; ?>

    <table class="table table-bordered table-striped">
      <thead class="table-light">
        <tr>
          <th>訂單編號</th>
          <th>會員</th>
          <th>訂單時間</th>
          <th>總金額</th>
          <th>狀態</th>
          <th>操作</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
          <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
              <td><?= htmlspecialchars($row['order_id']) ?></td>
              <td><?= htmlspecialchars($row['member_name']) ?></td>
              <td><?= htmlspecialchars($row['submission_date']) ?></td>
              <td>$<?= htmlspecialchars($row['total']) ?></td>
              <td><?= htmlspecialchars($row['status']) ?></td>
              <td>
                <a href="order_detail.php?order_id=<?= $row['order_id'] ?>" class="btn btn-sm btn-info">查看明細</a>
                <a href="order_delete.php?order_id=<?= $row['order_id'] ?>" class="btn btn-sm btn-danger ms-1"
                   onclick="return confirm('確定要刪除這筆訂單嗎？');">刪除</a>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr>
            <td colspan="6" class="text-center text-muted">查無資料</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
    <div class="mb-3">
      <a href="order_create.php" class="btn btn-success">新增訂單</a>
    </div>
  </div>
</body>
</html>
