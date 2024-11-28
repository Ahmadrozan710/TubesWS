<?php
// Ambil ID makanan dari parameter GET
$foodName = $_GET['id'];

// Ambil nama region dari parameter GET
$regionName = $_GET['region'];  // Nama region yang ingin diambil

$locationMapping = [
    'http://dbpedia.org/resource/West_Java' => 'West Java',
    'http://dbpedia.org/resource/Central_Java' => 'Central Java',
    'http://dbpedia.org/resource/East_Java' => 'East Java',
    'http://dbpedia.org/resource/Java' => 'Java',
    'http://dbpedia.org/resource/West_Sumatra' => 'West Sumatera',
    'http://dbpedia.org/resource/Aceh' => 'Aceh',
    'http://dbpedia.org/resource/Palembang' => 'Palembang',
    'http://dbpedia.org/resource/South_Sumatra' => 'South Sumatera',
    'http://dbpedia.org/resource/Sumatra' => 'Sumatera',
  ];
  
  // Cek apakah pencarian ada di dalam pemetaan lokasi
  $locationURI = isset($locationMapping[$regionName]) ? $locationMapping[$regionName] : '';

// Query SPARQL untuk mengambil detail makanan
$query = "
SELECT ?name ?description ?ingredient ?rating ?calories ?protein ?fat ?carbohydrates ?fiber ?image1 ?homepage ?video ?category1 ?lat ?long
WHERE {
  ?food rdf:type food:Description ;
        rdfs:label ?name ;
        food:abstract ?description ;
        food:ingredient ?ingredient ;
        food:rating ?rating ;
        food:calories ?calories ;
        food:protein ?protein ;
        food:fat ?fat ;
        food:carbohydrates ?carbohydrates ;
        food:fiber ?fiber ;
        food:image1 ?image1 ;
        foaf:homepage ?homepage ;
        foaf:video ?video ;
        food:category1 ?category1 ;
        FILTER(?name = '$foodName') .
}
";

$result = $sparqlJena->query($query);

// Query SPARQL untuk mengambil latitude dan longitude berdasarkan region
$mapQuery = "
SELECT DISTINCT ?lat ?long 
WHERE {
    <$regionName> geo:lat ?lat ;
                  geo:long ?long . 
}
";

$result2 = $sparqlDbPedia->query($mapQuery);

// Mengambil hasil pertama jika ada
$location = isset($result2[0]) ? $result2[0] : null;

// Ambil elemen pertama dari hasil query makanan jika ada
$detail = isset($result[0]) ? $result[0] : null;
?>

<div class="container my-5">
    <?php if ($detail) : ?>
        <div class="card shadow-lg p-3">
            <div class="row g-0">
                <!-- Gambar (Di sebelah kiri) -->
                <div class="col-md-4 col-12">
                    <a href="<?= $detail->homepage ?>" target="_blank">
                        <img src="<?= $detail->image1 ?>" class="img-fluid rounded-start shadow-sm" alt="<?= $detail->name ?>" onerror="this.src='./assets/img/default-food.jpg';" style="object-fit: cover; height: 250px; width: 100%;">
                    </a>
                </div>

                <!-- Detail Makanan (Di sebelah kanan gambar) -->
                <div class="col-md-8 col-12">
                    <div class="card-body">
                        <h2 class="card-title text-success fw-bold"><?= $detail->name ?></h2>
                        <p class="card-text text-muted mb-4"><?= $detail->description ?></p>
                    </div>
                </div>
            </div>

            <!-- Data Nutrisi, Rating, Bahan, dan Video Tutorial -->
            <div class="card-body">
                <h5 class="fw-semibold">Informasi Nutrisi:</h5>
                <ul class="list-group list-group-flush mb-3">
                    <li class="list-group-item">Kalori: <span class="text-primary"><?= $detail->calories ?> </span></li>
                    <li class="list-group-item">Protein: <span class="text-primary"><?= $detail->protein ?> </span></li>
                    <li class="list-group-item">Lemak: <span class="text-primary"><?= $detail->fat ?> </span></li>
                    <li class="list-group-item">Karbohidrat: <span class="text-primary"><?= $detail->carbohydrates ?> </span></li>
                    <li class="list-group-item">Serat: <span class="text-primary"><?= $detail->fiber ?> </span></li>
                </ul>

                <!-- Rating -->
                <div class="mb-3">
                    <h6 class="fw-semibold">Rating:</h6>
                    <div class="text-warning">
                        <?php
                        $rating = isset($detail->rating) ? (float) $detail->rating->getValue() : 0;
                        $rating = max(0, min(5, $rating));
                        $roundedRating = (int) $rating;

                        for ($i = 0; $i < $roundedRating; $i++) {
                            echo '<i class="fas fa-star"></i>';
                        }
                        for ($i = 0; $i < (5 - $roundedRating); $i++) {
                            echo '<i class="far fa-star"></i>';
                        }
                        echo " <span class='text-muted'>(" . number_format($rating, 1) . ")</span>";
                        ?>
                    </div>
                </div>

                <!-- Bahan -->
                <div class="mb-4">
                    <h6 class="fw-semibold">Bahan:</h6>
                    <p><?= $detail->ingredient ?></p>
                </div>

                <!-- Video Tutorial -->
                <?php if (!empty($detail->video)) : ?>
                    <h5 class="fw-semibold">Video Tutorial:</h5>
                    <div class="ratio ratio-16x9">
                        <iframe src="<?= $detail->video ?>" title="Video Tutorial <?= $detail->name ?>" frameborder="0" allowfullscreen></iframe>
                    </div>
                <?php endif; ?>

                <!-- Tombol Kembali ke Menu -->
                <div class="mt-4">
                    <a href="?x=menu" class="btn btn-outline-success fw-semibold shadow-sm">
                        <i class="fas fa-arrow-left"></i> Back to Menu
                    </a>
                </div>
            </div>
        </div>
    <?php else : ?>
        <div class="alert alert-warning text-center">Data not found!</div>
        <div class="text-center mt-4">
            <a href="?x=menu" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Menu
            </a>
        </div>
    <?php endif; ?>
</div>

<div class="container my-5">
    <?php if ($location) : ?>
        <div class="card shadow-lg p-3">
            <div class="row g-0">
                <!-- Data peta -->
                <h5 class="fw-semibold">Lokasi di Peta:</h5>
                <div id="regionMap" style="height: 400px; width: 100%;"></div>
            </div>
        </div>
    <?php else : ?>
        <div class="alert alert-warning text-center">Lokasi tidak ditemukan untuk region ini!</div>
    <?php endif; ?>
</div>

<?php if ($location) : ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Cek dan atur nilai default jika tidak ada data lat dan long
            let lat = <?= isset($location->lat) && $location->lat ? $location->lat : '-6.750000' ?>; // Default: Jakarta
            let long = <?= isset($location->long) && $location->long ? $location->long : '107.500000' ?>; // Default: Jakarta

            // Inisialisasi peta
            let map = L.map('regionMap').setView([lat, long], 13); // Ganti dengan id 'regionMap'

            // Tambahkan layer peta dari OpenStreetMap
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            // Tambahkan marker di lokasi
            L.marker([lat, long]).addTo(map)
                .bindPopup('Lokasi di <b><?= addslashes($locationURI) ?></b>')
                .openPopup();
        });
    </script>
<?php endif; ?>