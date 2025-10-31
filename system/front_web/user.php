<!-- user.php -->
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>會員登入 | 歐印 ALL-EN</title>
  <link rel="stylesheet" href="style.css" />
  <link rel="stylesheet" href="user.css" />
</head>
<body>
  <?php include("common/header.php"); ?>

  <!-- 🔐 登入區塊 -->
  <main class="login-wrapper">
    <div class="login-box">
      <h2>🔐 歡迎登入會員</h2>
      <form method="POST" action="login_check.php">
        <input type="email" name="email" placeholder="電子郵件 Email" required />
        <input type="password" name="password" placeholder="密碼" required />
        <button type="submit" name="login">登入</button>
      </form>

      <div class="divider">或</div>

      <a href="https://google-login-dw05.onrender.com/google_login.php" class="google-login-btn">使用 Google 帳戶登入</a>

      <div class="register-link" style="margin-top: 20px; text-align: center;">
        還沒有帳號？<a href="register.php" class="btn" style="margin-left: 8px;">註冊帳號</a>
      </div>
    </div>
  </main>
  <?php include("common/ai_widget.php"); ?>
  <?php include("common/footer.php"); ?>
</body>
</html>
