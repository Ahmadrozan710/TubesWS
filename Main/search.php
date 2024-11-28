<?php
$search = isset($_GET['search']) ? $_GET['search'] : ''; // Ambil kata kunci pencarian dari URL

// Fungsi untuk memotong deskripsi agar tidak terlalu panjang
function limit_words($text, $word_limit = 40) {
    $words = explode(' ', $text); // Memecah teks menjadi kata
    if (count($words) > $word_limit) {
        $words = array_slice($words, 0, $word_limit); // Potong jika kata melebihi limit
        $text = implode(' ', $words) . '...'; // Tambahkan "..." di akhir
    }
    return $text;
}

// Pemetaan nama lokasi ke URI
$locationMapping = [
  'West Java' => 'http://dbpedia.org/resource/West_Java',
  'Jawa Barat' => 'http://dbpedia.org/resource/West_Java',
  'jawa barat' => 'http://dbpedia.org/resource/West_Java', 
  'Central Java' => 'http://dbpedia.org/resource/Central_Java',
  'Jawa Tengah' => 'http://dbpedia.org/resource/Central_Java',
  'jawa tengah' => 'http://dbpedia.org/resource/Central_Java',
  'East Java' => 'http://dbpedia.org/resource/East_Java',
  'Jawa Timur' => 'http://dbpedia.org/resource/East_Java',
  'jawa timur' => 'http://dbpedia.org/resource/East_Java',
  'Java' => 'http://dbpedia.org/resource/Java',
  'Jawa' => 'http://dbpedia.org/resource/Java',
  'jawa' => 'http://dbpedia.org/resource/Java',
  'West Sumatra' => 'http://dbpedia.org/resource/West_Sumatra',
  'Sumatera Barat' => 'http://dbpedia.org/resource/West_Sumatra',
  'sumatera barat' => 'http://dbpedia.org/resource/West_Sumatra',
  'Aceh' => 'http://dbpedia.org/resource/Aceh',
  'aceh' => 'http://dbpedia.org/resource/Aceh',
  'Palembang' => 'http://dbpedia.org/resource/Palembang',
  'palembang' => 'http://dbpedia.org/resource/Palembang',
  'South Sumatra' => 'http://dbpedia.org/resource/South_Sumatra',
  'Sumatera Selatan' => 'http://dbpedia.org/resource/South_Sumatra',
  'sumatera selatan' => 'http://dbpedia.org/resource/South_Sumatra',
  'Sumatra Selatan' => 'http://dbpedia.org/resource/South_Sumatra',
  'sumatra selatan' => 'http://dbpedia.org/resource/South_Sumatra',
  'Sumatera' => 'http://dbpedia.org/resource/Sumatra',
  'sumatera' => 'http://dbpedia.org/resource/Sumatra',
  'Sumatra' => 'http://dbpedia.org/resource/Sumatra',
  'sumatra' => 'http://dbpedia.org/resource/Sumatra',
];

// Cek apakah pencarian ada di dalam pemetaan lokasi
$locationURI = isset($locationMapping[$search]) ? $locationMapping[$search] : '';

// Pastikan pencarian hanya terjadi jika ada kata kunci
if (!empty($search)) {
    // Query SPARQL untuk mencari makanan berdasarkan kategori, rating, atau lokasi
    if (!empty($locationURI)) {
        // Jika ada pencarian lokasi, filter berdasarkan region
        $query = "
        SELECT DISTINCT ?name ?description ?ingredient ?image1 ?rating ?category1 ?category2 ?video ?region ?region1 ?region2
        WHERE {
          ?d a food:Description . 
          ?d rdfs:label ?name . 
          ?d food:abstract ?description . 
          ?d food:image1 ?image1 . 
          ?d food:rating ?rating . 
          ?d food:category1 ?category1 . 
          ?d food:ingredient ?ingredient . 
          ?d foaf:region ?region . 

          OPTIONAL { ?d food:category2 ?category2 . }
          OPTIONAL { ?d foaf:region1 ?region1 . }
          OPTIONAL { ?d foaf:region2 ?region2 . }

          FILTER (
            REGEX(?name, '$search', 'i') || 
            REGEX(?rating, '$search', 'i') ||
            REGEX(?ingredient, '$search', 'i') ||
            REGEX(?category1, '$search', 'i') ||
            REGEX(?category2, '$search', 'i') || 
            REGEX(STR(?region), '$locationURI', 'i') ||
            REGEX(STR(?region1), '$locationURI', 'i') ||
            REGEX(STR(?region2), '$locationURI', 'i') 
          )
        }
        ORDER BY DESC(?rating)
        ";
    } else {
        // Jika tidak ada pencarian lokasi, lakukan pencarian berdasarkan kategori, rating, dan nama
        $query = "
        SELECT DISTINCT ?name ?description ?ingredient ?image1 ?rating ?category1 ?category2 ?video ?region
        WHERE {
          ?d a food:Description . 
          ?d rdfs:label ?name . 
          ?d food:abstract ?description . 
          ?d food:image1 ?image1 . 
          ?d food:rating ?rating . 
          ?d food:category1 ?category1 . 
          ?d food:ingredient ?ingredient . 
          ?d foaf:region ?region . 

          OPTIONAL { ?d food:category2 ?category2 . }

          FILTER (
            REGEX(?name, '$search', 'i') || 
            REGEX(?rating, '$search', 'i') ||
            REGEX(?ingredient, '$search', 'i') ||
            REGEX(?category1, '$search', 'i') ||
            REGEX(?category2, '$search', 'i')
          )
        }
        ORDER BY DESC(?rating)
        ";
    }
} 

// Menjalankan query SPARQL dan mendapatkan hasil
$result = $sparqlJena->query($query);
?>




<div class="container my-5">
  <h2 class="text-center mb-4" style="font-family: 'Poppins', sans-serif; color: #2c3e50;">
    <?php echo $search ? "Hasil Pencarian untuk '$search'" : "Menu Makanan"; ?>
  </h2>
  
  <?php if (empty($result)) : ?>
    <div class="alert alert-warning text-center">Data tidak ditemukan untuk pencarian ini!</div>
  <?php else : ?>
    <!-- Menampilkan Semua Makanan dalam Grid -->
    <div id="food-container" class="row row-cols-1 row-cols-md-3 g-4">
      <?php foreach ($result as $data) : ?>
        <div class="col food-item" data-category="<?= urlencode($data->category1) ?>">
          <div class="card shadow h-100" style="border-radius: 20px; overflow: hidden;">
            <div class="position-relative">
              <!-- Gambar Makanan -->
              <img src="<?= $data->image1 ?>" class="card-img-top img-fluid" alt="<?= $data->name ?>" onerror="this.onerror=null; this.src='./assets/img/default-food.jpg';" style="height: 200px; object-fit: cover;">
              
              <!-- Tanda Rating di Kiri -->
              <div class="rating position-absolute top-0 start-0 m-2">
                <?php 
                  $rating = isset($data->rating) ? (float)$data->rating->getValue() : 0; // Mengambil nilai rating sebagai float
                ?>
                <span class="badge bg-warning text-dark"><?= number_format($rating, 1) ?></span> <!-- Rating sebagai angka -->
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
