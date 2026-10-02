<?php
// Configuración de conexión a la base de datos Sakila
$servername = "localhost";
$username = "charuser";
$password = "Usuariochar25@";
$dbname = "sakila";

// Obtener el ID de la película desde la URL
$film_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

try {
    // Crear la conexión
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Verificar la conexión
    if ($conn->connect_error) {
        throw new Exception("Conexión fallida: " . $conn->connect_error);
    }

    // Consulta para obtener información de la película
    $query_pelicula = "SELECT title, description, release_year, rating, length, special_features 
                      FROM film WHERE film_id = ?";
    $stmt_pelicula = $conn->prepare($query_pelicula);
    $stmt_pelicula->bind_param("i", $film_id);
    $stmt_pelicula->execute();
    $result_pelicula = $stmt_pelicula->get_result();
    $pelicula = $result_pelicula->fetch_assoc();

    if (!$pelicula) {
        throw new Exception("Película no encontrada");
    }

    // Consulta para obtener los actores de la película
    $query_actores = "SELECT a.actor_id, a.first_name, a.last_name 
                     FROM film_actor fa
                     JOIN actor a ON fa.actor_id = a.actor_id
                     WHERE fa.film_id = ?
                     ORDER BY a.last_name, a.first_name";
    $stmt_actores = $conn->prepare($query_actores);
    $stmt_actores->bind_param("i", $film_id);
    $stmt_actores->execute();
    $actores = $stmt_actores->get_result();

    // Consulta para obtener las categorías de la película
    $query_categorias = "SELECT c.name 
                        FROM film_category fc
                        JOIN category c ON fc.category_id = c.category_id
                        WHERE fc.film_id = ?";
    $stmt_categorias = $conn->prepare($query_categorias);
    $stmt_categorias->bind_param("i", $film_id);
    $stmt_categorias->execute();
    $categorias = $stmt_categorias->get_result();

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
  <meta name="description" content="Detalle de Película - Sakila">
  
  <meta name="author" content="Tu Nombre">

  <title><?php echo htmlspecialchars($pelicula['title']); ?> | Sistema Sakila</title>

  <!-- Mobile Specific Meta -->
  <meta name="viewport" content="width=device-width, initial-scale=1">
  
  <!-- Favicon -->
  <link rel="shortcut icon" type="image/x-icon" href="images/favicon.jpg" />
  
  <!-- CSS -->
  <link rel="stylesheet" href="plugins/themefisher-font/style.css">
  <link rel="stylesheet" href="plugins/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="plugins/lightbox2/dist/css/lightbox.min.css">
  <link rel="stylesheet" href="plugins/slick-carousel/slick/slick.css">
  <link rel="stylesheet" href="plugins/slick-carousel/slick/slick-theme.css">
  <link rel="stylesheet" href="css/style.css">
  
  <style>
    .pelicula-header {
      background-color: #f8f9fa;
      padding: 30px;
      margin-bottom: 30px;
      border-radius: 5px;
    }
    .pelicula-info {
      margin-bottom: 20px;
    }
    .info-label {
      font-weight: bold;
      min-width: 150px;
      display: inline-block;
    }
    .actor-card {
      margin-bottom: 15px;
      padding: 10px;
      border: 1px solid #eee;
      border-radius: 5px;
    }
    .categoria-badge {
      margin-right: 5px;
      margin-bottom: 5px;
    }
    .btn-volver {
      margin-top: 30px;
    }
    .feature-list {
      list-style-type: none;
      padding-left: 0;
    }
    .feature-list li:before {
      content: "✓ ";
      color: #28a745;
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
               <li class="nav-item active">
                  <a class="nav-link" href="peliculas.php">Películas <span class="sr-only">(current)</span></a>
               </li>
               <li class="nav-item">
                  <a class="nav-link" href="clientes.php">Clientes</a>
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
          <!-- Cabecera de la película -->
          <div class="pelicula-header">
            <h1><?php echo htmlspecialchars($pelicula['title']); ?></h1>
            <p class="lead"><?php echo htmlspecialchars($pelicula['description']); ?></p>
          </div>
          
          <!-- Información detallada -->
          <div class="row">
            <div class="col-md-6">
              <div class="pelicula-info">
                <h3>Información de la Película</h3>
                <p><span class="info-label">Año de lanzamiento:</span> <?php echo htmlspecialchars($pelicula['release_year']); ?></p>
                <p><span class="info-label">Clasificación:</span> <?php echo htmlspecialchars($pelicula['rating']); ?></p>
                <p><span class="info-label">Duración:</span> <?php echo htmlspecialchars($pelicula['length']); ?> minutos</p>
                
                <p><span class="info-label">Categorías:</span> 
                  <?php if ($categorias && $categorias->num_rows > 0): ?>
                    <?php while ($categoria = $categorias->fetch_assoc()): ?>
                      <span class="badge badge-primary categoria-badge"><?php echo htmlspecialchars($categoria['name']); ?></span>
                    <?php endwhile; ?>
                  <?php else: ?>
                    No especificadas
                  <?php endif; ?>
                </p>
                
                <p><span class="info-label">Características especiales:</span></p>
                <ul class="feature-list">
                  <?php 
                  $features = explode(',', $pelicula['special_features']);
                  foreach ($features as $feature): 
                  ?>
                    <li><?php echo htmlspecialchars(trim($feature)); ?></li>
                  <?php endforeach; ?>
                </ul>
              </div>
            </div>
            
            <div class="col-md-6">
              <div class="pelicula-info">
                <h3>Reparto</h3>
                <?php if ($actores && $actores->num_rows > 0): ?>
                  <div class="row">
                    <?php while ($actor = $actores->fetch_assoc()): ?>
                      <div class="col-md-6">
                        <div class="actor-card">
                          <h5><?php echo htmlspecialchars($actor['first_name'].' '.$actor['last_name']); ?></h5>
                          <a href="actor_detalle.php?id=<?php echo $actor['actor_id']; ?>" class="btn btn-sm btn-main">
                            Ver filmografía
                          </a>
                        </div>
                      </div>
                    <?php endwhile; ?>
                  </div>
                <?php else: ?>
                  <p>No hay información del reparto disponible.</p>
                <?php endif; ?>
              </div>
            </div>
          </div>
          
          <!-- Botón de volver -->
          <div class="text-center btn-volver">
            <a href="peliculas.php" class="btn btn-main">Volver al listado de películas</a>
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

</body>
</html>
<?php
// Cerrar conexión
$conn->close();
?>