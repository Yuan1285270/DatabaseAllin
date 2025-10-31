<!DOCTYPE html>
<html lang="zh-Hant">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>歐印 ALL-EN</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .warranty-box {
      max-width: 1000px;
      margin: 50px auto;
      padding: 20px;
      background: #fdfdfd;
    }

    .warranty-header {
      text-align: center;
      margin-bottom: 30px;
    }

    .warranty-title {
      font-size: 28px;
      margin-bottom: 15px;
      color: #1976d2;
    }

    .warranty-actions {
      display: flex;
      justify-content: center;
      gap: 20px;
    }

    .action-btn {
      padding: 12px 24px;
      font-size: 16px;
      background-color: #1976d2;
      color: white;
      border-radius: 8px;
      text-decoration: none;
      transition: background-color 0.3s ease;
    }

    .action-btn:hover {
      background-color: #125ca1;
    }

    .warranty-images {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 30px;
      margin-top: 40px;
    }

    .warranty-images img {
      width: 90%;
      max-width: 700px;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    @media (max-width: 600px) {
      .action-btn {
        font-size: 14px;
        padding: 10px 20px;
      }
      .warranty-title {
        font-size: 22px;
      }
    }
  </style>
</head>
<body>
  <?php include("common/header.php"); ?>

  <main class="warranty-box">
    <div class="warranty-header">
      <h2 class="warranty-title">🔧 維修保固</h2>
      <div class="warranty-actions">
        <a href="repairNotify.php" class="action-btn">📝 維修通報系統</a>
        <a href="repairSearch.php" class="action-btn">🔍 維修單查詢</a>
      </div>
    </div>

    <div class="warranty-images">
      <img src="images/repairContext.jpg" alt="維修說明">
      <img src="images/repairRundown.jpg" alt="維修流程">
    </div>
  </main>

  <?php include("common/ai_widget.php"); ?>
  <?php include("common/footer.php"); ?>
</body>
</html>
