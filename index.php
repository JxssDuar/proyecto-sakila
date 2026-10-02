<?php
// Configuración de conexión a la base de datos Sakila
$servername = "localhost";
$username = "charuser";
$password = "Usuariochar25@";
$dbname = "sakila";

try {
    // Crear la conexión
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Verificar la conexión
    if ($conn->connect_error) {
        throw new Exception("Conexión fallida: " . $conn->connect_error);
    }
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
  <meta name="description" content="Sistema de gestión Sakila">
  
  <meta name="author" content="Tu Nombre">

  <title>Sistema Sakila | Gestión de Películas</title>

  <!-- Mobile Specific Meta -->
  <meta name="viewport" content="width=device-width, initial-scale=1">
  
  <!-- Favicon -->
  <link rel="shortcut icon" type="image/x-icon" href="images/favicon.jpg" />
  
  <!-- CSS -->
  <!-- Themefisher Icon font -->
  <link rel="stylesheet" href="plugins/themefisher-font/style.css">
  <!-- bootstrap.min css -->
  <link rel="stylesheet" href="plugins/bootstrap/dist/css/bootstrap.min.css">
  <!-- Lightbox.min css -->
  <link rel="stylesheet" href="plugins/lightbox2/dist/css/lightbox.min.css">
  <!-- Slick Carousel -->
  <link rel="stylesheet" href="plugins/slick-carousel/slick/slick.css">
  <link rel="stylesheet" href="plugins/slick-carousel/slick/slick-theme.css">
  <!-- Main Stylesheet -->
  <link rel="stylesheet" href="css/style.css">
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
               <li class="nav-item active">
                  <a class="nav-link" href="index.php">Inicio <span class="sr-only">(current)</span></a>
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
<section class="hero-area">
	<div class="container">
		<div class="row">
		<div class="col-md-12">
    <div class="row">
        <div class="col-md-6">
            <div class="video-player">
                <img class="img-fluid rounded" src="images/pelicula.png" alt="Película">
                <a class="play-icon" href="javascript:void(0)">
                    <i class="tf-ion-play" data-video="https://www.youtube.com/embed/DyEKs7lc0kY?autoplay=1"></i>
                </a>
            </div>
        </div>
        <div class="col-md-6">
            <div class="block">
                <h2>Bienvenido al Sistema de Gestión Sakila</h2>
                <p>Sistema completo para administrar actores, películas, clientes y rentas de la base de datos Sakila.</p>

					
					<?php
					// Ejemplo de consulta a la base de datos
					$query = "SELECT COUNT(*) as total_peliculas FROM film";
					$result = $conn->query($query);
					if ($result && $row = $result->fetch_assoc()) {
						echo "<p>Total de películas en el sistema: <strong>" . $row['total_peliculas'] . "</strong></p>";
					}
					?>
					
					<ul class="list-inline wow fadeInUp" data-wow-duration=".5s" data-wow-delay=".7s">
						<li class="list-inline-item">
							<a href="peliculas.php" class="btn btn-main">Ver Películas</a>		
						</li>
					</ul>
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
