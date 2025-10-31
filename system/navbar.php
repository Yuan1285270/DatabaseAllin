<!-- navbar.php -->
<style>
    * {
        box-sizing: border-box;
    }


    .navbar {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        width: 100vw;              /* 保證撐滿視窗，不受 container 影響 */
        height: 80px;
        background-color: #003366;
        padding: 10px 30px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        z-index: 999;
    }

    .navbar img {
        height: 60px;
        display: block;
        object-fit: contain;
    }

    .navbar .nav-links a,
    .navbar .nav-links a:visited {
        color: white;
        text-decoration: none;
        margin-left: 40px;
        font-size: 18px;
        font-weight: bold;
    }

    .navbar .nav-links a:hover {
        text-decoration: underline;
    }
</style>

<div class="navbar">
    <img src="cloud.png" alt="All-en Logo">
    <div class="nav-links">
        <a href="index.php">主選單</a>
    </div>
</div>
