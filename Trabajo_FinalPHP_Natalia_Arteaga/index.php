<?php 
# Ver si hay una sesión activa - el usuario o el admin van a poder ver esta página entre otras
if (session_status() == PHP_SESSION_NONE) {
   session_start();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>VitalCare Medical Centre</title>
   <link rel="stylesheet" type="text/css" href="./assets/css/estilos_index.css">
   <link rel="stylesheet" type="text/css" href="./assets/css/navbar.css">
   <script src="./assets/scripts/js_index.js"></script>
   <link rel="preconnect" href="https://fonts.googleapis.com">
   <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
   <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;700&display=swap" rel="stylesheet">
</head>

<body>
   <!-- Incluyo la barra de navegación -->
   <?php include_once('views/navbar.php'); ?>
   <main>
      <!-- Imagen principal-->
      <div class="imagen-principal">
         <img src="./assets/images/index/principal.jpg" alt="Imagen Principal" width="1800" height="572">
      </div>
   </main>
   <section class="vital-care">
      <!-- Primera sección index (descripción)-->
      <div class="vital-care">
         <h1>VitalCare Medical Center</h1>
         <p>Los mejores profesionales, la tecnología más avanzada, investigación, formación y un
            modelo común de gestión aseguran el compromiso del grupo por la calidad de nuestros servicios para todos los
            ciudadanos. </p>
         <p>VitalCare cubre todas las especialidades médicas, y destaca, con reconocido prestigio, en el diagnóstico y
            tratamiento de patología cardiovascular y oncológica.</p>
      </div>
   </section>
   <!-- Segunda sección donde agregaremos más información (background color: gris para separar secciones)-->
   <section class="sobre-nosotros">
      <h1>Sobre nosotros</h1>
      <p>
         VitalCare es un destacado centro de salud que se distingue por su excelencia en el cuidado de la salud
         cardiovascular y oncológica. Nuestro equipo está compuesto por profesionales altamente cualificados que
         utilizan tecnología de punta para ofrecer diagnósticos precisos y tratamientos efectivos. </p>
      <p>
         Además, nos comprometemos a proporcionar servicios de alta calidad a todos nuestros pacientes, respaldados por
         un enfoque en la investigación y la formación continua. Con una amplia gama de especialidades médicas, estamos
         aquí para atender las necesidades de salud de la comunidad con dedicación y experiencia.
      </p>
   </section>
   <!-- Tercera sección donde agregaremos los especialistas en círculo con su nombre y apellidos, etc (background color: blanco ahora para separar secciones) de dos en dos -->
   <section class="seccion3-equipo">
      <h1>Nuestro Equipo</h1>
      <div class="nuestro-equipo">
         <div class="fila">
            <div class="miembro">
               <div class="circulo">
                  <img src="./assets/images/index/doctor1.jpg" alt="Dr. María González" height="350" width="350">
               </div>
               <h3>Dr. María González</h3>
               <p>Oncóloga</p>
               <p>Especializada en el tratamiento de pacientes con cáncer y terapias innovadoras</p>
            </div>
            <div class="miembro">
               <div class="circulo">
                  <img src="./assets/images/index/doctor2.jpg" alt="Dr. Juan Pérez" height="350" width="350"> 
               </div>
               <h3>Dr. Juan Pérez</h3>
               <p>Cardiólogo</p>
               <p>Con más de 10 años de experiencia en el diagnóstico y tratamiento de enfermedades cardiovasculares</p>
            </div>
         </div>
         <div class="fila">
            <div class="miembro">
               <div class="circulo">
                  <img src="./assets/images/index/enfermera2.jpg" alt="Enfermera Laura Sánchez" height="350" width="350">
               </div>
               <h3>Enfermera Laura Sánchez</h3>
               <p>Cuidados Intensivos</p>
               <p>Con experiencia en cuidados críticos y soporte vital avanzado</p>
            </div>
            <div class="miembro">
               <div class="circulo">
                  <img src="./assets/images/index/enfermero.jpg" alt="Enfermero Carlos Martínez" height="350" width="350">
               </div>
               <h3>Enfermero Carlos Martínez</h3>
               <p>Geriatría</p>
               <p>Especializado en el cuidado y bienestar de pacientes de edad avanzada</p>
            </div>
         </div>
      </div>
   </section>
   <!-- Cuarta sección reseñas - background color: gris para separar secciones - agregar un carrusel con un minimo de 4 reseñas -->
   <section class="seccion4-resenas">
      <div class="container">
          <div class="clients">
              <h2>Nuestras reseñas</h2>
              <p>Descubre lo que dicen nuestros pacientes sobre su experiencia en nuestra clínica médica. Sus opiniones son nuestra mayor satisfacción y nos motivan a seguir brindando el mejor cuidado médico posible.</p>
              <a href="#!" class="btn btn-primary rounded-pill">Más Testimonios</a>
          </div>
          <div class="testimonials">
            <div class="testimonial">
                <img src="./assets/images/index/3.jpg" alt="Marta García" height="300" width="300">
                <blockquote>Excelente atención médica. El personal es muy profesional y amable. Siempre me siento bienvenido y bien cuidado en esta clínica. ¡Altamente recomendado!</blockquote>
                <h4>Marta García</h4>
                <h5>Paciente satisfecha</h5>
            </div>
            <div class="testimonial">
                <img src="./assets/images/index/1.jpg" alt="José Martínez" height="300" width="300">
                <blockquote>Desde que comencé a venir a esta clínica, mi salud ha mejorado significativamente. Los médicos son muy conocedores y se preocupan genuinamente por el bienestar de sus pacientes.</blockquote>
                <h4>José Martínez</h4>
                <h5>Paciente regular</h5>
            </div>
            <div class="testimonial">
                <img src="./assets/images/index/4.jpg" alt="Laura Pérez" height="300" width="300">
                <blockquote>La clínica cuenta con instalaciones modernas y equipos de última generación. Me siento seguro sabiendo que estoy recibiendo atención médica de alta calidad aquí.</blockquote>
                <h4>Laura Pérez</h4>
                <h5>Paciente satisfecha</h5>
            </div>
            <div class="testimonial">
                <img src="./assets/images/index/2.jpg" alt="Carlos Rodríguez" height="300" width="300">
                <blockquote>El personal médico y administrativo siempre es muy atento y servicial. Me siento agradecido por el excelente servicio que he recibido en esta clínica.</blockquote>
                <h4>Carlos Rodríguez</h4>
                <h5>Paciente leal</h5>
            </div>
        </div>
        
      </div>
  </section>
   <!-- FOOTER -->
   <footer>
      <div class="footer-section">
         <h3>Ubicación</h3>
         <p>Paseo de la Castellana 189 </p>
         <p>28046</p>
         <p>Madrid, España</p>
      </div>
      <div class="footer-section">
         <h3>Enlaces</h3>
         <ul class="footer-links">
            <li><a href="index.php">Inicio</a></li>
            <li><a href="./views/noticias.php">Noticias</a></li>
            <li><a href="./views/registro.php">Registro</a></li>
            <li><a href="./views/login.php">Login</a></li>
         </ul>
      </div>
      <div class="footer-section">
         <h3>Síguenos</h3>
         <div class="social-icons">
            <a href="#"><img src="./assets/images/icons/facebook.png" alt="Facebook" width="64" height="64"></a>
            <a href="#"><img src="./assets/images/icons/instagram.png" alt="Instagram" width="64" height="64"></a>
            <a href="#"><img src="./assets/images/icons/twitter.png" alt="Twitter" width="64" height="64"></a>
            <a href="#"><img src="./assets/images/icons/tiktok.png" alt="TikTok" width="64" height="64"></a>
         </div>
      </div>
   </footer>
   <!-- FIN DEL FOOTER -->
</body>

</html>