<?php
session_start();
if (!isset($_SESSION['google_email'])) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Google 快速註冊 | 歐印 ALL-EN</title>
  <link rel="stylesheet" href="style.css" />
  <style>
    .google-register-wrapper {
      max-width: 500px;
      margin: 80px auto;
      background-color: #ffffff;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 6px 20px rgba(0,0,0,0.1);
    }

    .google-register-wrapper h2 {
      text-align: center;
      margin-bottom: 20px;
      color: #2c3e50;
    }

    .google-register-wrapper p {
      text-align: center;
      font-size: 16px;
      margin-bottom: 20px;
    }

    .google-register-wrapper input {
      width: 100%;
      padding: 12px;
      margin: 10px 0;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 15px;
      box-sizing: border-box;
    }

    .google-register-wrapper button {
      width: 100%;
      background-color: #4285f4;
      color: white;
      padding: 12px;
      font-size: 16px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      margin-top: 20px;
    }

    .google-register-wrapper button:hover {
      background-color: #3367d6;
    }
  </style>
</head>
<body>
  <?php include("common/header.php"); ?>

  <main>
    <form class="google-register-wrapper" method="POST" action="google_register_save.php">
      <h2>請完成註冊</h2>
      <p>Email: <?= htmlspecialchars($_SESSION['google_email']) ?></p>
      <input type="hidden" name="email" value="<?= $_SESSION['google_email'] ?>">
      <input type="text" name="name" placeholder="姓名" required>
      <input type="text" name="phone" placeholder="電話" required>
      <button type="submit">完成註冊</button>
    </form>
  </main>

  <?php include("common/footer.php"); ?>
</body>
</html>
