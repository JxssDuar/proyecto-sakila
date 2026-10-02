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

    // Obtener ID del cliente
    $cliente_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    
    // Consulta para información del cliente
    $stmt = $conn->prepare("SELECT c.*, a.address, a.postal_code, a.phone, 
                                  city.city, country.country
                           FROM customer c
                           JOIN address a ON c.address_id = a.address_id
                           JOIN city ON a.city_id = city.city_id
                           JOIN country ON city.country_id = country.country_id
                           WHERE c.customer_id = ?");
    $stmt->bind_param("i", $cliente_id);
    $stmt->execute();
    $cliente = $stmt->get_result()->fetch_assoc();

    if (!$cliente) {
        throw new Exception("Cliente no encontrado");
    }

    // Configuración de paginación para el historial
    $pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
    $items_por_pagina = 5;
    $offset = ($pagina_actual - 1) * $items_por_pagina;

    // Consulta para historial de rentas
    $query_rentas = "SELECT r.rental_id, r.rental_date, r.return_date,
                            f.title as pelicula, f.rental_rate,
                            s.first_name as staff_nombre, s.last_name as staff_apellido
                     FROM rental r
                     JOIN inventory i ON r.inventory_id = i.inventory_id
                     JOIN film f ON i.film_id = f.film_id
                     JOIN staff s ON r.staff_id = s.staff_id
                     WHERE r.customer_id = $cliente_id
                     ORDER BY r.rental_date DESC
                     LIMIT $items_por_pagina OFFSET $offset";
    $result_rentas = $conn->query($query_rentas);
    
    // Total de rentas para paginación
    $total_rentas = $conn->query("SELECT COUNT(*) as total 
                                 FROM rental 
                                 WHERE customer_id = $cliente_id")->fetch_assoc()['total'];
    $total_paginas = ceil($total_rentas / $items_por_pagina);

} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> 
<html class="no-js"> <!--<![endif]-->
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta name="description" content="Detalle de Cliente - Sakila">
  <meta name="author" content="Tu Nombre">
  <title>Detalle Cliente | Sistema Sakila</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="shortcut icon" type="image/x-icon" href="images/favicon.jpg">
  <link rel="stylesheet" href="plugins/themefisher-font/style.css">
  <link rel="stylesheet" href="plugins/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="plugins/lightbox2/dist/css/lightbox.min.css">
  <link rel="stylesheet" href="plugins/slick-carousel/slick/slick.css">
  <link rel="stylesheet" href="plugins/slick-carousel/slick/slick-theme.css">
  <link rel="stylesheet" href="css/style.css">
  <style>
    .cliente-header {
        background: linear-gradient(100deg, #f9643d, #fe2a77);
        color: white;
        padding: 30px;
        border-radius: 10px;
        margin-bottom: 30px;
    }
    .cliente-avatar {
        width: 120px;
        height: 120px;
        margin: 0 auto 20px;
        border-radius: 50%;
        background: rgba(255,255,255,0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 40px;
        font-weight: bold;
    }
    .renta-card {
        margin-bottom: 20px;
        border: 1px solid #eee;
        padding: 20px;
        border-radius: 8px;
        transition: all 0.3s ease;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .renta-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.15);
    }
    .badge-estado {
        padding: 5px 12px;
        border-radius: 15px;
        font-size: 0.8em;
    }
    .badge-devuelto {
        background: #28a745;
        color: white;
    }
    .badge-pendiente {
        background: #dc3545;
        color: white;
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
        background: linear-gradient(100deg, #f9643d, #fe2a77);
        color: white;
        border-color: transparent;
    }
    .pagination a:hover:not(.active) {
        background: #f8f9fa;
    }
    .info-box {
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        margin-bottom: 30px;
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
                    <!-- Header del Cliente -->
                    <div class="cliente-header">
                        <div class="cliente-avatar">
                            <?= substr($cliente['first_name'], 0, 1) . substr($cliente['last_name'], 0, 1) ?>
                        </div>
                        <h1 class="text-center mb-3"><?= $cliente['first_name'] . ' ' . $cliente['last_name'] ?></h1>
                        <div class="text-center">
                            <p class="mb-1"><?= $cliente['email'] ?></p>
                            <p class="mb-1"><?= $cliente['phone'] ?></p>
                        </div>
                    </div>

                    <!-- Información detallada -->
                    <div class="info-box">
                        <h4 class="mb-4">Información de Contacto</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Dirección:</strong> <?= $cliente['address'] ?></p>
                                <p><strong>Código Postal:</strong> <?= $cliente['postal_code'] ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Ciudad:</strong> <?= $cliente['city'] ?></p>
                                <p><strong>País:</strong> <?= $cliente['country'] ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Historial de Rentas -->
                    <h3 class="mb-4">Historial de Rentas (<?= $total_rentas ?>)</h3>
                    <div class="row">
                        <?php if ($result_rentas && $result_rentas->num_rows > 0): ?>
                            <?php while ($renta = $result_rentas->fetch_assoc()): ?>
                                <div class="col-md-6">
                                    <div class="renta-card">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h5><?= $renta['pelicula'] ?></h5>
                                            <span class="badge-estado <?= $renta['return_date'] ? 'badge-devuelto' : 'badge-pendiente' ?>">
                                                <?= $renta['return_date'] ? 'Devuelto' : 'Pendiente' ?>
                                            </span>
                                        </div>
                                        <p><strong>Fecha Renta:</strong> <?= date('d/m/Y H:i', strtotime($renta['rental_date'])) ?></p>
                                        <?php if($renta['return_date']): ?>
                                            <p><strong>Fecha Devolución:</strong> <?= date('d/m/Y H:i', strtotime($renta['return_date'])) ?></p>
                                        <?php endif; ?>
                                        <p><strong>Atendió:</strong> <?= $renta['staff_nombre'] ?> <?= $renta['staff_apellido'] ?></p>
                                        <p><strong>Tarifa:</strong> $<?= number_format($renta['rental_rate'], 2) ?></p>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="col-md-12">
                                <div class="alert alert-info">Este cliente no tiene rentas registradas</div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Paginación -->
                    <div class="pagination">
                        <?php
                        $bloque_actual = ceil($pagina_actual / 5);
                        $pagina_inicio_bloque = (($bloque_actual - 1) * 5) + 1;
                        $pagina_fin_bloque = min($bloque_actual * 5, $total_paginas);
                        
                        // Botón Anterior
                        if($pagina_actual > 1): ?>
                            <a href="?id=<?= $cliente_id ?>&pagina=<?= $pagina_actual - 1 ?>">&laquo; Anterior</a>
                        <?php endif; ?>

                        <!-- Botón de bloque anterior -->
                        <?php if($bloque_actual > 1): ?>
                            <a href="?id=<?= $cliente_id ?>&pagina=<?= max(1, $pagina_inicio_bloque - 5) ?>">&lt;&lt;&lt;</a>
                        <?php endif; ?>

                        <!-- Números de página -->
                        <?php for ($i = $pagina_inicio_bloque; $i <= $pagina_fin_bloque; $i++): ?>
                            <a href="?id=<?= $cliente_id ?>&pagina=<?= $i ?>" class="<?= $i == $pagina_actual ? 'active' : '' ?>">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>

                        <!-- Botón de bloque siguiente -->
                        <?php if($bloque_actual < ceil($total_paginas / 5)): ?>
                            <a href="?id=<?= $cliente_id ?>&pagina=<?= min($total_paginas, $pagina_fin_bloque + 1) ?>">&gt;&gt;&gt;</a>
                        <?php endif; ?>

                        <!-- Botón Siguiente -->
                        <?php if($pagina_actual < $total_paginas): ?>
                            <a href="?id=<?= $cliente_id ?>&pagina=<?= $pagina_actual + 1 ?>">Siguiente &raquo;</a>
                        <?php endif; ?>
                    </div>

                    <div class="text-center mt-4">
                        <a href="clientes.php" class="btn btn-main">Volver a Clientes</a>
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