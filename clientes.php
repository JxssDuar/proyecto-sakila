<?php
// Configuración de conexión a la base de datos Sakila
$servername = "localhost";
$username = "charuser";
$password = "Usuariochar25@";
$dbname = "sakila";

try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        throw new Exception("Conexión fallida: " . $conn->connect_error);
    }
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}

// Configuración de paginación
$pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$items_por_pagina = 15;
$offset = ($pagina_actual - 1) * $items_por_pagina;

// Obtener total de clientes
$total_clientes = $conn->query("SELECT COUNT(*) as total FROM customer")->fetch_assoc()['total'];
$total_paginas = ceil($total_clientes / $items_por_pagina);

// Consulta para obtener clientes con dirección
$query_clientes = "SELECT c.customer_id, c.first_name, c.last_name, c.email, 
                          a.address, a.postal_code, a.phone, city.city, country.country
                   FROM customer c
                   JOIN address a ON c.address_id = a.address_id
                   JOIN city ON a.city_id = city.city_id
                   JOIN country ON city.country_id = country.country_id
                   ORDER BY c.last_name, c.first_name
                   LIMIT $items_por_pagina OFFSET $offset";
$result_clientes = $conn->query($query_clientes);
?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> 
<html class="no-js"> <!--<![endif]-->
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta name="description" content="Gestión de Clientes - Sakila">
  <meta name="author" content="Tu Nombre">
  <title>Clientes | Sistema Sakila</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="shortcut icon" type="image/x-icon" href="images/favicon.jpg">
  <link rel="stylesheet" href="plugins/themefisher-font/style.css">
  <link rel="stylesheet" href="plugins/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="plugins/lightbox2/dist/css/lightbox.min.css">
  <link rel="stylesheet" href="plugins/slick-carousel/slick/slick.css">
  <link rel="stylesheet" href="plugins/slick-carousel/slick/slick-theme.css">
  <link rel="stylesheet" href="css/style.css">
  <style>
    .cliente-card {
        margin-bottom: 30px;
        border: 1px solid #eee;
        padding: 25px;
        border-radius: 8px;
        transition: all 0.3s ease;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        background: white;
        height: 100%;
    }
    .cliente-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.15);
        border-color: #fe2a77;
    }
    .cliente-avatar {
        width: 80px;
        height: 80px;
        margin: 0 auto 15px;
        border-radius: 50%;
        background: linear-gradient(100deg, #fe2a77, #f9643d);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
        font-weight: bold;
    }
    .cliente-info {
        margin-top: 15px;
    }
    .cliente-info p {
        margin-bottom: 5px;
        font-size: 0.9em;
    }
    .btn-detalle {
        margin-top: 15px;
        background: linear-gradient(100deg, #fe2a77, #f9643d);
        border: none;
        padding: 8px 15px;
        border-radius: 4px;
        color: white;
        transition: all 0.3s;
        display: inline-block;
    }
    .btn-detalle:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 10px rgba(254, 42, 119, 0.3);
        text-decoration: none;
    }
    .pagination {
        margin-top: 30px;
        justify-content: center;
        gap: 5px;
        flex-wrap: wrap;
    }
    .pagination a {
        padding: 8px 12px;
        border: 1px solid #ddd;
        border-radius: 4px;
        text-decoration: none;
        min-width: 40px;
        text-align: center;
        transition: all 0.3s;
    }
    .pagination a.active {
        background: linear-gradient(100deg, #fe2a77, #f9643d);
        color: white;
        border-color: transparent;
    }
    .pagination a:hover:not(.active) {
        background: #f8f9fa;
    }
  </style>
</head>

<body id="body">
 <!-- Preloader -->
  <div id="preloader">
    <div class="preloader">
      <div class="sk-circle1 sk-child"></div>
      <div class="sk-circle2 sk-child"></div>
      <div class="sk-circle3 sk-child"></div>
      <div class="sk-circle4 sk-child"></div>
      <div class="sk-circle5 sk-child"></div>
      <div class="sk-circle6 sk-child"></div>
      <div class="sk-circle7 sk-child"></div>
      <div class="sk-circle8 sk-child"></div>
      <div class="sk-circle9 sk-child"></div>
      <div class="sk-circle10 sk-child"></div>
      <div class="sk-circle11 sk-child"></div>
      <div class="sk-circle12 sk-child"></div>
    </div>
  </div> 

<!-- Fixed Navigation -->
<section class="header navigation">
   <div class="container">
      <div class="row">
         <div class="col-md-12">
            <nav class="navbar navbar-expand-md">
               <a class="navbar-brand" href="index.php">
                  <img src="images/logo.png" alt="logo">
               </a>  
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
               <span class="tf-ion-android-menu"></span>
            </button>
         <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ml-auto">
               <li class="nav-item">
                  <a class="nav-link" href="index.php">Inicio</a>
               </li>
               <li class="nav-item">
                  <a class="nav-link" href="actores.php">Actores</a>
               </li>
               <li class="nav-item">
                  <a class="nav-link" href="peliculas.php">Películas</a>
               </li>
               <li class="nav-item active">
                  <a class="nav-link" href="clientes.php">Clientes <span class="sr-only">(current)</span></a>
               </li>
               <li class="nav-item">
                  <a class="nav-link" href="rentas.php">Rentas</a>
               </li>
            </ul>
         </div>
      </nav>
   </div>
</div>
</div>
</section>

 <!-- Contenido Principal -->
<section class="section">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="block">
                    <div class="text-center mb-5">
                        <h2>Listado de Clientes</h2>
                        <p class="text-muted">Mostrando <?= $items_por_pagina ?> clientes por página</p>
                    </div>
                    
                    <div class="row">
                        <?php if ($result_clientes && $result_clientes->num_rows > 0): ?>
                            <?php while ($cliente = $result_clientes->fetch_assoc()): ?>
                                <div class="col-lg-4 col-md-6 mb-4">
                                    <div class="cliente-card">
                                        <div class="cliente-avatar">
                                            <?= substr($cliente['first_name'], 0, 1) . substr($cliente['last_name'], 0, 1) ?>
                                        </div>
                                        <h4 class="text-center"><?= $cliente['first_name'] . ' ' . $cliente['last_name'] ?></h4>
                                        <div class="cliente-info">
                                            <p class="text-center">
                                                <i class="tf-ion-email"></i> <?= $cliente['email'] ?>
                                            </p>
                                            <p class="text-center">
                                                <i class="tf-ion-ios-telephone"></i> <?= $cliente['phone'] ?>
                                            </p>
                                            <p class="text-center">
                                                <?= $cliente['address'] ?><br>
                                                <?= $cliente['postal_code'] ?> - <?= $cliente['city'] ?><br>
                                                <?= $cliente['country'] ?>
                                            </p>
                                        </div>
                                        <div class="text-center">
                                            <a href="cliente_detalle.php?id=<?= $cliente['customer_id'] ?>" class="btn-detalle">
                                                Ver Historial
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="col-md-12">
                                <div class="alert alert-info">No se encontraron clientes</div>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Paginación -->
                    <div class="pagination">
                        <?php
                        $bloque_actual = ceil($pagina_actual / 10);
                        $pagina_inicio_bloque = (($bloque_actual - 1) * 10) + 1;
                        $pagina_fin_bloque = min($bloque_actual * 10, $total_paginas);
                        
                        // Botón Anterior
                        if($pagina_actual > 1): ?>
                            <a href="?pagina=<?= $pagina_actual - 1 ?>">&laquo; Anterior</a>
                        <?php endif; ?>

                        <!-- Botón de bloque anterior -->
                        <?php if($bloque_actual > 1): ?>
                            <a href="?pagina=<?= max(1, $pagina_inicio_bloque - 10) ?>">&lt;&lt;&lt;</a>
                        <?php endif; ?>

                        <!-- Números de página -->
                        <?php for ($i = $pagina_inicio_bloque; $i <= $pagina_fin_bloque; $i++): ?>
                            <a href="?pagina=<?= $i ?>" class="<?= $i == $pagina_actual ? 'active' : '' ?>">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>

                        <!-- Botón de bloque siguiente -->
                        <?php if($bloque_actual < ceil($total_paginas / 10)): ?>
                            <a href="?pagina=<?= min($total_paginas, $pagina_fin_bloque + 1) ?>">&gt;&gt;&gt;</a>
                        <?php endif; ?>

                        <!-- Botón Siguiente -->
                        <?php if($pagina_actual < $total_paginas): ?>
                            <a href="?pagina=<?= $pagina_actual + 1 ?>">Siguiente &raquo;</a>
                        <?php endif; ?>
                    </div>

                    <div class="text-center mt-4">
                        <a href="index.php" class="btn btn-main">Volver al Inicio</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<footer id="footer" class="bg-one">
  <div class="footer-bottom">
    <h5>Copyright <?php echo date('Y'); ?>. Todos los derechos reservados.</h5>
    <h6>Sakila Studios</h6>
  </div>
</footer>

    <!-- Essential Scripts -->
    <script src="plugins/jquery/dist/jquery.min.js"></script>
    <script src="plugins/bootstrap/dist/js/popper.min.js"></script>
    <script src="plugins/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="plugins/parallax/jquery.parallax-1.1.3.js"></script>
    <script src="plugins/lightbox2/dist/js/lightbox.min.js"></script>
    <script src="plugins/slick-carousel/slick/slick.min.js"></script>
    <script src="plugins/mixitup/dist/mixitup.min.js"></script>
    <script src="plugins/smooth-scroll/dist/js/smooth-scroll.min.js"></script>
    <script src="js/script.js"></script>

<?php
// Cerrar conexión
$conn->close();
?>
</body>
</html>