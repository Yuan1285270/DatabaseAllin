function showCategory(id) {
  const blocks = document.querySelectorAll('.category-block');
  blocks.forEach(b => b.classList.remove('active'));
  document.getElementById(id).classList.add('active');

  const buttons = document.querySelectorAll('.category-menu button');
  buttons.forEach(b => b.classList.remove('active'));
  document.querySelector(`.category-menu button[onclick*="${id}"]`).classList.add('active');
}
