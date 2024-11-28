<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm py-3">
  <div class="container">
    <!-- Logo -->
    <a class="navbar-brand fw-bold text-success" href=".layout/.." <?php if (isset($_GET['x']) && $_GET['x'] == 'home')  ?> style="font-family: 'Poppins', sans-serif; font-size: 1.5rem;">
      <img src="./assets/img/logo.png" alt="Logo" width="40" class="me-2">Foodies
    </a>
    
    <!-- Toggler Button -->
    <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Navbar Links -->
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto align-items-center">
        <li class="nav-item">
          <a class="nav-link" href=".layout/.." <?php if (isset($_GET['x']) && $_GET['x'] == 'home')  ?>>Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="?x=menu" <?php if (isset($_GET['x']) && $_GET['x'] == 'menu')  ?>>Menu</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="?x=aboutus" <?php if (isset($_GET['x']) && $_GET['x'] == 'aboutus')  ?>>About</a>
        </li>
      </ul>

      <form class="d-flex ms-lg-4 mt-3 mt-lg-0" method="GET">
    <input class="form-control me-2 border-2 border-success rounded-pill px-3" 
           type="search" 
           name="search" 
           placeholder="Search food..." 
           aria-label="Search" 
           value="<?php echo isset($_GET['search']) ? trim($_GET['search']) : ''; ?>" 
           style="font-size: 0.9rem; box-shadow: none;">
    <button class="btn btn-success text-white rounded-pill px-4" 
            type="submit" 
            style="font-size: 0.9rem; background: linear-gradient(90deg, #16a085, #2ecc71); border: none;">
        Search
    </button>
</form>


</form>


    </div>
  </div>
</nav>
