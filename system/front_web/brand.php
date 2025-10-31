<?php
// 可選：session_start(); 如未用到登入狀態，可省略
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>品牌介紹 - 歐印 Boutique</title>
  <link rel="stylesheet" href="style.css" />
  <link rel="stylesheet" href="brand.css" />
  <script src="brand.js" defer></script>
</head>
<body>
  <?php include("common/header.php"); ?>

  <main class="brand-wrapper">
    <img src="images/title.png" alt="歐印 LOGO" class="brand-logo" />
    <h2 class="brand-title">品牌介紹</h2>

    <div class="brand-text">
      <p>觀摩日本、發跡台灣、走向世界，我們是旅遊的獨角獸！創辦人夫婦在遨遊各國參展旅遊中結交各國的朋友，文化交流外不僅把所學的國際貿易和工藝技術運用在「品牌行李箱的台灣發展事業」，也確認「輕量化，設計感，功能性，」才是所謂的夢幻行李箱！</p>
      <p>本著雄厚汽車改裝技術為背景更成立專業的後勤售服團隊，提供旅人安心的保證！也要求團隊全員誠懇親切來體現「微笑愛地球、世界跟著走」的企業宗旨，及「歐印精品」的核心價值。「箱子」是旅人不可缺少的工具，就從現在起～「箱子的事就交給歐印！」創造、享受旅行的意義就交給獨角獸們！</p>
    </div>

    <h3 class="subheading">以下為歐印代理之品牌</h3>

    <div class="brand-buttons">
      <button onclick="showBrand('legend')" id="btn-legend" class="active">LegendWalker</button>
      <button onclick="showBrand('haugas')" id="btn-haugas">Haugas</button>
    </div>

    <div id="legend" class="brand-desc active">
      <img src="images/legendwalker.png" alt="Legend Walker Logo" class="brand-image"/>
      <p>時尚品牌 LEGEND WALKER 與 WORLD TRUNK 創始於1998年日本越谷市，產品設計以堅固、耐用、時尚的特色為主。並以復古元素與細膩工法相結合來象徵產品的特點，在近二十年的發展歷程中，演繹出能為全球的旅行者提供舒適而可靠的高品質創新產品。</p>
      <p>LEGEND WALKER與WORLD TRUNK集古典與現代設計於一體，與日乃本公司合作研發360度旋轉靜音輪、段式拉桿堅固耐用，並輔以全手工製作，功能性商品更是業界翹楚，首選德國PC材質讓箱體更輕量以符合時勢所趨。</p>
      <p>因產品具特色，深受日本的偶像劇青睞作為道具，同時更成為日本少女追逐的頂級夢幻箱首選。LEGEND WALKER 與 WORLD TRUNK 每一步用心都將伴您旅途的傳奇！</p>
    </div>

    <div id="haugas" class="brand-desc">
      <img src="images/haugas.jpg" alt="Haugas Logo" class="brand-image"/>
      <p>“HAUGAS” 是來自北歐的設計師 Leila Haugas 個人創設的旅行箱品牌，她以家族姓氏之意義為品牌命名，而在愛沙尼亞語中，此詞彙則是意指「老鷹」，寓含著以老鷹的自由、敏捷與準確，帶領旅人遨遊世界各地的展望。</p>
      <p>“HAUGAS”品牌為環境關懷不遺餘力，旗下產品皆以北歐簡約的設計概念結合現代精緻工藝，兼顧環保與耐用，讓使用的旅人如老鷹展翅般航向天際，創造友善環境、打造生活美學的品牌精神！</p>
    </div>
  </main>
  <?php include("common/ai_widget.php"); ?>
  <?php include("common/footer.php"); ?>
</body>
</html>
