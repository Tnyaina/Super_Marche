<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>Supermarché - Caisse</title>

  <!-- Bootstrap core CSS -->
  <link href="<?php echo BASE_URL; ?>/assets/css/bootstrap.min.css" rel="stylesheet">

  <!-- Custom styles for this template -->
  <link href="<?php echo BASE_URL; ?>/assets/css/shop-homepage.css" rel="stylesheet">
</head>

<body>
  <!-- Navigation -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container">
      <a class="navbar-brand" href="#">TD - SI-IHM - ETU003080 - ETU003185</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarResponsive">
        <ul class="navbar-nav ml-auto">
          <?php if (isset($_SESSION['selected_caisse_numero'])): ?>
            <li class="nav-item active">
              <a class="nav-link" href="#">Caisse: <?php echo htmlspecialchars($_SESSION['selected_caisse_numero']); ?></a>
            </li>
          <?php endif; ?>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo BASE_URL; ?>/logout">Déconnexion</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Page Content -->
  <div class="container">
    <?php
    // Display any session messages
    if (isset($_SESSION['error'])): ?>
      <div class="alert alert-danger mt-3">
        <?php
        echo htmlspecialchars($_SESSION['error']);
        unset($_SESSION['error']);
        ?>
      </div>
    <?php endif; ?>

    <?php
    if (!empty($pageContent)) {
      echo $pageContent;
    }
    ?>
  </div>
  <!-- /.container -->

  <!-- Footer -->
  <footer class="py-5 bg-dark fixed-bottom">
    <div class="container">
      <p class="m-0 text-center text-white">Copyright &copy; Supermarché 2025</p>
    </div>
  </footer>

  <!-- Bootstrap core JavaScript -->
  <script src="<?php echo BASE_URL; ?>/assets/jquery/jquery.min.js"></script>
  <script src="<?php echo BASE_URL; ?>/assets/js/bootstrap.bundle.min.js"></script>
</body>

</html>