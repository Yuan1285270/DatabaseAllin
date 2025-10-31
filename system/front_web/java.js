// script.js
// 歐印商店 JavaScript 互動控制

console.log("Ouyin 商店網站載入完成");

function showBrand(brand) {
    document.getElementById("legend").style.display = "none";
    document.getElementById("haugas").style.display = "none";
    document.getElementById("btn-legend").classList.remove("active");
    document.getElementById("btn-haugas").classList.remove("active");
  
    document.getElementById(brand).style.display = "block";
    document.getElementById("btn-" + brand).classList.add("active");
  }
  

