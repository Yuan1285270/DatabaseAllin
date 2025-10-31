<!DOCTYPE html>
<html lang="zh-Hant">
<head>
  <meta charset="UTF-8" />
  <title>註冊帳號 | 歐印 ALL-EN</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .register-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 40px;
    }
    .register-box {
        width: 100%;
        max-width: 400px;
        background-color: #ffffffcc;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    .register-box h2 {
        text-align: center;
        color: #2c3e50;
        margin-bottom: 20px;
    }
    .register-box label {
        display: block;
        margin-top: 15px;
        font-weight: bold;
    }
    .register-box input {
        width: 100%;
        padding: 10px;
        margin-top: 5px;
        border-radius: 6px;
        border: 1px solid #ccc;
        box-sizing: border-box;
    }
    .register-box button {
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
    .register-box button:hover {
        background-color: #2980b9;
    }
  </style>
</head>
<body>
  <?php include("common/header.php"); ?>

  <main class="register-wrapper">
    <div class="register-box">
      <h2>📋 註冊新帳號</h2>
      <form method="POST" action="register_save.php">
        <label for="name">姓名</label>
        <input type="text" id="name" name="name" required>

        <label for="email">電子郵件</label>
        <input type="email" id="email" name="email" required>

        <label for="phone">電話號碼</label>
        <input type="text" id="phone" name="phone" required>

        <label for="password">密碼</label>
        <input type="password" id="password" name="password" required>

        <label for="confirm">再次輸入密碼</label>
        <input type="password" id="confirm" name="confirm_password" required>

        <button type="submit">註冊</button>
      </form>
    </div>
  </main>
  <?php include("common/ai_widget.php"); ?>
  <?php include("common/footer.php"); ?>
</body>
</html>
