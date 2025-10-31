<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'db.php';

$admin_id = '001';

// 圖片上傳（不寫入資料庫）
// 圖片上傳（不寫入資料庫）
$image_upload_message = '';
if (isset($_POST['upload_image'])) {
  if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = '/home/D1285270/public_html/images/upload/'; // ✅ 修正此處路徑
    if (!is_dir($upload_dir)) {
      mkdir($upload_dir, 0755, true);
    }

    $filename = uniqid() . "_" . basename($_FILES['image_file']['name']);
    $target_path = $upload_dir . $filename;

    if (move_uploaded_file($_FILES['image_file']['tmp_name'], $target_path)) {
      $image_url = "/~D1285270/images/upload/$filename"; // 顯示正確網址
      $image_upload_message = "
        <div class='alert alert-success'>
          圖片上傳成功！<br>
          圖片網址：<a href='$image_url' target='_blank'>$image_url</a>
          <script>
            window.addEventListener('DOMContentLoaded', function() {
              const input = document.querySelector('input[name=\"image_url\"]');
              if (input) {
                input.value = '$image_url';
              }
              const preview = document.getElementById('image-preview');
              if (preview) {
                preview.innerHTML = '<img src=\"$image_url\" style=\"max-height:100px;\">';
              }
            });
          </script>
        </div>";
    } else {
      $image_upload_message = "<div class='alert alert-danger'>圖片上傳失敗！</div>";
    }
  } else {
    $image_upload_message = "<div class='alert alert-warning'>請選擇圖片再上傳！</div>";
  }
}

// 儲存或更新消息
if (isset($_POST['save'])) {
  $news_id = $_POST['news_id'] ?? null;
  $title = $_POST['title'];
  $content = $_POST['content'];
  $image_url = $_POST['image_url'] ?? null;
  $link_url = $_POST['link_url'] ?? null;
  $status = $_POST['status'];

  if ($news_id) {
    $stmt = $conn->prepare("UPDATE news SET title=?, content=?, image_url=?, link_url=?, status=? WHERE news_id=?");
    $stmt->bind_param("sssssi", $title, $content, $image_url, $link_url, $status, $news_id);
  } else {
    $stmt = $conn->prepare("INSERT INTO news (title, content, image_url, link_url, status, admin_id) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $title, $content, $image_url, $link_url, $status, $admin_id);
  }

  if (!$stmt->execute()) {
    die("操作失敗：" . $stmt->error);
  }
  header("Location: news_manage.php");
  exit;
}

// 刪除消息
if (isset($_POST['delete_id'])) {
  $stmt = $conn->prepare("DELETE FROM news WHERE news_id = ?");
  $stmt->bind_param("i", $_POST['delete_id']);
  $stmt->execute();
  header("Location: news_manage.php");
  exit;
}

// 查詢所有消息
$news_result = $conn->query("SELECT * FROM news ORDER BY publish_date DESC");
$edit = false;
if (isset($_GET['edit'])) {
  $edit = true;
  $news_id = $_GET['edit'];
  $stmt = $conn->prepare("SELECT * FROM news WHERE news_id = ?");
  $stmt->bind_param("i", $news_id);
  $stmt->execute();
  $edit_data = $stmt->get_result()->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
  <meta charset="UTF-8">
  <title>最新消息管理</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    html, body {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    body {
      padding-top: 100px;
      font-family: "Microsoft JhengHei", sans-serif;
    }

    .navbar {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      width: 100vw;
      height: 80px;
      background-color: #003366;
      padding: 10px 30px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      z-index: 999;
    }

    .navbar img {
      height: 60px;
      display: block;
      object-fit: contain;
    }

    .navbar .nav-links a {
      color: white;
      text-decoration: none;
      margin-left: 40px;
      font-size: 18px;
      font-weight: bold;
    }

    .navbar .nav-links a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="navbar">
    <img src="cloud.png" alt="All-en Logo">
    <div class="nav-links">
      <a href="index.php">主選單</a>
    </div>
  </div>

  <div class="container">
    <h2 class="mb-4">最新消息管理</h2>

    <!-- 圖片上傳 -->
    <?= $image_upload_message ?>
    <form method="POST" enctype="multipart/form-data" class="row g-3 mb-5">
      <div class="col-md-6">
        <label class="form-label">上傳圖片（不寫入資料庫）</label>
        <input type="file" name="image_file" class="form-control" required>
      </div>
      <div class="col-12">
        <button type="submit" name="upload_image" class="btn btn-success">上傳圖片</button>
      </div>
    </form>

    <!-- 新增/編輯表單 -->
    <form method="POST" class="row g-3 mb-4">
      <?php if ($edit): ?>
        <input type="hidden" name="news_id" value="<?= htmlspecialchars($edit_data['news_id']) ?>">
      <?php endif; ?>
      <div class="col-md-6">
        <label class="form-label">標題</label>
        <input type="text" name="title" class="form-control" required value="<?= $edit ? htmlspecialchars($edit_data['title']) : '' ?>">
      </div>
      <div class="col-md-6">
        <label class="form-label">狀態</label>
        <select name="status" class="form-select">
          <option value="草稿" <?= $edit && $edit_data['status'] === '草稿' ? 'selected' : '' ?>>草稿</option>
          <option value="已發佈" <?= $edit && $edit_data['status'] === '已發佈' ? 'selected' : '' ?>>已發佈</option>
          <option value="下架" <?= $edit && $edit_data['status'] === '下架' ? 'selected' : '' ?>>下架</option>
        </select>
      </div>
      <div class="col-md-6">
        <label class="form-label">圖片網址（自動貼上也可手動改）</label>
        <input type="text" name="image_url" class="form-control" value="<?= $edit ? htmlspecialchars($edit_data['image_url']) : '' ?>">
        <div id="image-preview" class="mt-2">
          <?php if ($edit && $edit_data['image_url']): ?>
            <img src="<?= htmlspecialchars($edit_data['image_url']) ?>" style="max-height: 100px;">
          <?php endif; ?>
        </div>
      </div>
      <div class="col-md-6">
        <label class="form-label">相關連結</label>
        <input type="text" name="link_url" class="form-control" value="<?= $edit ? htmlspecialchars($edit_data['link_url']) : '' ?>">
      </div>
      <div class="col-12">
        <label class="form-label">內容</label>
        <textarea name="content" class="form-control" rows="5" required><?= $edit ? htmlspecialchars($edit_data['content']) : '' ?></textarea>
      </div>
      <div class="col-12 text-end">
        <button type="submit" name="save" class="btn btn-primary"><?= $edit ? '更新消息' : '新增消息' ?></button>
        <?php if ($edit): ?><a href="news_manage.php" class="btn btn-secondary">取消</a><?php endif; ?>
      </div>
    </form>

    <!-- 消息列表 -->
    <table class="table table-bordered table-striped">
      <thead class="table-light">
        <tr>
          <th>標題</th>
          <th>狀態</th>
          <th>發佈時間</th>
          <th>操作</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $news_result->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($row['title']) ?></td>
            <td><?= htmlspecialchars($row['status']) ?></td>
            <td><?= htmlspecialchars($row['publish_date']) ?></td>
            <td>
              <a href="?edit=<?= $row['news_id'] ?>" class="btn btn-sm btn-warning">編輯</a>
              <form method="POST" style="display:inline;" onsubmit="return confirm('確定刪除這則消息？');">
                <input type="hidden" name="delete_id" value="<?= $row['news_id'] ?>">
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
