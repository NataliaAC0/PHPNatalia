<?php
# Ver si hay una sesión activa ya que esta página la podrán ver tanto administradores como usuarios (entre otras páginas)
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
   <link rel="stylesheet" type="text/css" href="../assets/css/registro.css">
   <link rel="stylesheet" type="text/css" href="../assets/css/navbar.css">
   <script src="../assets/scripts/js_registro.js"></script>
   <link rel="preconnect" href="https://fonts.googleapis.com">
   <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
   <link href="https://fonts.googleapis.com/css2?family=Raleway:ital,wght@0,100..900;1,100..900&display=swap"
      rel="stylesheet">
</head>

<body>
   <!-- Incluimos la barra de navegación con php -->
   <?php include_once('navbar.php'); ?>
   <!-- Imagen principal -->
   <div class="imagen-principal-registro">
      <img src="../assets/images/registro/principal-registro.jpg" alt="Imagen Principal" width="1800" height="400">
   </div>
   <div class="formulario">
      <h2>Registro</h2>
      <div class="aviso_registro">
      </div>
      <!-- Formulario sencillo con nombre, apellidos, email, dirección, teléfono, fecha de nacimiento, dirección, género, contraseña validado desde el archivo c_registro.php -- esta sección también contiene bloques de php para mostrar los mensajes de error de php en cada uno de los campos y también los elimina en el caso que no haya. Para poder linkear al archivo de c_registro usamos ACTION y la ruta relativa al archivo de c_registro, que está dentro de controllers. -->
      <form class="miFormulario" action="../controllers/c_registro.php" method="POST" id="registro-form">
         <label for="nombre">Nombre: </label>
         <?php
         # Ver si hay mensajes de error en nombre (validando si contiene todo lo que hemos pediddo en c_registro.php)
         if (isset($_SESSION["nombre_error"])) {
            echo "<div class='error_message'>" . $_SESSION['nombre_error'] . "</div>";
            # Si no hay errores, lo eliminamos
            unset($_SESSION["nombre_error"]);
         }
         ?>
         <div class="error-mensaje" id="nombre-error">El nombre es obligatorio y debe tener al menos 3 caracteres.</div>
         <input type="text" id="nombre" name="nombre" required>   

         <label for="apellidos">Apellidos: </label>
         <?php
         # Ver si hay mensajes de error en apellidos (validando si contiene todo lo que hemos pediddo en c_registro.php)
         if (isset($_SESSION["apellidos_error"])) {
            echo "<div class='error_message'>" . $_SESSION['apellidos_error'] . "</div>";
            # Si no hay errores, lo eliminamos
            unset($_SESSION["apellidos_error"]);
         }
         ?>
         <div class="error-mensaje" id="apellidos-error">Los apellidos son obligatorios y deben tener al menos 3
            caracteres.</div>
         <input type="text" id="apellidos" name="apellidos" required>

         <label for="email">Email: </label>
         <?php
         # Ver si hay mensajes de error en email (validando si contiene todo lo que hemos pediddo en c_registro.php)
         if (isset($_SESSION["email_error"])) {
            echo "<div class='error_message'>" . $_SESSION['email_error'] . "</div>";
            # Si no hay mensajes de error, lo eliminamos
            unset($_SESSION["email_error"]);
         }
         ?>
         <div class="error-mensaje" id="email-error">El email es obligatorio y debe tener un formato válido.</div>
         <input type="email" id="email" name="email" required>

         <label for="telefono">Teléfono: </label>
         <?php
         # Ver si hay mensajes de error en telefono (validando si contiene todo lo que hemos pediddo en c_registro.php)
         if (isset($_SESSION["telefono_error"])) {
            echo "<div class='error_message'>" . $_SESSION['telefono_error'] . "</div>";
            # Si no hay mensajes de error, lo eliminamos
            unset($_SESSION["telefono_error"]);
         }
         ?>
         <div class="error-mensaje" id="telefono-error">El teléfono es obligatorio.</div>
         <input type="tel" id="telefono" name="telefono" required>

         <label for="fecha_nacimiento">Fecha de Nacimiento: </label>
         <?php
         # Ver si hay mensajes de error en la fecha de nacimiento (validando si contiene todo lo que hemos pediddo en c_registro.php)
         if (isset($_SESSION["fecha_nacimiento_error"])) {
            echo "<div class='error_message'>" . $_SESSION['fecha_nacimiento_error'] . "</div>";
            # Si no hay mensajes de error, lo eliminamos
            unset($_SESSION["fecha_nacimiento_error"]);
         }
         ?>
         <div class="error-mensaje" id="fNac-error">La fecha de nacimiento es obligatoria.</div>
         <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" required>

         <label for="direccion">Dirección: </label>
         <?php
         # Ver si hay mensajes de error en la direccion (validando si contiene todo lo que hemos pediddo en c_registro.php)
         if (isset($_SESSION["direccion_error"])) {
            echo "<div class='error_message'>" . $_SESSION['direccion_error'] . "</div>";
            # Si no hay mensajes de error, los eliminamos 
            unset($_SESSION["direccion_error"]);
         }
         ?>
         <div class="error-mensaje" id="direccion-error">La dirección es obligatoria.</div>
         <input type="text" id="direccion" name="direccion" required>

         <label for="sexo">Género: </label>
         <?php
         # Ver si hay mensajes de error en sexo (validando si contiene todo lo que hemos pediddo en c_registro.php)
         if (isset($_SESSION["sexo_error"])) {
            echo "<div class='error_message'>" . $_SESSION['sexo_error'] . "</div>";
            # Si no hay mensajes de error, lo eliminamos 
            unset($_SESSION["sexo_error"]);
         }
         ?>
         <!-- Para la selección del genero tendremos que usar select -->
         <div class="error-mensaje" id="sexo-error">Por favor, seleccione su género.</div>
         <select id="sexo" name="sexo" required>
            <option value="">Seleccione su género</option>
            <option value="hombre">Hombre</option>
            <option value="mujer">Mujer</option>
         </select>

         <label for="password">Contraseña: </label>
         <?php
         # Ver si hay mensajes de error en contraseña (validando si contiene todo lo que hemos pediddo en c_registro.php)
         if (isset($_SESSION["password_error"])) {
            echo "<div class='error_message'>" . $_SESSION['password_error'] . "</div>";
            # Si no hay mensajes de error, lo eliminamos 
            unset($_SESSION["password_error"]);
         }
         ?>
         <div class="error-mensaje" id="password-error">La contraseña debe tener un mínimo de 6 caracteres y un carácter
            especial.</div>
         <div class="input-group">
            <input type="password" id="password" name="password" required>
            <button type="button" id="togglePassword">Mostrar contraseña</button>
         </div>

         <?php
         # Ver si no han marcado la casilla de acepto (validando si contiene todo lo que hemos pediddo en c_registro.php)
         if (isset($_SESSION["acepto_error"])) {
            echo "<div class='error_message'>" . $_SESSION['acepto_error'] . "</div>";
            # Si no hay mensajes de error, lo eliminamos 
            unset($_SESSION["acepto_error"]);
         }
         ?>
         <div class="acepto-checkbox">
            <input type="checkbox" id="acepto" name="acepto" required>
            <label for="acepto">Acepto los términos y condiciones</label>
            <div class="error-mensaje" id="acepto-error">Debe aceptar los términos y condiciones para continuar.</div>
         </div>
         <!-- Botón para confirmar el registro-->
         <button type="submit">Confirmar registro</button>
      </form>
      <!-- Si alguien ya es usuario, puede acceder al login desde aquí -->
      <a href="../views/login.php" class="inicia">¿Ya eres usuario? Inicia sesión</a>
   </div>
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