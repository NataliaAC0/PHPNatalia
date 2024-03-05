<?php 
# Ver si hay una sesión activa ya que los usuarios y los administradores tendrán acceso a esta página (entre otras)
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
   <link rel="stylesheet" type="text/css" href="../assets/css/noticias.css">
   <link rel="stylesheet" type="text/css" href="../assets/css/navbar.css">
   <script src="../assets/scripts/js_noticias.js"></script>
   <link rel="preconnect" href="https://fonts.googleapis.com">
   <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
   <link href="https://fonts.googleapis.com/css2?family=Raleway:ital,wght@0,100..900;1,100..900&display=swap"
      rel="stylesheet">
</head>
<!-- Incluyo la barra de navegación con php -->
   <?php include_once('./navbar.php'); ?>
<body>
   <!-- Imagen principal -->
   <div class="imagen-principal-noticias">
      <img src="../assets/images/noticias/principal-noticias.jpg" alt="Imagen Principal" width="1800" height="400">   
      <h2>Noticias</h2>
   </div>
   <section class="noticias-mysql">
   <?php
   # Vinculo la ruta absoluta de config.php desde db_con.php
    require_once __DIR__ . '/../config/config.php';

    # Conexión a la base de datos para poder mostrar las noticias
    require_once __DIR__ . '/../controllers/db_conn.php';

    # Consulta SQL - necesitamos reocger todos los datos de las noticias excepto el ID (o sea, necesitamos, titulo, fecha de creación y nombre del autor). Para poder recoger todo esto tenemos que usar la entidad noticias y además la de users_data para poder recoger el nombre del admin que ha sido el encargado de crear la noticia.
    $sql = "SELECT noticias.idNoticia, noticias.titulo, noticias.imagen, noticias.texto, noticias.fecha, users_data.nombre, users_data.apellidos FROM noticias INNER JOIN users_data ON noticias.idUser = users_data.idUser";
    $result = $mysqli_connection->query($sql);

    if ($result->num_rows > 0) {
        # Mostramos las noticias con los echos 
        while ($row = $result->fetch_assoc()) {
            echo "<div class='noticia'>";
            echo "<h2 class='titulo'>" . $row["titulo"] . "</h2>";
            echo "<div class='fecha'>Fecha: " . $row["fecha"] . " | Autor: " . $row["nombre"] . " " . $row["apellidos"] . "</div>";
            echo "<div class='contenido-imagen'>";
            echo "<div class='contenido'>";
            echo "<div class='imagen'><img src='" . $row["imagen"] . "' alt='Imagen de la noticia'></div>";
            echo "<div class='texto'><p>" . $row["texto"] . "</p></div>";
            echo "</div>";
            echo "</div>"; 
            echo "</div>"; 
        }
    } else {
        echo "No se encontraron noticias.";
    }
    $mysqli_connection->close();
?>
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
            <li><a href="../index.php">Inicio</a></li>
            <li><a href="../views/noticias.php">Noticias</a></li>
            <li><a href="../views/registro.php">Registro</a></li>
            <li><a href="../views/login.php">Login</a></li>
         </ul>
      </div>
      <div class="footer-section">
         <h3>Síguenos</h3>
         <div class="social-icons">
            <a href="#"><img src="../assets/images/icons/facebook.png" alt="Facebook" width="64" height="64"></a>
            <a href="#"><img src="../assets/images/icons/instagram.png" alt="Instagram" width="64" height="64"></a>
            <a href="#"><img src="../assets/images/icons/twitter.png" alt="Twitter" width="64" height="64"></a>
            <a href="#"><img src="../assets/images/icons/tiktok.png" alt="TikTok" width="64" height="64"></a>
         </div>
      </div>
   </footer>
   <!-- FIN DEL FOOTER -->

</body>

</html>
