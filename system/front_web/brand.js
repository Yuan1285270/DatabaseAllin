function showBrand(brand) {
    const brands = ['legend', 'haugas'];
    brands.forEach(id => {
      document.getElementById(id).classList.remove('active');
      document.getElementById(id).style.display = 'none';
      document.getElementById('btn-' + id).classList.remove('active');
    });
  
    document.getElementById(brand).classList.add('active');
    document.getElementById(brand).style.display = 'block';
    document.getElementById('btn-' + brand).classList.add('active');
  }
  
  // 預設載入時顯示 LegendWalker
  document.addEventListener("DOMContentLoaded", () => {
    showBrand('legend');
  });