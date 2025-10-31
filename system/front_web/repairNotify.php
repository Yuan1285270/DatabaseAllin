<!DOCTYPE html>
<html lang="zh-Hant">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>維修通報須知 | 歐印 ALL-EN</title>
  <link rel="stylesheet" href="style.css" />
  <style>
    .notice-box {
      max-width: 800px;
      margin: 40px auto;
      background-color: #fff;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .notice-box h2 {
      color: #d35400;
    }
    .notice-box ol {
      padding-left: 20px;
    }
    .notice-box button {
      margin-top: 20px;
      background-color: #2ecc71;
      color: white;
      padding: 12px 20px;
      font-size: 16px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
    }
    .notice-box button:hover {
      background-color: #27ae60;
    }
  </style>
</head>
<body>
  <?php include("common/header.php"); ?>

  <main>
    <div class="notice-box">
      <h2>維修說明及注意事項</h2>
      <ol>
        <li>物流收件時間為周一至周六下午3點至6點左右（依當日路線收件，無法指定時間）。</li>
        <li>請以適合大小紙箱包裹行李箱，避免損傷與運費加成（損失須自行負責）。</li>
        <li>維修約需 7–14 個工作天（等待配件除外），進度可於系統查詢。</li>
        <li>請保持空箱（密碼鎖請回歸000，無海關鎖提供），恕不保管物品。</li>
        <li>完修後不提供外觀清潔（髒污與托運貼條屬之）。</li>
        <li>填報後請加入 LINE：@legendwalker，我們將與您聯繫。</li>
        <li>點選下方「確認並繼續」即代表您已同意上述內容。</li>
      </ol>
      <form action="repairReporting.php">
        <button type="submit">✅ 確認並繼續</button>
      </form>
    </div>
  </main>
  <?php include("common/ai_widget.php"); ?>
  <?php include("common/footer.php"); ?>
</body>
</html>
