document.addEventListener("DOMContentLoaded", () => {
  const cartList = document.getElementById("cart-list");
  const emptyMessage = document.getElementById("empty-message");
  const orderBtn = document.getElementById("order-button");

  // 🛒 模擬購物車內容
  const cartItems = []; // 可改為 ['5114 行李箱', '5603 行李箱']

  function updateCart() {
    cartList.innerHTML = "";
    if (cartItems.length === 0) {
      emptyMessage.style.display = "block";
      orderBtn.disabled = true;
      orderBtn.style.opacity = 0.5;
    } else {
      emptyMessage.style.display = "none";
      orderBtn.disabled = false;
      orderBtn.style.opacity = 1;
      cartItems.forEach(item => {
        const li = document.createElement("li");
        li.textContent = item;
        cartList.appendChild(li);
      });
    }
  }

  updateCart();

  orderBtn.addEventListener("click", () => {
    alert("模擬：訂單已送出！");
    // 清空購物車也可以加入 cartItems.length = 0; updateCart();
  });
});
