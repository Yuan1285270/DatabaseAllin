<?php
include 'navbar.php';
session_start();
require 'db.php';
$db = $conn;

if (isset($_POST['username'], $_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $db->prepare("SELECT admin_id, password FROM admin WHERE username = ? LIMIT 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($row = $res->fetch_assoc()) {
        if ($password === $row['password']) { // 明碼比對
            $_SESSION['admin_id'] = $row['admin_id'];
            header("Location: index.php");
            exit;
        } else {
            $error = "密碼錯誤";
        }
    } else {
        $error = "找不到此帳號";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>管理員登入</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f3f3f3;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .login-box {
            background: white;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            width: 300px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 8px 10px;
            margin: 8px 0 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #0077cc;
            border: none;
            border-radius: 5px;
            color: white;
            font-weight: bold;
        }

        .error {
            color: red;
            text-align: center;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>管理員登入</h2>
        <?php if (isset($error)) echo "<div class='error'>$error</div>"; ?>
        <form method="POST">
            <label>帳號</label>
            <input type="text" name="username" required>
            <label>密碼</label>
            <input type="password" name="password" required>
            <button type="submit">登入</button>
        </form>
    </div>
</body>
</html>
