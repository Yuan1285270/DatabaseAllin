<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
include 'db.php';

$msg = '';
$action = $_REQUEST['action'] ?? '';
$id     = $_REQUEST['id']     ?? '';

if ($action === 'delete' && $id) {
    $stmt = $conn->prepare('DELETE FROM ai_prompt WHERE id = ?');
    $stmt->bind_param('i', $id);
    $msg = $stmt->execute()
         ? '✅ 已刪除 prompt #' . htmlspecialchars($id)
         : '❌ 刪除失敗：' . $stmt->error;
    $stmt->close();
    header('Location: ' . $_SERVER['PHP_SELF'] . '?msg=' . urlencode($msg));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($action, ['add','update'])) {
    $fields = ['destination','url','response_text','enabled'];
    foreach ($fields as $f) $$f = trim($_POST[$f] ?? '');
    $enabled = isset($_POST['enabled']) ? 1 : 0;

    if (!$destination) die('❌ destination 必填');

    if ($action === 'add') {
        $stmt = $conn->prepare("INSERT INTO ai_prompt (destination, url, response_text, enabled) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('sssi', $destination, $url, $response_text, $enabled);
    } else {
        $stmt = $conn->prepare("UPDATE ai_prompt SET destination=?, url=?, response_text=?, enabled=? WHERE id=?");
        $stmt->bind_param('sssii', $destination, $url, $response_text, $enabled, $id);
    }

    $msg = $stmt->execute()
         ? ($action === 'add' ? '✅ 新增成功！' : '✅ 更新成功！')
         : '❌ 執行失敗：' . $stmt->error;
    $stmt->close();
    header('Location: ' . $_SERVER['PHP_SELF'] . '?msg=' . urlencode($msg));
    exit;
}

$edit_data = null;
if ($action === 'edit' && $id) {
    $stmt = $conn->prepare('SELECT * FROM ai_prompt WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $edit_data = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

$stmt = $conn->prepare('SELECT * FROM ai_prompt ORDER BY updated_at DESC');
$stmt->execute();
$list = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
  <meta charset="UTF-8">
  <title>AI Prompt 管理</title>
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
<div class="container py-5">
  <h2 class="mb-4">AI Prompt 管理</h2>

  <?php if ($msg = ($_GET['msg'] ?? $msg)): ?>
    <div class="alert alert-info"> <?= $msg ?> </div>
  <?php endif; ?>

  <?php $is_edit = ($action === 'edit' && $edit_data); ?>
  <div class="card mb-4">
    <div class="card-header"> <?= $is_edit ? '編輯 Prompt #' . $edit_data['id'] : '新增 Prompt' ?> </div>
    <div class="card-body">
      <form method="post">
        <input type="hidden" name="action" value="<?= $is_edit ? 'update' : 'add' ?>">
        <?php if ($is_edit): ?>
          <input type="hidden" name="id" value="<?= $edit_data['id'] ?>">
        <?php endif; ?>

        <div class="mb-3">
          <label class="form-label">關鍵字</label>
          <input type="text" name="destination" class="form-control" required
                 value="<?= htmlspecialchars($edit_data['destination'] ?? '') ?>">
        </div>

        <div class="mb-3">
          <label class="form-label">功能網址</label>
          <input type="text" name="url" class="form-control"
                 value="<?= htmlspecialchars($edit_data['url'] ?? '') ?>">
        </div>

        <div class="mb-3">
          <label class="form-label">特殊回應文字</label>
          <textarea name="response_text" class="form-control" rows="3"><?= htmlspecialchars($edit_data['response_text'] ?? '') ?></textarea>
        </div>

        <div class="form-check form-switch mb-3">
          <input class="form-check-input" type="checkbox" name="enabled" id="enabledSwitch" <?= ($edit_data['enabled'] ?? 1) ? 'checked' : '' ?>>
          <label class="form-check-label" for="enabledSwitch">啟用</label>
        </div>

        <div class="text-end">
          <button class="btn btn-primary"><?= $is_edit ? '儲存變更' : '新增 Prompt' ?></button>
          <?php if ($is_edit): ?>
            <a href="<?= $_SERVER['PHP_SELF'] ?>" class="btn btn-outline-secondary ms-2">取消</a>
          <?php endif; ?>
        </div>
      </form>
    </div>
  </div>

  <table class="table table-bordered table-hover">
    <thead class="table-light">
      <tr>
        <th>ID</th>
        <th>關鍵字</th>
        <th>網址</th>
        <th>回應文字</th>
        <th>啟用</th>
        <th>操作</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = $list->fetch_assoc()): ?>
        <tr>
          <td><?= $row['id'] ?></td>
          <td><?= htmlspecialchars($row['destination']) ?></td>
          <td><?= htmlspecialchars($row['url']) ?></td>
          <td><?= htmlspecialchars($row['response_text']) ?></td>
          <td><?= $row['enabled'] ? '✅' : '❌' ?></td>
          <td>
            <a href="?action=edit&id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">編輯</a>
            <a href="?action=delete&id=<?= $row['id'] ?>" class="btn btn-sm btn-danger ms-1"
               onclick="return confirm('確定要刪除這筆 Prompt 嗎？');">刪除</a>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>
</body>
</html>
