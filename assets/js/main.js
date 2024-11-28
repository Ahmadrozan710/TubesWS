document.querySelectorAll('.category-btn').forEach(button => {
  button.addEventListener('click', e => {
    e.preventDefault();
    const selectedCategory = button.getAttribute('data-category');
    document.querySelectorAll('.food-item').forEach(item => {
      const itemCategory = item.getAttribute('data-category');
      // Tampilkan semua makanan jika "Semua Makanan" dipilih atau kategori cocok
      if (selectedCategory === 'all' || itemCategory === selectedCategory) {
        item.style.display = 'block';
      } else {
        item.style.display = 'none';
      }
    });

    // Tambahkan kelas "active" pada tombol yang dipilih
    document.querySelectorAll('.category-btn').forEach(btn => btn.classList.remove('active'));
    button.classList.add('active');
  });
});

