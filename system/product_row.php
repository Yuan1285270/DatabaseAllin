<?php
// 檔名：product_row.php

?>
<div class="row align-items-end product-row mb-2">
  <div class="col-md-5">
    <label class="form-label">商品</label>
    <select name="product_id[]" class="form-select product-select" required>
      <?php
      // 重設指標，確保每次 include 都可以跑完整迴圈
      mysqli_data_seek($product_list, 0);
      while ($p = $product_list->fetch_assoc()):
          $pid    = $p['product_id'];
          $name   = htmlspecialchars($p['name']);
          $remain = isset($remaining[$pid]) ? $remaining[$pid] : 0;
      ?>
        <option value="<?= $pid ?>" data-remaining="<?= $remain ?>">
          <?= $pid ?> - <?= $name ?>
        </option>
      <?php endwhile; ?>
    </select>
  </div>
  <div class="col-md-2">
    <label class="form-label">數量</label>
    <input type="number" name="quantity[]" class="form-control" value="1" min="1" required>
  </div>
  <div class="col-md-3">
    <label class="form-label">剩餘庫存</label>
    <div class="remaining-display">--</div>
  </div>
  <div class="col-md-2 text-end">
    <button type="button" class="btn btn-danger btn-remove mt-4">移除</button>
  </div>
</div>
