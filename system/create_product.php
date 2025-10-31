<?php
// 開啟錯誤顯示
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 匯入資料庫連線
include 'db.php';
echo "✅ 資料庫已連線<br>";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "✅ 表單已送出<br>";
    $product_id = $_POST['product_id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $status = $_POST['status'];
    $warranty_due = $_POST['warranty_due'];
    $price = $_POST['price'];
    $storage = $_POST['storage'];
    $image = $_POST['image'];
    $class = $_POST['class'];
    $admin_id = $_POST['admin_id'];
    

    $sql = "INSERT INTO products (product_id, name, description, status, warranty_due, price, storage, image, class, admin_id)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?,?, ?)";

    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        die("❌ prepare 錯誤：" . $conn->error);
    }

    $stmt->bind_param("ssssssssss", $product_id, $name, $description, $status, $warranty_due, $price, $storage, $image, $class, $admin_id);

    if ($stmt->execute()) {
        echo "✅ 新增成功！<br><a href='products_list.php'>前往產品列表</a><hr>";
    } else {
        echo "❌ 執行失敗：" . $stmt->error . "<hr>";
    }

    $stmt->close();
}
?>

<!-- 表單區塊 -->
<h2>新增產品</h2>
<form method="post">
    <label>產品 ID product_id:<input type="number" name="product_id" required></label><br><br>
    名稱：<input type="text" name="name" required><br><br>
    描述：<textarea name="description"></textarea><br><br>
    狀態：<input type="text" name="status"><br><br>
    保固到期日：<input type="date" name="warranty_due"><br><br>
    價格：<input type="number" name="price" step="0.01"><br><br>
    儲存位置：<input type="text" name="storage"><br><br>
    圖片網址：<input type="text" name="image"><br><br>
    分類（class）：<input type="text" name="class"><br><br>
    管理員 ID：<input type="number" name="admin_id"><br><br>
    <button type="submit">送出</button>
</form>
