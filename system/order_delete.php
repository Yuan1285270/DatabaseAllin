<?php
include 'navbar.php';
/* ---------- 基本設定 ---------- */
ini_set('display_errors', 1);
error_reporting(E_ALL);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

include 'db.php';      // $conn

$order_id = $_GET['order_id'] ?? '';
if (!$order_id) die('缺少訂單 ID');

try {
    $conn->begin_transaction();

    // 先刪 orders_product
    $stmt = $conn->prepare('DELETE FROM orders_product WHERE order_id = ?');
    $stmt->bind_param('s', $order_id);
    $stmt->execute();

    // 再刪 orders
    $stmt = $conn->prepare('DELETE FROM orders WHERE order_id = ?');
    $stmt->bind_param('s', $order_id);
    $stmt->execute();

    $conn->commit();

    // 刪完回訂單總覽
    header('Location: orders_manage.php?msg=deleted');
    exit;

} catch (Throwable $e) {
    $conn->rollback();
    exit('❌ 刪除失敗：' . $e->getMessage());
}
