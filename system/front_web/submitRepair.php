<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'db.php';
date_default_timezone_set('Asia/Taipei');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // 📌 Step 1. 收集表單資料
  $buyer_name = $_POST['buyer_name'];
  $buyer_phone = $_POST['buyer_phone'];
  $reporter_name = $_POST['reporter_name'];
  $reporter_phone = $_POST['reporter_phone'];
  $email = $_POST['email'];
  $receive_address = $_POST['receive_address'];
  $send_address = $_POST['send_address'];
  $pickup_date = $_POST['pickup_date'];
  $product_code = $_POST['product_code'];
  $spec = $_POST['spec'];
  $color = $_POST['color'];
  $description = $_POST['description'];

  // 📌 Step 2. 插入 repair 主表（預設狀態與會員資料，可根據登入狀態設定）
  $repair_id = uniqid();  // or auto-increment if your DB uses INT
  $request_date = date("Y-m-d H:i:s");
  $status = 0;
  $warranty_due = date("Y-m-d", strtotime("+30 days"));  // ⏰ 預設保固到期日
  $warranty_state = $_POST['warranty_state'];
  $member_id = '002';     // 若有登入系統可改為 $_SESSION['member_id']
  $product_id = '001';    // 若有商品選擇可從表單取得

  $stmt1 = $conn->prepare("INSERT INTO repair (repair_id, request_date, problem_description, status, warranty_due, warranty_state, member_id, product_id)
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
  $stmt1->bind_param("sssissss", $repair_id, $request_date, $description, $status, $warranty_due, $warranty_state, $member_id, $product_id);
  $stmt1->execute();

  // 📌 Step 3. 插入 repair_request 表（詳細填寫內容）
  $stmt2 = $conn->prepare("INSERT INTO repair_request (
    repair_id, buyer_name, buyer_phone, reporter_name, reporter_phone, email,
    receive_address, send_address, pickup_date, product_code, product_size, product_color, problem_description, warranty_state
  ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

  $stmt2->bind_param("ssssssssssssss", $repair_id, $buyer_name, $buyer_phone, $reporter_name, $reporter_phone, $email,
    $receive_address, $send_address, $pickup_date, $product_code, $spec, $color, $description, $warranty_state);
  $stmt2->execute();

  // 📌 Step 4. 回應
  if ($stmt1->affected_rows > 0 && $stmt2->affected_rows > 0) {
    echo "<script>alert('✅ 維修通報已送出成功！'); window.location.href='repairSearch.php';</script>";
  } else {
    echo "<script>alert('❌ 維修通報失敗，請稍後再試'); history.back();</script>";
  }

  // 📌 關閉連線
  $stmt1->close();
  $stmt2->close();
  $conn->close();
}
?>