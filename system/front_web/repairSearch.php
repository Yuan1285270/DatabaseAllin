<!DOCTYPE html>
<html lang="zh-Hant">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>維修單查詢 | 歐印 all-en</title>
  <link rel="stylesheet" href="style.css" />
  <style>
    .search-form {
      max-width: 600px;
      margin: 50px auto;
      padding: 30px;
      background-color: #fff;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .search-form h2 {
      text-align: center;
      margin-bottom: 30px;
      color: #2980b9;
    }

    label {
      display: block;
      margin-top: 15px;
      font-weight: bold;
    }

    input {
      width: 100%;
      padding: 10px;
      margin-top: 5px;
      border-radius: 6px;
      border: 1px solid #ccc;
      box-sizing: border-box;
    }

    button {
      margin-top: 25px;
      width: 100%;
      background-color: #3498db;
      color: white;
      padding: 12px;
      font-size: 16px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
    }

    button:hover {
      background-color: #2980b9;
    }
  </style>
</head>
<body>
  <?php include("common/header.php"); ?>

  <main>
    <form class="search-form" action="searchResult.php" method="POST">
      <h2>🔍 維修訂單查詢系統</h2>

      <label for="name">通報人姓名</label>
      <input type="text" id="name" name="reporter_name" required>

      <label for="phone">通報人電話</label>
      <input type="text" id="phone" name="reporter_phone" required>

      <button type="submit">查詢維修狀態</button>
    </form>
  </main>
  <?php include("common/ai_widget.php"); ?>
  <?php include("common/footer.php"); ?>
</body>
</html>
