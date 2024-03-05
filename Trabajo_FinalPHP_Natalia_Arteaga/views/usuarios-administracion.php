<?php
session_start();

# Archivo de conexión a la base de datos
include_once '../controllers/db_conn.php';

# Errores validación
$errores_creacion = array();
$errores_modificacion = array();

# Ver si el usuario ha iniciado sesión, si no es así, redirigir a login.php
if (!isset($_SESSION['idUser'])) {
   header("Location: login.php");
   exit();
}

# Id inicio de sesión 
$idLogin = $_SESSION['idUser'];


# VALIDACIONES DE EMAIL, LONGITUD, FECHA NACIMIENTO, NOMBRE, APELLIDOS, TELEFONO, EMAIL, DIRECCIÓN, SEXO, CONTRASEÑA Y ROL. 
function validarEmail($email)
{
   return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validarLongitud($texto, $min, $max)
{
   $longitud = strlen($texto);
   return ($longitud >= $min && $longitud <= $max);
}

function validarFechaNacimiento($fecha_nacimiento)
{
   # YYYY - MM - DD
   if (!DateTime::createFromFormat('Y-m-d', $fecha_nacimiento)) {
      return false;
   }
   # HOY
   $fecha_actual = new DateTime();

   $fecha_nacimiento_obj = DateTime::createFromFormat('Y-m-d', $fecha_nacimiento);
   if (!$fecha_nacimiento_obj) {
      return false;
   }
   # Calcular la diferencia
   $edad = $fecha_nacimiento_obj->diff($fecha_actual)->y;
   return true;
}

# Nuevo usuario 
function crearUsuario($nombre, $apellidos, $email, $telefono, $direccion, $sexo, $password, $fecha_nacimiento, $rol)
{
   global $mysqli_connection, $errores_creacion;
   # Validar nombre
   if (!validarLongitud($nombre, 3, 25) || !preg_match("/^[a-zA-ZÁÉÍÓÚáéíóúñÑ\s]+$/", $nombre)) {
      $errores_creacion['nombre'] = "El nombre debe contener entre 3 y 25 caracteres.";
   }
   # Validar apellidos
   if (!validarLongitud($apellidos, 3, 50) || !preg_match("/^[a-zA-ZÁÉÍÓÚáéíóúñÑ\s]+$/", $apellidos)) {
      $errores_creacion['apellidos'] = "Los apellidos deben contener entre 4 y 50 caracteres.";
   }
   # Validar email
   if (!validarEmail($email)) {
      $errores_creacion['email'] = "El formato del correo electrónico es inválido.";
   }
   # Validar teléfono (opcional)
   if (!empty($telefono) && !preg_match("/^\d{9}$/", $telefono)) {
      $errores_creacion['telefono'] = "El teléfono debe contener 9 dígitos.";
   }
   # Validar sexo
   if ($sexo !== "Hombre" && $sexo !== "Mujer") {
      $errores_creacion['sexo'] = "El sexo seleccionado no es válido.";
   }
   # Validar contraseña
   if (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^\da-zA-Z]).{6,}$/", $password)) {
      $errores_creacion['password'] = "La contraseña debe tener al menos 6 caracteres y contener al menos una letra mayúscula, una letra minúscula, un número y un carácter especial.";
   }
   # Validar dirección
   if (!validarLongitud($direccion, 5, 50)) {
      $errores_creacion['direccion'] = "El campo dirección es obligatorio y debe contener entre 5 y 50 caracteres.";
   }
   # Validar fecha de nacimiento
   if (!validarFechaNacimiento($fecha_nacimiento)) {
      $errores_creacion['fecha_nacimiento'] = "La fecha de nacimiento no es válida o el usuario debe tener al menos 18 años.";
   }
   # Validar rol
   if ($rol !== "user" && $rol !== "admin") {
      $errores_creacion['rol'] = "El rol seleccionado no es válido";
   }
   # En el caso que no haya errores, se seguirá con la creación del usuario
   if (empty($errores_creacion)) {
      # hasheamos la contraseña
      $hashed_password = password_hash($password, PASSWORD_DEFAULT);
      # Ver si el correo existe en la base de datos. Si ya existe, creamos un alert donde diga que el correo electrónico ya está registrado. Por lo tanto, no se podrá crear un nuevo usuario con el mismo correo. 
      $check_email_query = "SELECT COUNT(*) as count FROM users_data WHERE email = '$email'";
      $result = $mysqli_connection->query($check_email_query);
      $row = $result->fetch_assoc();

      if ($row['count'] > 0) {
         echo "<script>document.getElementById('mensaje-crear').innerHTML = 'El correo electrónico ya está registrado.';</script>";
         return;
      }
      # Consulta sql para insertar los datos en users_data 
      $sql = "INSERT INTO users_data (nombre, apellidos, email, telefono, direccion, sexo, password, fecha_nacimiento) 
               VALUES ('$nombre', '$apellidos', '$email', '$telefono', '$direccion', '$sexo', '$hashed_password', '$fecha_nacimiento')";
      if ($mysqli_connection->query($sql) === TRUE) {
         # Obtenemos su id para insertarlo con una consulta sql en la entidad users_login
         $last_id = $mysqli_connection->insert_id;
         $sql_login = "INSERT INTO users_login (idUser, password, rol) VALUES ('$last_id', '$hashed_password', '$rol')";
         if ($mysqli_connection->query($sql_login) === TRUE) {
            echo "<script>document.getElementById('mensaje-crear').innerHTML = 'Usuario creado exitosamente.';</script>";
         } else {
            # Si hubo un error, no hacer la creación en users_data y lanzar un script con un error. 
            $mysqli_connection->query("DELETE FROM users_data WHERE idUser = '$last_id'");
            echo "<script>document.getElementById('mensaje-crear').innerHTML = 'Error al crear el usuario.';</script>";
         }
      } else {
         echo "<script>document.getElementById('mensaje-crear').innerHTML = 'Error al crear el usuario.';</script>";
      }
   }
}

// Modificar datos por correo electrónico 
function modificarUsuario($email, $nombre, $apellidos, $telefono, $direccion, $sexo, $password, $fecha_nacimiento, $rol)
{
   global $mysqli_connection, $errores_modificacion;
   # Validar nombre
   if (!validarLongitud($nombre, 3, 25) || !preg_match("/^[a-zA-ZÁÉÍÓÚáéíóúñÑ\s]+$/", $nombre)) {
      $errores_modificacion['nombre'] = "El nombre debe contener entre 3 y 25 caracteres.";
   }
   # Validar apellidos
   if (!validarLongitud($apellidos, 3, 50) || !preg_match("/^[a-zA-ZÁÉÍÓÚáéíóúñÑ\s]+$/", $apellidos)) {
      $errores_modificacion['apellidos'] = "Los apellidos deben contener entre 3 y 50 caracteres.";
   }
   # Validar email
   if (!validarEmail($email)) {
      $errores_modificacion['email'] = "El formato del correo electrónico es inválido.";
   }
   # Validar teléfono (opcional)
   if (!empty($telefono) && !preg_match("/^\d{9}$/", $telefono)) {
      $errores_modificacion['telefono'] = "El teléfono debe contener 9 dígitos.";
   }
   # Validar dirección
   if (!validarLongitud($direccion, 5, 50)) {
      $errores_modificacion['direccion'] = "El campo dirección es obligatorio y debe contener entre 5 y 50 caracteres.";
   }
   # Validar sexo
   if ($sexo !== "Hombre" && $sexo !== "Mujer") {
      $errores_modificacion['sexo'] = "El sexo seleccionado no es válido.";
   }
   # Validar contraseña
   if (!empty($password) && !preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^\da-zA-Z]).{6,}$/", $password)) {
      $errores_modificacion['password'] = "La contraseña debe tener al menos 6 caracteres y contener al menos una letra mayúscula, una letra minúscula, un número y un carácter especial.";
   }
   # Validar fecha de nacimiento
   if (!validarFechaNacimiento($fecha_nacimiento)) {
      $errores_modificacion['fecha_nacimiento'] = "La fecha de nacimiento no es válida o el usuario debe tener al menos 18 años.";
   }
   # Validar rol
   if ($rol !== "user" && $rol !== "admin") {
      $errores_modificacion['rol'] = "El rol seleccionado no es válido.";
   }
   # En el caso que no haya errores, se sigue con la modificación y se crea una consulta sql para actualizar los datos del usuario con UPDATE 
   if (empty($errores_modificacion)) {
      global $mysqli_connection;
      $sql = "UPDATE users_data SET nombre='$nombre', apellidos='$apellidos', telefono='$telefono', direccion='$direccion', 
               sexo='$sexo', fecha_nacimiento='$fecha_nacimiento'";

      if (!empty($password)) {
         $hashed_password = password_hash($password, PASSWORD_DEFAULT);
         $sql .= ", password='$hashed_password'";
      }
      $sql .= " WHERE email='$email'";

      if ($mysqli_connection->query($sql) === TRUE) {
         # Actualizamos el rol del usuario 
         $sql_update_login = "UPDATE users_login SET rol='$rol' WHERE idUser IN (SELECT idUser FROM users_data WHERE email='$email')";
         if ($mysqli_connection->query($sql_update_login) === TRUE) {
            echo "<script>document.getElementById('mensaje-modificar').innerHTML = 'Usuario modificado exitosamente.';</script>";
         } else {
            echo "<script>alert('Error al modificar el usuario.'); location.reload();</script>";
         }
      } else {
         echo "<script>alert('Error al modificar el usuario.'); location.reload();</script>";
      }
   }
}
# Eliminar un usuario
function eliminarUsuario($email)
{
   global $mysqli_connection;
   
   // Eliminar citas relacionadas con el usuario
   $sql_delete_citas = "DELETE FROM citas WHERE idUser IN (SELECT idUser FROM users_data WHERE email='$email')";
   if ($mysqli_connection->query($sql_delete_citas) === TRUE) {
      // Luego eliminar el usuario de la tabla users_login
      $sql_delete_login = "DELETE FROM users_login WHERE idUser IN (SELECT idUser FROM users_data WHERE email='$email')";
      if ($mysqli_connection->query($sql_delete_login) === TRUE) {
         // Finalmente, eliminar el usuario de la tabla users_data
         $sql_delete_data = "DELETE FROM users_data WHERE email='$email'";
         if ($mysqli_connection->query($sql_delete_data) === TRUE) {
            echo "<script>document.getElementById('mensaje-eliminar').innerHTML = 'Usuario eliminado exitosamente.';</script>";
         } else {
            echo "<script>alert('Error al eliminar el usuario.'); location.reload();</script>";
         }
      } else {
         echo "<script>alert('Error al eliminar el usuario.'); location.reload();</script>";
      }
   } else {
      echo "<script>alert('Error al eliminar el usuario.'); location.reload();</script>";
   }
}


// Ver todos los correos electrónicos para el select
function obtenerCorreosUsuarios()
{
   global $mysqli_connection;
   $sql = "SELECT email FROM users_data";
   $result = $mysqli_connection->query($sql);
   $correos = array();
   if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
         $correos[] = $row['email'];
      }
   }
   return $correos;
}

# Procesamos las sentencias de modificación, creación y eliminación 
if ($_SERVER["REQUEST_METHOD"] == "POST") {
   if (isset($_POST['crear_usuario'])) {
      # Obtenemos datos del formulario
      $nombre = $_POST['nombre'];
      $apellidos = $_POST['apellidos'];
      $email = $_POST['email'];
      $telefono = $_POST['telefono'];
      $direccion = $_POST['direccion'];
      $sexo = $_POST['sexo'];
      $password = $_POST['password'];
      $fecha_nacimiento = $_POST['fecha_nacimiento'];
      $rol = $_POST['rol'];
      # Llamar a crear usuario
      crearUsuario($nombre, $apellidos, $email, $telefono, $direccion, $sexo, $password, $fecha_nacimiento, $rol);
   } elseif (isset($_POST['modificar_usuario'])) {
      # Obtenemos datos del formulario
      $email = $_POST['email'];
      $nombre = $_POST['nombre'];
      $apellidos = $_POST['apellidos'];
      $telefono = $_POST['telefono'];
      $direccion = $_POST['direccion'];
      $sexo = $_POST['sexo'];
      $password = $_POST['password'];
      $fecha_nacimiento = $_POST['fecha_nacimiento'];
      $rol = $_POST['rol'];
      # Llamar a la funcion modificar usuario 
      modificarUsuario($email, $nombre, $apellidos, $telefono, $direccion, $sexo, $password, $fecha_nacimiento, $rol);
   } elseif (isset($_POST['eliminar_usuario'])) {
      # Obtener datos del formulario
      $email = $_POST['email'];
      # Llamar a la funcion eliminar usuario 
      eliminarUsuario($email);
   }
}

// Correos electrónicos para el select 
$correos_usuarios = obtenerCorreosUsuarios();
?>

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>VitalCare Medical Centre</title>
   <link rel="stylesheet" type="text/css" href="../assets/css/us_admin.css">
   <link rel="stylesheet" type="text/css" href="../assets/css/navbar.css">
   <link rel="preconnect" href="https://fonts.googleapis.com">
   <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
   <link href="https://fonts.googleapis.com/css2?family=Raleway:ital,wght@0,100..900;1,100..900&display=swap"
      rel="stylesheet">
</head>
<style>
   /* Estilos del mensaje de error */
   .error-message {
      color: red;
      font-size: 0.8rem;
      margin-top: 5px;
      margin-bottom: 10px;
   }
</style>

<body>
   <!-- Incluimos la barra de navegación con php -->
   <?php include_once('navbar.php'); ?>
   <div class="espacio"></div>
   <section class="crear">
      <!-- Aquí creamos unos div con php para mostrar los mensajes de error  -->
      <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
         <h2 class="crear">Crear Nuevo Usuario</h2>
         <label for="nombre">Nombre:</label>
         <?php if (isset($errores_creacion['nombre'])): ?>
            <div class="error-message">
               <?php echo $errores_creacion['nombre']; ?>
            </div>
         <?php endif; ?>
         <input type="text" name="nombre" required><br><br>

         <label for="apellidos">Apellidos:</label>
         <?php if (isset($errores_creacion['apellidos'])): ?>
            <div class="error-message">
               <?php echo $errores_creacion['apellidos']; ?>
            </div>
         <?php endif; ?>
         <input type="text" name="apellidos" required><br><br>

         <label for="email">Email:</label>
         <?php if (isset($errores_creacion['email'])): ?>
            <div class="error-message">
               <?php echo $errores_creacion['email']; ?>
            </div>
         <?php endif; ?>
         <input type="text" name="email" required><br><br>

         <label for="telefono">Teléfono:</label>
         <?php if (isset($errores_creacion['telefono'])): ?>
            <div class="error-message">
               <?php echo $errores_creacion['telefono']; ?>
            </div>
         <?php endif; ?>
         <input type="text" name="telefono" required><br><br>

         <label for="direccion">Dirección:</label>
         <?php if (isset($errores_creacion['direccion'])): ?>
            <div class="error-message">
               <?php echo $errores_creacion['direccion']; ?>
            </div>
         <?php endif; ?>
         <input type="text" name="direccion" required><br><br>

         <label for="sexo">Sexo:</label>
         <?php if (isset($errores_creacion['sexo'])): ?>
            <div class="error-message">
               <?php echo $errores_creacion['sexo']; ?>
            </div>
         <?php endif; ?>
         <select name="sexo" required>
            <option value="Hombre">Hombre</option>
            <option value="Mujer">Mujer</option>
         </select>

         <label for="password">Contraseña:</label>
         <?php if (isset($errores_creacion['password'])): ?>
            <div class="error-message">
               <?php echo $errores_creacion['password']; ?>
            </div>
         <?php endif; ?>
         <input type="password" name="password" required><br><br>

         <label for="feche_nacimiento">Fecha de Nacimiento:</label>
         <?php if (isset($errores_creacion['fecha_nacimiento'])): ?>
            <div class="error-message">
               <?php echo $errores_creacion['fecha_nacimiento']; ?>
            </div>
         <?php endif; ?>
         <input type="date" name="fecha_nacimiento" required><br><br>

         <label for="rol">Rol:</label>
         <?php if (isset($errores_creacion['rol'])): ?>
            <div class="error-message">
               <?php echo $errores_creacion['rol']; ?>
            </div>
         <?php endif; ?>
         <select name="rol" required>
            <option value="user">Usuario</option>
            <option value="admin">Administrador</option>
         </select><br><br>


         <input type="submit" name="crear_usuario" value="Crear Usuario">
      </form>
   </section>

   <section class="modificar">
      <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
         <h2 class="crear">Modificar Usuario</h2>

         <label for="email">Correo Electrónico del Usuario:</label>
         <select name="email" required>
            <?php foreach ($correos_usuarios as $correo_usuario): ?>
               <option value="<?php echo $correo_usuario; ?>">
                  <?php echo $correo_usuario; ?>
               </option>
            <?php endforeach; ?>
         </select><br><br>

         <label for="nombre">Nombre:</label>
         <?php if (isset($errores_creacion['nombre'])): ?>
            <div class="error-message">
               <?php echo $errores_creacion['nombre']; ?>
            </div>
         <?php endif; ?>
         <input type="text" name="nombre" required><br><br>

         <label for="apellidos">Apellidos:</label>
         <?php if (isset($errores_creacion['apellidos'])): ?>
            <div class="error-message">
               <?php echo $errores_creacion['apellidos']; ?>
            </div>
         <?php endif; ?>
         <input type="text" name="apellidos" required><br><br>

         <label for="telefono">Teléfono:</label>
         <?php if (isset($errores_creacion['telefono'])): ?>
            <div class="error-message">
               <?php echo $errores_creacion['telefono']; ?>
            </div>
         <?php endif; ?>
         <input type="text" name="telefono" required><br><br>

         <label for="direccion">Dirección:</label>
         <?php if (isset($errores_creacion['direccion'])): ?>
            <div class="error-message">
               <?php echo $errores_creacion['direccion']; ?>
            </div>
         <?php endif; ?>
         <input type="text" name="direccion" required><br><br>

         <label for="sexo">Sexo:</label>
         <?php if (isset($errores_creacion['sexo'])): ?>
            <div class="error-message">
               <?php echo $errores_creacion['sexo']; ?>
            </div>
         <?php endif; ?>
         <select name="sexo" required>
            <option value="Hombre">Hombre</option>
            <option value="Mujer">Mujer</option>
         </select>

         <label for="password">Contraseña:</label>
         <?php if (isset($errores_creacion['password'])): ?>
            <div class="error-message">
               <?php echo $errores_creacion['password']; ?>
            </div>
         <?php endif; ?>
         <input type="password" name="password" required><br><br>

         <label for="feche_nacimiento">Fecha de Nacimiento:</label>
         <?php if (isset($errores_creacion['fecha_nacimiento'])): ?>
            <div class="error-message">
               <?php echo $errores_creacion['fecha_nacimiento']; ?>
            </div>
         <?php endif; ?>
         <input type="date" name="fecha_nacimiento" required><br><br>

         <label for="rol">Rol:</label>
         <?php if (isset($errores_creacion['rol'])): ?>
            <div class="error-message">
               <?php echo $errores_creacion['rol']; ?>
            </div>
         <?php endif; ?>
         <select name="rol" required>
            <option value="user">Usuario</option>
            <option value="admin">Administrador</option>
         </select><br><br>

         <input type="submit" name="modificar_usuario" value="Modificar Usuario">
      </form>
   </section>

   <section class="eliminar">
      <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
         <h2 class="eliminar">Eliminar Usuario</h2>
         <label for="email">Correo Electrónico del Usuario:</label>
         <select name="email" required>
            <?php foreach ($correos_usuarios as $correo_usuario): ?>
               <option value="<?php echo $correo_usuario; ?>">
                  <?php echo $correo_usuario; ?>
               </option>
            <?php endforeach; ?>
         </select><br><br>
         <input type="submit" name="eliminar_usuario" value="Eliminar Usuario">
      </form>
   </section>

</body>

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

</html>