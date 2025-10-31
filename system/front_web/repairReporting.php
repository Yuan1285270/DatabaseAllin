<!DOCTYPE html>
<html lang="zh-Hant">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>維修通報表單 | 歐印 ALL-EN</title>
  <link rel="stylesheet" href="style.css" />
  <style>
    form {
      max-width: 800px;
      margin: 40px auto;
      background-color: #fff;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    form h2 {
      margin-bottom: 20px;
      color: #2980b9;
    }
    label {
      display: block;
      margin-top: 15px;
      font-weight: bold;
    }
    input, select, textarea {
      width: 100%;
      padding: 10px;
      margin-top: 5px;
      border-radius: 6px;
      border: 1px solid #ccc;
      box-sizing: border-box;
    }
    textarea {
      height: 120px;
    }
    button {
      margin-top: 20px;
      background-color: #3498db;
      color: white;
      padding: 12px 24px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-size: 16px;
    }
    button:hover {
      background-color: #2980b9;
    }
  </style>
</head>
<body>
  <?php include("common/header.php"); ?>

  <main>
    <form action="submitRepair.php" method="POST">
      <h2>維修通報表單</h2>

      <label>購買人姓名</label>
      <input type="text" name="buyer_name" required>

      <label>購買人電話</label>
      <input type="text" name="buyer_phone" required>

      <label>通報人姓名</label>
      <input type="text" name="reporter_name" required>

      <label>通報人電話</label>
      <input type="text" name="reporter_phone" required>

      <label>電郵地址</label>
      <input type="email" name="email" required>

      <label>收件地址</label>
      <input type="text" name="receive_address" required>

      <label>送件地址</label>
      <input type="text" name="send_address" required>

      <label>可派收日期</label>
      <input type="date" name="pickup_date" required>

      <label>產品系列代碼</label>
      <input type="text" name="product_code" required>

      <label>尺寸 / 規格</label>
      <input type="text" name="spec" required>

      <label>顏色</label>
      <input type="text" name="color" required>

      <label>保固狀態</label>
      <select name="warranty_state" required>
        <option value="">請選擇</option>
        <option value="1">保內</option>
        <option value="2">保外</option>
        <option value="9">不確定</option>
      </select>

      <label>狀況概述</label>
      <textarea name="description" required></textarea>

      <button type="submit">送出通報</button>
    </form>
  </main>
  <?php include("common/ai_widget.php"); ?>
  <?php include("common/footer.php"); ?>
</body>
</html>
