<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'db.php';
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
  <meta charset="UTF-8">
  <title>會員管理</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    html, body {
      margin: 0;
      padding: 0;
    }
    body {
      padding-top: 100px;
      font-family: "Microsoft JhengHei", sans-serif;
    }
  </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container">

  <h2 class="mb-4">會員管理</h2>

  <?php
  // ========== 處理區塊 ==========
  if (isset($_POST['save'])) {
      $errors = [];
      $admin_id = '001';

      if (empty($_POST['name'])) $errors[] = "姓名不能空白";
      if (empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) $errors[] = "請填入有效的 Email";
      if (empty($_POST['edit_id'])) {
          if (empty($_POST['password']) || strlen($_POST['password']) < 6) $errors[] = "密碼至少 6 字元";
      } else {
          if (!empty($_POST['password']) && strlen($_POST['password']) < 6) $errors[] = "密碼至少 6 字元";
      }
      if (!preg_match('/^\d{10}$/', $_POST['phone'])) $errors[] = "電話必須為 10 碼數字";
      if (!is_numeric($_POST['level']) || $_POST['level'] < 1 || $_POST['level'] > 5) $errors[] = "等級必須是 1~5 的數字";
      if (empty($_POST['registration_date'])) $errors[] = "請填入註冊時間";

      if (!empty($errors)) {
          echo "<div class='alert alert-danger'><ul>";
          foreach ($errors as $e) echo "<li>$e</li>";
          echo "</ul></div>";
      } else {
          if (!empty($_POST['edit_id'])) {
              if (!empty($_POST['password'])) {
                  $stmt = $conn->prepare("UPDATE members SET name=?, email=?, password=?, phone=?, buying_history=?, level=?, registration_date=?, admin_id=? WHERE member_id=?");
                  $stmt->bind_param("sssssssss", $_POST['name'], $_POST['email'], $_POST['password'], $_POST['phone'], $_POST['buying_history'], $_POST['level'], $_POST['registration_date'], $admin_id, $_POST['edit_id']);
              } else {
                  $stmt = $conn->prepare("UPDATE members SET name=?, email=?, phone=?, buying_history=?, level=?, registration_date=?, admin_id=? WHERE member_id=?");
                  $stmt->bind_param("ssssssss", $_POST['name'], $_POST['email'], $_POST['phone'], $_POST['buying_history'], $_POST['level'], $_POST['registration_date'], $admin_id, $_POST['edit_id']);
              }
          } else {
              $stmt = $conn->prepare("INSERT INTO members (member_id, name, email, password, phone, buying_history, level, registration_date, admin_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
              $stmt->bind_param("ssssssiss", $_POST['member_id'], $_POST['name'], $_POST['email'], $_POST['password'], $_POST['phone'], $_POST['buying_history'], $_POST['level'], $_POST['registration_date'], $admin_id);
          }
          $stmt->execute();
          $stmt->close();
          header("Location: " . $_SERVER['PHP_SELF']);
          exit;
      }
  }

  if (isset($_POST['delete_id'])) {
      $stmt = $conn->prepare("DELETE FROM members WHERE member_id = ?");
      $stmt->bind_param("s", $_POST['delete_id']);
      $stmt->execute();
      $stmt->close();
      header("Location: " . $_SERVER['PHP_SELF']);
      exit;
  }

  $search = $_GET['search'] ?? '';
  $sql = "SELECT * FROM members";
  if (!empty($search)) {
      $sql .= " WHERE name LIKE '%$search%' OR email LIKE '%$search%'";
  }
  $sql .= " ORDER BY registration_date DESC";
  $result = $conn->query($sql);

  $edit = false;
  if (isset($_GET['edit'])) {
      $edit_id = $_GET['edit'];
      $edit = true;
      $edit_result = $conn->query("SELECT * FROM members WHERE member_id = '$edit_id'");
      $edit_data = $edit_result->fetch_assoc();
  }
  ?>

  <!-- 搜尋 -->
  <form method="GET" class="mb-3">
    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="搜尋信箱" class="form-control w-50 d-inline">
    <button type="submit" class="btn btn-secondary">搜尋</button>
    <a href="member_manage.php" class="btn btn-outline-secondary">清除</a>
  </form>

  <!-- 表單 -->
  <form method="POST" class="row g-3 mb-5">
    <input type="hidden" name="edit_id" value="<?= $edit ? $edit_data['member_id'] : '' ?>">
    <?php if (!$edit): ?>
    <div class="col-md-4">
      <label class="form-label">會員 ID</label>
      <input type="text" name="member_id" class="form-control" required>
    </div>
    <?php endif; ?>
    <div class="col-md-4">
      <label class="form-label">姓名</label>
      <input type="text" name="name" class="form-control" required value="<?= $edit ? $edit_data['name'] : '' ?>">
    </div>
    <div class="col-md-4">
      <label class="form-label">Email</label>
      <input type="email" name="email" class="form-control" required value="<?= $edit ? $edit_data['email'] : '' ?>">
    </div>
    <div class="col-md-4">
      <label class="form-label">密碼</label>
      <input type="text" name="password" class="form-control" <?= $edit ? 'placeholder="留空則不變更密碼"' : 'required minlength="6"' ?>>
    </div>
    <div class="col-md-4">
      <label class="form-label">電話</label>
      <input type="text" name="phone" class="form-control" required pattern="\d{10}" value="<?= $edit ? $edit_data['phone'] : '' ?>">
    </div>
    <div class="col-md-4">
      <label class="form-label">購買紀錄</label>
      <input type="text" name="buying_history" class="form-control" value="<?= $edit ? $edit_data['buying_history'] : '' ?>">
    </div>
    <div class="col-md-2">
      <label class="form-label">等級</label>
      <input type="number" name="level" class="form-control" min="1" max="5" required value="<?= $edit ? $edit_data['level'] : '' ?>">
    </div>
    <div class="col-md-3">
      <label class="form-label">註冊日期</label>
      <input type="datetime-local" name="registration_date" class="form-control" required value="<?= $edit ? str_replace(' ', 'T', $edit_data['registration_date']) : '' ?>">
    </div>
    <div class="col-12">
      <button type="submit" name="save" class="btn btn-primary"><?= $edit ? '更新會員' : '新增會員' ?></button>
      <?php if ($edit): ?><a href="member_manage.php" class="btn btn-secondary">取消</a><?php endif; ?>
    </div>
  </form>

  <!-- 表格 -->
  <table class="table table-bordered table-striped">
    <thead class="table-light">
      <tr>
        <th>會員ID</th><th>姓名</th><th>Email</th><th>電話</th><th>等級</th><th>註冊日</th><th>操作</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($row['member_id']) ?></td>
          <td><?= htmlspecialchars($row['name']) ?></td>
          <td><?= htmlspecialchars($row['email']) ?></td>
          <td><?= htmlspecialchars($row['phone']) ?></td>
          <td><?= htmlspecialchars($row['level']) ?></td>
          <td><?= htmlspecialchars($row['registration_date']) ?></td>
          <td>
            <a href="?edit=<?= $row['member_id'] ?>" class="btn btn-sm btn-warning">編輯</a>
            <form method="POST" style="display:inline;" onsubmit="return confirm('確定刪除？');">
              <input type="hidden" name="delete_id" value="<?= $row['member_id'] ?>">
              <button type="submit" class="btn btn-sm btn-danger">刪除</button>
            </form>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>

</body>
</html>
