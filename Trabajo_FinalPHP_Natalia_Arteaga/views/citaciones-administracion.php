<?php
# Ver si hay una sesión activa 
session_start();

# Ver si el usuario ha iniciado sesión, si no es así, redirigir a login.php
if (!isset($_SESSION['idUser'])) {
   header("Location: login.php");
   exit();
}

# Id inicio de sesión 
$idLogin = $_SESSION['idUser'];

# Poner el archivo de la base de datos
include_once '../controllers/db_conn.php';

# Datos de las citas CONSULTA SQL 
$sql = "SELECT citas.idCita, citas.fechaCita, citas.motivoCita, users_data.nombre, users_data.apellidos 
        FROM citas 
        INNER JOIN users_data ON citas.idUser = users_data.idUser";
$result = $mysqli_connection->query($sql);
 
# Ver si se ha enviado el formulario para crear una cita. Obtenemos los datos del usuario, la fecha de la cita, y el motvio de la cita. 
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['crear_cita'])) {
   $idUser = $_POST['id_user']; 
   $fechaCita = $_POST['fecha_cita'];
   $motivoCita = $_POST['motivo_cita'];

   # Sentencia sql para insertar una nueva cita. Saldrá un alert en el caso que haya ido bien, y en el caso que haya ido mal saldrá un alert con un mensaje de error. 
   $sql_insert = "INSERT INTO citas (idUser, fechaCita, motivoCita) VALUES (?, ?, ?)";
   $stmt_insert = $mysqli_connection->prepare($sql_insert);
   if ($stmt_insert) {
      $stmt_insert->bind_param("iss", $idUser, $fechaCita, $motivoCita);
      if ($stmt_insert->execute()) {
         echo "<script>alert('Cita creada exitosamente.');</script>";
         echo "<meta http-equiv='refresh' content='0'>"; 
      } else {
         echo "<script>alert('Error al crear la cita. Por favor, inténtalo de nuevo.');</script>";
      }
      $stmt_insert->close();
   } else {
      echo "<script>alert('Error en la preparación de la consulta para crear la cita.');</script>";
   }
}
# Modificar citas. Obtenemos los datos idcita, nuevafecha y nuevomotivo
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['modificar_cita'])) {
   $idCita = $_POST['id_cita_modificar'];
   $nuevaFecha = $_POST['nueva_fecha'];
   $nuevoMotivo = $_POST['nuevo_motivo'];

   # Actualización de la base de datos CONSULTA SQL
   $sql_update = "UPDATE citas SET fechaCita=?, motivoCita=? WHERE idCita=?";
   $stmt_update = $mysqli_connection->prepare($sql_update);
   if ($stmt_update) {
      $stmt_update->bind_param("ssi", $nuevaFecha, $nuevoMotivo, $idCita);
      if ($stmt_update->execute()) {
         echo "<script>alert('Cita modificada exitosamente.');</script>";
         echo "<meta http-equiv='refresh' content='0'>"; 
      } else {
         echo "<script>alert('Error al modificar la cita. Por favor, inténtalo de nuevo.');</script>";
      }
      $stmt_update->close();
   } else {
      echo "<script>alert('Error en la preparación de la consulta para modificar la cita.');</script>";
   }
}

# Eliminar una cita 
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['eliminar_cita'])) {
   // Obtener el ID de la cita a eliminar
   $idCitaEliminar = $_POST['id_cita_eliminar'];

   # Consulta sql para eliminar una cita 
   $sql_delete = "DELETE FROM citas WHERE idCita=?";
   $stmt_delete = $mysqli_connection->prepare($sql_delete);
   if ($stmt_delete) {
      // Vincular el parámetro y ejecutar la consulta
      $stmt_delete->bind_param("i", $idCitaEliminar);
      if ($stmt_delete->execute()) {
         echo "<script>alert('Cita eliminada exitosamente.');</script>";
         echo "<meta http-equiv='refresh' content='0'>"; 
      } else {
         echo "<script>alert('Error al eliminar la cita. Por favor, inténtalo de nuevo.');</script>";
      }
      $stmt_delete->close();
   } else {
      echo "<script>alert('Error en la preparación de la consulta para eliminar la cita.');</script>";
   }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>VitalCare Medical Centre</title>
   <link rel="stylesheet" type="text/css" href="/Trabajo_FinalPHP_Natalia_Arteaga/assets/css/c_admin.css">
   <link rel="stylesheet" type="text/css" href="../assets/css/navbar.css">
   <script src="../assets/scripts/js_noticias.js"></script>
   <link rel="preconnect" href="https://fonts.googleapis.com">
   <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
   <link href="https://fonts.googleapis.com/css2?family=Raleway:ital,wght@0,100..900;1,100..900&display=swap"
      rel="stylesheet">
</head>

<body>
   <!-- Incluir la barra de navegación con php -->
   <?php include_once('navbar.php'); ?>

   <body>
      <div class="espacio"></div>
      <section class="crear">
         <!-- Formulario para crear una nueva cita -->
         <h2>Crear Nueva Cita</h2>
         <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
            <label for="id_user">Usuario:</label>
            <select name="id_user" id="id_user">
               <?php
               # Consulta para ver todos los usuarios que no son administradores
               $sql_users = "SELECT idUser, nombre, apellidos FROM users_data WHERE idUser NOT IN (SELECT idUser FROM users_login WHERE rol = 'admin')";
               $result_users = $mysqli_connection->query($sql_users);
               if ($result_users->num_rows > 0) {
                  while ($row_user = $result_users->fetch_assoc()) {
                     echo "<option value='" . $row_user['idUser'] . "'>" . $row_user['nombre'] . " " . $row_user['apellidos'] . "</option>";
                  }
               }
               ?>
            </select>
            <label for="fecha_cita">Fecha de la Cita:</label>
            <input type="date" name="fecha_cita" id="fecha_cita" required>
            <label for="motivo_cita">Motivo de la Cita:</label>
            <textarea name="motivo_cita" id="motivo_cita" rows="3" required></textarea>
            <button type="submit" name="crear_cita">Crear Cita</button>
         </form>
      </section>
      <!-- Formulario para modificar una cita -->
      <section class="modificar">
         <h2>Modificar Cita</h2>
         <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
            <label for="cita_modificar">Selecciona una cita:</label>
            <select id="cita_modificar" name="id_cita_modificar" required>
               <?php
               # Consulta sql para ver todas las citas
               $sql_citas = "SELECT idCita, idUser, fechaCita, motivoCita FROM citas";
               $result_citas = $mysqli_connection->query($sql_citas);
               if ($result_citas->num_rows > 0) {
                  while ($row_cita = $result_citas->fetch_assoc()) {
                     # Obtener el nombre del usuario en vez de su id. Lo buscamos en users_data 
                     $sql_usuario = "SELECT nombre, apellidos FROM users_data WHERE idUser = " . $row_cita['idUser'];
                     $result_usuario = $mysqli_connection->query($sql_usuario);
                     $row_usuario = $result_usuario->fetch_assoc();
                     echo "<option value='" . $row_cita['idCita'] . "'>Cita con " . $row_usuario['nombre'] . " " . $row_usuario['apellidos'] . " - Fecha: " . $row_cita['fechaCita'] . "</option>";
                  }
               } else {
                  echo "<option value=''>No hay citas disponibles</option>";
               }
               ?>
            </select>
            <label for="nueva_fecha">Nueva fecha:</label>
            <input type="date" id="nueva_fecha" name="nueva_fecha" required>
            <label for="nuevo_motivo">Nuevo motivo:</label>
            <textarea id="nuevo_motivo" name="nuevo_motivo" rows="3" required></textarea>
            <button type="submit" name="modificar_cita">Modificar Cita</button>
         </form>
      </section>


      <!-- Formulario para eliminar una cita -->
      <section class="eliminar">
         <h2>Eliminar Cita</h2>
         <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
            <label for="cita_eliminar">Selecciona una cita:</label>
            <select id="cita_eliminar" name="id_cita_eliminar" required>
               <?php
               # Consulta sql para ver todas las citas 
               $sql_citas = "SELECT idCita, idUser, fechaCita, motivoCita FROM citas";
               $result_citas = $mysqli_connection->query($sql_citas);
               if ($result_citas->num_rows > 0) {
                  while ($row_cita = $result_citas->fetch_assoc()) {
                     # Ver el nombre del usuario en vez de su id
                     $sql_usuario = "SELECT nombre, apellidos FROM users_data WHERE idUser = " . $row_cita['idUser'];
                     $result_usuario = $mysqli_connection->query($sql_usuario);
                     $row_usuario = $result_usuario->fetch_assoc();
                     echo "<option value='" . $row_cita['idCita'] . "'>Cita con " . $row_usuario['nombre'] . " " . $row_usuario['apellidos'] . " - Fecha: " . $row_cita['fechaCita'] . "</option>";
                  }
               } else {
                  echo "<option value=''>No hay citas disponibles</option>";
               }
               ?>
            </select>
            <button type="submit" name="eliminar_cita">Eliminar Cita</button>
         </form>
      </section>

      <!-- Lista de citas existentes -->
      <section class="listado">
         <h2>Listado de Citas</h2>
         <table>
            <thead>
               <tr>
                  <th>Usuario</th>
                  <th>Fecha</th>
                  <th>Motivo</th>
               </tr>
            </thead>
            <tbody>
               <?php
               if ($result->num_rows > 0) {
                  while ($row = $result->fetch_assoc()) {
                     echo "<tr>";
                     
                     echo "<td>" . $row['nombre'] . " " . $row['apellidos'] . "</td>";
                     echo "<td>" . $row['fechaCita'] . "</td>";
                     echo "<td>" . $row['motivoCita'] . "</td>";
                     echo "</tr>";
                  }
               } else {
                  echo "<tr><td colspan='4'>No hay citas registradas.</td></tr>";
               }
               ?>
            </tbody>
         </table>
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
   </body>

</html>