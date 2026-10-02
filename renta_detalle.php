<?php
try {
    require_once __DIR__ . '/conexion.php';

    // Obtener ID de la renta
    $rental_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    
    // Consulta para información de la renta
    $query_renta = "SELECT r.*, 
                           c.first_name as cliente_nombre, c.last_name as cliente_apellido, c.email,
                           f.title as pelicula, f.rental_rate,
                           s.first_name as staff_nombre, s.last_name as staff_apellido,
                           a.address, a.postal_code, city.city, country.country
                    FROM rental r
                    JOIN customer c ON r.customer_id = c.customer_id
                    JOIN staff s ON r.staff_id = s.staff_id
                    JOIN inventory i ON r.inventory_id = i.inventory_id
                    JOIN film f ON i.film_id = f.film_id
                    JOIN address a ON c.address_id = a.address_id
                    JOIN city ON a.city_id = city.city_id
                    JOIN country ON city.country_id = country.country_id
                    WHERE r.rental_id = ?";
    $stmt = $conn->prepare($query_renta);
    $stmt->bind_param("i", $rental_id);
    $stmt->execute();
    $renta = $stmt->get_result()->fetch_assoc();

    if (!$renta) {
        throw new Exception("Renta no encontrada");
    }

    // Configuración de paginación para pagos
    $pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
    $items_por_pagina = 5;
    $offset = ($pagina_actual - 1) * $items_por_pagina;

    // Consulta para historial de pagos
    $query_pagos = "SELECT p.* 
                   FROM payment p
                   WHERE p.rental_id = $rental_id
                   ORDER BY p.payment_date DESC
                   LIMIT $items_por_pagina OFFSET $offset";
    $result_pagos = $conn->query($query_pagos);
    
    // Total de pagos para paginación
    $total_pagos = $conn->query("SELECT COUNT(*) as total 
                                FROM payment 
                                WHERE rental_id = $rental_id")->fetch_assoc()['total'];
    $total_paginas = ceil($total_pagos / $items_por_pagina);

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
  <meta name="description" content="Detalle de Renta - Sakila">
  <meta name="author" content="Tu Nombre">
  <title>Detalle Renta | Sistema Sakila</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="shortcut icon" type="image/x-icon" href="images/favicon.jpg">
  <link rel="stylesheet" href="plugins/themefisher-font/style.css">
  <link rel="stylesheet" href="plugins/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="plugins/lightbox2/dist/css/lightbox.min.css">
  <link rel="stylesheet" href="plugins/slick-carousel/slick/slick.css">
  <link rel="stylesheet" href="plugins/slick-carousel/slick/slick-theme.css">
  <link rel="stylesheet" href="css/style.css">
  <style>
    .renta-header {
        background: linear-gradient(100deg, #fe2a77, #f9643d);
        color: white;
        padding: 30px;
        border-radius: 10px;
        margin-bottom: 30px;
    }
    .pelicula-avatar {
        width: 150px;
        height: 150px;
        margin: 0 auto 20px;
        border-radius: 10px;
        background: rgba(255,255,255,0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 40px;
        font-weight: bold;
    }
    .pago-card {
        margin-bottom: 20px;
        border: 1px solid #eee;
        padding: 20px;
        border-radius: 8px;
        transition: all 0.3s ease;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .pago-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.15);
    }
    .badge-estado {
        padding: 5px 12px;
        border-radius: 15px;
        font-size: 0.8em;
    }
    .badge-completado {
        background: #28a745;
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
        background: linear-gradient(100deg, #fe2a77, #f9643d);
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
    .detalle-item {
        padding: 15px;
        border-bottom: 1px solid #eee;
    }
    .detalle-item:last-child {
        border-bottom: none;
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
               <li class="nav-item">
                  <a class="nav-link" href="clientes.php">Clientes</a>
               </li>
               <li class="nav-item active">
                  <a class="nav-link" href="rentas.php">Rentas <span class="sr-only">(current)</span></a>
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
                    <!-- Header de la Renta -->
                    <div class="renta-header">
                        <div class="pelicula-avatar">
                            <?= substr($renta['pelicula'], 0, 2) ?>
                        </div>
                        <h1 class="text-center mb-3"><?= htmlspecialchars($renta['pelicula']) ?></h1>
                        <div class="text-center">
                            <span class="badge-estado badge-completado">
                                <?= $renta['return_date'] ? 'DEVUELTO' : 'PENDIENTE' ?>
                            </span>
                        </div>
                    </div>

                    <!-- Información detallada -->
                    <div class="info-box">
                        <h4 class="mb-4">Detalles de la Renta</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="detalle-item">
                                    <h5>Cliente</h5>
                                    <p><?= $renta['cliente_nombre'] ?> <?= $renta['cliente_apellido'] ?></p>
                                    <p><?= $renta['email'] ?></p>
                                </div>
                                <div class="detalle-item">
                                    <h5>Dirección</h5>
                                    <p><?= $renta['address'] ?></p>
                                    <p><?= $renta['city'] ?>, <?= $renta['country'] ?></p>
                                    <p>Código Postal: <?= $renta['postal_code'] ?></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="detalle-item">
                                    <h5>Fechas</h5>
                                    <p><strong>Renta:</strong> <?= date('d/m/Y H:i', strtotime($renta['rental_date'])) ?></p>
                                    <?php if($renta['return_date']): ?>
                                        <p><strong>Devolución:</strong> <?= date('d/m/Y H:i', strtotime($renta['return_date'])) ?></p>
                                    <?php endif; ?>
                                </div>
                                <div class="detalle-item">
                                    <h5>Staff</h5>
                                    <p><?= $renta['staff_nombre'] ?> <?= $renta['staff_apellido'] ?></p>
                                </div>
                                <div class="detalle-item">
                                    <h5>Tarifa</h5>
                                    <p>$<?= number_format($renta['rental_rate'], 2) ?> por día</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Historial de Pagos -->
                    <h3 class="mb-4">Historial de Pagos (<?= $total_pagos ?>)</h3>
                    <div class="row">
                        <?php if ($result_pagos && $result_pagos->num_rows > 0): ?>
                            <?php while ($pago = $result_pagos->fetch_assoc()): ?>
                                <div class="col-md-6">
                                    <div class="pago-card">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h5>Pago #<?= $pago['payment_id'] ?></h5>
                                            <span class="badge-estado badge-completado">
                                                Completado
                                            </span>
                                        </div>
                                        <p><strong>Monto:</strong> $<?= number_format($pago['amount'], 2) ?></p>
                                        <p><strong>Fecha:</strong> <?= date('d/m/Y H:i', strtotime($pago['payment_date'])) ?></p>
                                        <p><strong>Método:</strong> <?= $pago['payment_method'] ?? 'Tarjeta' ?></p>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="col-md-12">
                                <div class="alert alert-info">No se encontraron pagos registrados</div>
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
                            <a href="?id=<?= $rental_id ?>&pagina=<?= $pagina_actual - 1 ?>">&laquo; Anterior</a>
                        <?php endif; ?>

                        <!-- Botón de bloque anterior -->
                        <?php if($bloque_actual > 1): ?>
                            <a href="?id=<?= $rental_id ?>&pagina=<?= max(1, $pagina_inicio_bloque - 5) ?>">&lt;&lt;&lt;</a>
                        <?php endif; ?>

                        <!-- Números de página -->
                        <?php for ($i = $pagina_inicio_bloque; $i <= $pagina_fin_bloque; $i++): ?>
                            <a href="?id=<?= $rental_id ?>&pagina=<?= $i ?>" class="<?= $i == $pagina_actual ? 'active' : '' ?>">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>

                        <!-- Botón de bloque siguiente -->
                        <?php if($bloque_actual < ceil($total_paginas / 5)): ?>
                            <a href="?id=<?= $rental_id ?>&pagina=<?= min($total_paginas, $pagina_fin_bloque + 1) ?>">&gt;&gt;&gt;</a>
                        <?php endif; ?>

                        <!-- Botón Siguiente -->
                        <?php if($pagina_actual < $total_paginas): ?>
                            <a href="?id=<?= $rental_id ?>&pagina=<?= $pagina_actual + 1 ?>">Siguiente &raquo;</a>
                        <?php endif; ?>
                    </div>

                    <div class="text-center mt-4">
                        <a href="rentas.php" class="btn btn-main">Volver a Rentas</a>
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