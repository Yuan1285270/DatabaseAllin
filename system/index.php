<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title>後台主頁</title>
    <style>
        html, body {
            margin: 0;
            padding: 0;
        }
        body {
            font-family: "Microsoft JhengHei", sans-serif;
            background-color: white;
            padding-top: 100px; /* 推開 navbar 高度 */
        }
        .container {
            background-color: white;
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
        }
        .card-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 30px;
        }
        .card {
            background-color: rgb(48, 132, 200);
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            aspect-ratio: 1 / 1;             /* ✅ 正方形關鍵 */
            display: flex;                   /* ✅ 置中文字 */
            align-items: center;
            justify-content: center;
            transition: transform 0.2s;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .card a {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-decoration: none;
            color: white;
            font-size: 20px;
            font-weight: bold;
        }
        .card img {
            max-height: 100px;
            margin-bottom: 30px;
            filter: brightness(0) invert(1);
        }
    </style>
</head>

<body>
    <?php include 'navbar.php'; ?>

    <div class="container">
        <div class="card-grid">
            <div class="card">
                <a href="member_manage.php">
                    <img src="images/icons/member.png" alt="會員管理圖示">
                    <div>會員管理</div>
                </a>
            </div>
            <div class="card">
                <a href="product_manage.php">
                    <img src="images/icons/product.png" alt="商品管理圖示">
                    <div>商品管理</div>
                </a>
            </div>
            <div class="card">
                <a href="orders_manage.php">
                    <img src="images/icons/order.png" alt="訂單查詢圖示">
                    <div>訂單查詢</div>
                </a>
            </div>
            <div class="card">
                <a href="vwarehouse_manage.php">
                    <img src="images/icons/warehouse.png" alt="虛擬倉庫圖示">
                    <div>虛擬倉庫管理</div>
                </a>
            </div>
            <div class="card">
                <a href="repair_manage.php">
                    <img src="images/icons/repair.png" alt="維修圖示">
                    <div>維修回報紀錄</div>
                </a>
            </div>
            <div class="card">
                <a href="discount_manage.php">
                    <img src="images/icons/discount.png" alt="折扣圖示">
                    <div>折扣碼管理</div>
                </a>
            </div>
            <div class="card">
                <a href="news_manage.php">
                    <img src="images/icons/news.png" alt="新聞圖示">
                    <div>最新消息</div>
                </a>
            </div>
            <div class="card">
                <a href="prompt.php">
                    <img src="images/icons/AI_wingman.png" alt="AI圖示">
                    <div>AI助理</div>
                </a>
            </div>
        </div>
    </div>
</body>
</html>
