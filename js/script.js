(function ($) { 
    "use strict"; // Habilita el modo estricto de JavaScript
	
	/* ========================================================================= */
	/*	Precargador de Página - Animación de carga inicial                       */
	/* ========================================================================= */
	$(window).on("load",function(){
		// Oculta gradualmente el preloader y lo elimina del DOM
		$('#preloader').fadeOut('slow',function(){
			$(this).remove();
		});
	});

	/* ========================================================================= */
	/*	Reproductor de Video Embebido - Conversión de icono a iframe              */
	/* ========================================================================= */
	$('.play-icon i').click(function() {
		// Crea un iframe con el video usando la URL del atributo data-video
		var video = '<iframe allowfullscreen src="' + $(this).attr('data-video') + '"></iframe>';
		// Reemplaza el icono por el reproductor de video
		$(this).replaceWith(video);
	});

	/* ========================================================================= */
	/*	Carrusel de Galería de la Compañía - Configuración Slick Slider           */
	/* ========================================================================= */
	$('.company-gallery').slick({
		infinite: true,     // Carrusel infinito
		arrows: false,      // Oculta flechas de navegación
		autoplay: true,     // Reproducción automática
  		autoplaySpeed: 2000,// Velocidad de autoplay (2 segundos)
  		slidesToShow: 5,    // Muestra 5 elementos simultáneamente
  		slidesToScroll: 1,  // Desplazamiento de 1 slide por vez
	});
	
	/* ========================================================================= */
	/*	Contador Animado - Efecto de incremento numérico                         */
	/* ========================================================================= */
	$('.counter').each(function() {
		var $this = $(this),
			countTo = $this.attr('data-count'); // Obtiene valor final del atributo
	  
		// Animación del número usando jQuery.animate()
		$({ countNum: $this.text()}).animate({
			countNum: countTo
		}, {
			duration: 1500,    // Duración de 1.5 segundos
			easing: 'linear',  // Animación lineal
			step: function() { // Actualización por frame
				$this.text(Math.floor(this.countNum));
			},
			complete: function() { // Callback final
				$this.text(this.countNum);
			}
		});  
	});

	/* ========================================================================= */
	/*	Scroll Suave - Navegación fluida a anclas                                */
	/* ========================================================================= */
	var scroll = new SmoothScroll('a[href*="#"]'); // Inicializa scroll suave

	/* ========================================================================= */
	/*	Header Dinámico - Efecto sticky al hacer scroll                          */
	/* ========================================================================= */	
	$(window).scroll(function() {    
		var scroll = $(window).scrollTop(); // Obtiene posición del scroll
		
		// Añade/quita clase CSS según posición
		if (scroll > 200) {
			$(".navigation").addClass("sticky-header");
		} else {
			$(".navigation").removeClass("sticky-header");
		}
	});

})(jQuery); // Fin de la función IIFE que encapsula todo el código


                            