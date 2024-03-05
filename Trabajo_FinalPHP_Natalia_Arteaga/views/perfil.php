<?php
session_start();

include_once '../controllers/db_conn.php';

# Verificar si el usuario ha iniciado sesión, si no es así, redirigir a login.php
if (!isset($_SESSION['idUser'])) {
   header("Location: login.php");
   exit();
}

# Id inicio de sesión 
$idLogin = $_SESSION['idUser'];

# Obtener datos del usuario a través del login del id 
$query = "SELECT ud.nombre, ud.apellidos, ud.email, ud.telefono, ud.direccion, ud.sexo, ud.fecha_nacimiento, ul.password
   FROM users_login ul
   JOIN users_data ud ON ul.idUser = ud.idUser
   WHERE ud.idUser = $idLogin";

$result = mysqli_query($mysqli_connection, $query);

if ($result && mysqli_num_rows($result) > 0) {
   $user_data = mysqli_fetch_assoc($result);

} else {
   echo "Error al obtener los datos del usuario: " . mysqli_error($mysqli_connection);
}

# Ver si se ha enviado el formulario con un método post para recuperar los datos usando variables ($nombre, $apellidos, etc)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
   $nombre = $_POST['nombre'];
   $apellidos = $_POST['apellidos'];
   $telefono = $_POST['telefono'];
   $direccion = $_POST['direccion'];
   $sexo = $_POST['sexo'];
   $fecha_nacimiento = $_POST['fecha_nacimiento'];
   $password = $_POST['password'];

   $errores = false;

   # Para esta sección, vamos a hacer lo mismo que en c_registro.php, crear todas las validaciones ya que por mucho que te registres, si luego puedes insertar cualquier valor, no serviría de nada, además de hashear la contraseña. 

   # Validación del nombre
   if (empty($nombre) || strlen($nombre) < 3 || strlen($nombre) > 25) {
      $_SESSION['nombre_error'] = "El campo nombre es obligatorio y debe contener entre 3 y 25 caracteres.";
      $errores = true;
   }

   # Validación de los apellidos
   if (empty($apellidos) || strlen($apellidos) < 4 || strlen($apellidos) > 50) {
      $_SESSION['apellidos_error'] = "El campo apellidos es obligatorio y debe contener entre 4 y 50 caracteres";
      $errores = true;
   }

   # Validación del teléfono
   if (empty($telefono) || !preg_match("/^\d{9}$/", $telefono)) {
      $_SESSION['telefono_error'] = "El teléfono es obligatorio y debe contener 9 dígitos numéricos.";
      $errores = true;
   }

   # Validación de la fecha de nacimiento
   $fecha_actual = new DateTime();
   $fecha_nacimiento_obj = DateTime::createFromFormat('Y-m-d', $fecha_nacimiento);
   if (!$fecha_nacimiento_obj || $fecha_nacimiento_obj > $fecha_actual || $fecha_nacimiento_obj->diff($fecha_actual)->y < 18) {
      $_SESSION['fecha_nacimiento_error'] = "La fecha de nacimiento no es válida.";
      $errores = true;
   }

   # Validación de la dirección
   if (empty($direccion) || strlen($direccion) < 5 || strlen($direccion) > 50) {
      $_SESSION['direccion_error'] = "El campo dirección es obligatorio y debe contener entre 5 y 50 caracteres.";
      $errores = true;
   }

   # Validación del género
   if (empty($sexo)) {
      $_SESSION['sexo_error'] = "Por favor, seleccione su género.";
      $errores = true;
   }

   # Validación de la contraseña
   if (!empty($password) && (strlen($password) < 6 || !preg_match("/[A-Z]/", $password) || !preg_match("/[a-z]/", $password) || !preg_match("/[0-9]/", $password) || !preg_match("/[!@#\$%\^\&*\)\(+=._-]/", $password))) {
      $_SESSION['password_error'] = "La contraseña debe tener al menos 6 caracteres y contener al menos una letra mayúscula, una letra minúscula, un número y un carácter especial.";
      $errores = true;
   }

   # Si hay errores, vuelve a redirigir a la misma página
   if ($errores) {
      header("Location: ../views/perfil.php");
      exit();
   }

   # Hashear la contraseña antes de utilizarla
   $hashed_password = password_hash($password, PASSWORD_DEFAULT);

   # Creamos una consulta SQL para acceder a users_data y poder actualizar aquellos datos.
   $update_query = "UPDATE users_data AS ud
                 JOIN users_login AS ul ON ud.idUser = ul.idUser
                 SET ud.nombre = '$nombre',
                     ud.apellidos = '$apellidos',
                     ud.telefono = '$telefono',
                     ud.direccion = '$direccion',
                     ud.sexo = '$sexo',
                     ud.fecha_nacimiento = '$fecha_nacimiento',
                     ul.password = '$hashed_password'
                 WHERE ud.idUser = $idLogin";

   # Ejecutamos la consulta
   $update_result = mysqli_query($mysqli_connection, $update_query);

   # Si la actualización fue bien, redirige a la página de perfil.php
   if ($update_result) {
      header("Location: ../views/perfil.php");
      exit();
   } else {
      echo "Error al actualizar el perfil: " . mysqli_error($mysqli_connection);
   }
}

# Cerrar la conexión a la base de datos
mysqli_close($mysqli_connection);

?>

<!DOCTYPE html>
<html lang="es">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>VitalCare Medical Centre</title>
   <link rel="stylesheet" type="text/css" href="../assets/css/perfil.css">
   <link rel="stylesheet" type="text/css" href="../assets/css/navbar.css">
   <link rel="preconnect" href="https://fonts.googleapis.com">
   <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
   <link href="https://fonts.googleapis.com/css2?family=Raleway:ital,wght@0,100..900;1,100..900&display=swap"
      rel="stylesheet">
</head>

<body>
   <!-- BARRA NAVEGACIÓN -->
   <?php include_once('navbar.php'); ?>

   <!-- Sección de Perfil -->
   <div class="centrado">
      <section class="tus-datos">
         <h2>Tus datos</h2>
         <p><strong>Nombre:</strong>
            <?php echo $user_data['nombre']; ?>
         </p>
         <p><strong>Apellidos:</strong>
            <?php echo $user_data['apellidos']; ?>
         </p>
         <p><strong>Email:</strong>
            <?php echo $user_data['email']; ?>
         </p>
         <p><strong>Teléfono:</strong>
            <?php echo $user_data['telefono']; ?>
         </p>
         <p><strong>Dirección:</strong>
            <?php echo $user_data['direccion']; ?>
         </p>
         <p><strong>Género:</strong>
            <?php echo $user_data['sexo']; ?>
         </p>
         <p><strong>Fecha de Nacimiento:</strong>
            <?php echo $user_data['fecha_nacimiento']; ?>
         </p>
         <p><strong>Contraseña:</strong> <input type="password" value="<?php echo $user_data['password']; ?>" disabled>
         </p>
      </section>

      <!-- Sección de Modificar Perfil -->
      <section class="perfil-modificar">
         <h2>Modificar Datos</h2>
         <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <label for="nombre">Nombre:</label>
            <?php
            # Ver si hay errores en nombre
            if (isset($_SESSION["nombre_error"])) {
               echo "<div class='error_message'>" . $_SESSION['nombre_error'] . "</div>";
               # Si no hay errores, eliminamos el mensaje de error
               unset($_SESSION["nombre_error"]);
            }
            ?>
            <input type="text" id="nombre" name="nombre" value="<?php echo $user_data['nombre']; ?>" required>

            <label for="apellidos">Apellidos:</label>
            <?php
            # Ver si hay errores. Si no, eliminamos el mensaje de error
            if (isset($_SESSION["apellidos_error"])) {
               echo "<div class='error_message'>" . $_SESSION['apellidos_error'] . "</div>";
               unset($_SESSION["apellidos_error"]);
            }
            ?>
            <input type="text" id="apellidos" name="apellidos" value="<?php echo $user_data['apellidos']; ?>" required>

            <label for="email">Correo Electrónico:</label>
            <input type="email" id="email" name="email" value="<?php echo $user_data['email']; ?>" disabled>

            <label for="telefono">Teléfono:</label>
            <?php
            # Ver si hay errores. Si no, eliminamos el mensaje de error
            if (isset($_SESSION["telefono_error"])) {
               echo "<div class='error_message'>" . $_SESSION['telefono_error'] . "</div>";
               unset($_SESSION["telefono_error"]);
            }
            ?>
            <input type="text" id="telefono" name="telefono" value="<?php echo $user_data['telefono']; ?>" required>

            <label for="direccion">Dirección:</label>
            <?php
            # Ver si hay errores. Si no, eliminamos el mensaje de error
            if (isset($_SESSION["direccion_error"])) {
               echo "<div class='error_message'>" . $_SESSION['direccion_error'] . "</div>";
               unset($_SESSION["direccion_error"]);
            }
            ?>
            <textarea id="direccion" name="direccion" required><?php echo $user_data['direccion']; ?></textarea>

            <label for="sexo">Género:</label>
            <?php
            # Ver si hay errores. Si no, eliminamos el mensaje de error
            if (isset($_SESSION["sexo_error"])) {
               echo "<div class='error_message'>" . $_SESSION['sexo_error'] . "</div>";
               unset($_SESSION["sexo_error"]);
            }
            ?>
            <select id="sexo" name="sexo" required>
               <option value="Hombre" <?php if ($user_data['sexo'] == 'Hombre')
                  echo 'selected'; ?>>Hombre</option>
               <option value="Mujer" <?php if ($user_data['sexo'] == 'Mujer')
                  echo 'selected'; ?>>Mujer</option>
            </select>

            <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
            <?php
            # Ver si hay errores. Si no, eliminamos el mensaje de error
            if (isset($_SESSION["fecha_nacimiento_error"])) {
               echo "<div class='error_message'>" . $_SESSION['fecha_nacimiento_error'] . "</div>";
               unset($_SESSION["fecha_nacimiento_error"]);
            }
            ?>
            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento"
               value="<?php echo $user_data['fecha_nacimiento']; ?>" required>

            <label for="password">Contraseña:</label>
            <?php
            # Ver si hay errores. Si no, eliminamos el mensaje de error
            if (isset($_SESSION["password_error"])) {
               echo "<div class='error_message'>" . $_SESSION['password_error'] . "</div>";
               unset($_SESSION["password_error"]);
            }
            ?>
            <input type="password" id="password" name="password" value="" required>



            <button type="submit">Guardar Cambios</button>
         </form>
      </section>
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