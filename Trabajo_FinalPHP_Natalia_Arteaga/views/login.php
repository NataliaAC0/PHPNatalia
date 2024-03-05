<?php
# Ver si hay una sesión activa
session_start();

# Inicializar la variable $error_message
$error_message = "";

# Si hay un error en el inicio de sesión, actualizar $error_message con el mensaje de error
if (isset($_GET['error'])) {
    $error_message = $_GET['error'];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>VitalCare Medical Centre</title>
   <link rel="stylesheet" type="text/css" href="../assets/css/login.css">
   <link rel="stylesheet" type="text/css" href="../assets/css/navbar.css">
   <script src="../assets/scripts/js_login.js"></script>
   <link rel="preconnect" href="https://fonts.googleapis.com">
   <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
   <link href="https://fonts.googleapis.com/css2?family=Raleway:ital,wght@0,100..900;1,100..900&display=swap"
      rel="stylesheet">
   <link rel="preconnect" href="https://fonts.gstatic.com">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
   <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap" rel="stylesheet">
</head>
<body>
  <!-- Incluyo la barra de navegación con php -->
  <?php include_once('navbar.php'); ?>
   <!-- Pongo una imagen de fono -->
   <div class="background">
      <img src="../assets/images/login/login.jpg" alt="" width="1080" height="608">
   </div>
   <!-- Creo un miniformulario con el username y la password que estará linkeado a mi página de c_login.php ubicada en controllers/, además creo un botón de no tienes cuenta? registrate que irá linkeado con registro.php. Y añado un bloque de php que se encargará de lidiar con los errores de inicio de sesión (usuario no encontrado o contraseña incorrecta) -->
   <form method="POST" action="../controllers/c_login.php">
      <h3>Login</h3>
      <label for="username">Email</label>
      <input type="text" placeholder="Email" id="username" name="username">
      <label for="password">Contraseña</label>
      <input type="password" placeholder="Contraseña" id="password" name="password">
      <button type="submit">Log In</button>
      <a href="../views/registro.php" class="registro">¿No tienes cuenta? Regístrate</a>
      <div class="error-message"><?php echo $error_message; ?></div>
   </form>
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
