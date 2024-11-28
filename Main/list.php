<?php
// Query SPARQL untuk mendapatkan data makanan
$query = "
SELECT DISTINCT ?category1 ?name ?description ?rating ?image1 ?region
WHERE {
  ?d a food:Description .
  ?d rdfs:label ?name .
  ?d food:abstract ?description .
  ?d food:rating ?rating .
  ?d food:image1 ?image1 .
  ?d foaf:region ?region .
  ?d food:category1 ?category1 .
}
ORDER BY DESC(?rating)
";
$result = $sparqlJena->query($query);

$foodByCategory = [];
$allFoods = [];

// Memproses hasil query
foreach ($result as $data) {
  $category1 = (string)$data->category1->getValue(); // Ambil kategori utama
  $foodByCategory[$category1][] = $data; // Kelompokkan berdasarkan kategori utama
  $allFoods[] = $data; // Simpan semua makanan untuk ditampilkan
}

// Fungsi membatasi jumlah kata dalam deskripsi
function limit_words($text, $limit)
{
  $words = explode(' ', $text);
  return count($words) > $limit ? implode(' ', array_slice($words, 0, $limit)) . '...' : $text;
}
?>

<div class="container my-5">
  <h2 class="text-center mb-4 menu-title"><span>M</span>enu <span>M</span>akanan</h2>

  <!-- Tombol Kategori -->
  <div class="text-center mb-4">
    <button class="btn btn-outline-success mx-2 category-btn active" data-category="all">Semua Makanan</button>
    <?php foreach (array_keys($foodByCategory) as $category1) : ?>
      <!-- Tombol kategori utama -->
      <button class="btn btn-outline-success mx-2 category-btn" data-category="<?= urlencode($category1) ?>">
        <?= $category1 ?>
      </button>
    <?php endforeach; ?>
  </div>

  <!-- Daftar Makanan -->
  <?php if (empty($allFoods)) : ?>
    <div class="alert alert-warning text-center">Data not found!</div>
  <?php else : ?>
    <div id="food-container" class="row row-cols-1 row-cols-md-3 g-4">
      <?php foreach ($allFoods as $data) : ?>
        <?php $category1 = (string)$data->category1->getValue(); ?>
        <div class="col food-item" data-category="<?= urlencode($category1) ?>">
          <div class="card shadow h-100" style="border-radius: 20px; overflow: hidden;">
            <div class="position-relative">
              <img src="<?= $data->image1 ?>" class="card-img-top img-fluid" alt="<?= $data->name ?>" onerror="this.onerror=null; this.src='./assets/img/default-food.jpg';" style="height: 200px; object-fit: cover;">
              <div class="rating position-absolute top-0 start-0 m-2">
                <?php $rating = isset($data->rating) ? (float)$data->rating->getValue() : 0; ?>
                <span class="badge bg-warning text-dark"><?= number_format($rating, 1) ?></span>
              </div>
            </div>
            <div class="card-body text-center">
              <h5 class="card-title text-truncate" style="color: #16a085;"><?= $data->name ?></h5>
              <p class="card-text text-muted"><?= limit_words($data->description, 40) ?></p>
              <a href="?x=detail&id=<?= urlencode($data->name) ?>&region=<?= urlencode($data->region) ?>" class="btn btn-green" style="background-color: #16a085; color: white; border-radius: 20px;">
                Lihat Detail
              </a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>