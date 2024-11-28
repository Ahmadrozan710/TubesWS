<?php

if ($_GET) {
    // Cek apakah ada parameter 'search'
    if (isset($_GET['search']) && $_GET['search'] !== '') {
        // Menangani hasil pencarian
        include "./Main/search.php";  // Halaman search.php untuk menampilkan hasil pencarian
    } else {
        // Cek apakah parameter 'x' ada
        if (isset($_GET['x'])) {
            switch ($_GET['x']) {
                case "menu":
                    include "./Main/list.php";
                    break;
                case "aboutus":
                    include "./Main/aboutus.php";
                    break;
                case "detail":
                    include "./Main/detail.php";
                    break;
                default:
                    echo "
                        <div class='not-found'>404 Page Not Found</div>
                    ";
                    break;
            }
        } else {
            // Jika tidak ada parameter 'x', misalnya, tampilkan halaman utama
            include "./Main/home.php";
        }
    }
} else {
    // Jika $_GET kosong, tampilkan halaman utama
    include "./Main/home.php";
}
?>
