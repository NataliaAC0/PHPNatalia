<?php
# Ver si hay una sesión activa
session_start();

# Archivo de conexión a la base de datos
include_once '../controllers/db_conn.php';

# Verificar si el usuario ha iniciado sesión, si no es así, redirigir a login.php
if (!isset($_SESSION['idUser'])) {
   header("Location: login.php");
   exit();
}

# Id inicio de sesión 
$idLogin = $_SESSION['idUser'];

# Comprobar si el formulario se ha enviado
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['modificar'])) {
   $fecha = $_POST['fecha'];
   $motivo = $_POST['motivo'];
   $idCita = $_POST['idCita'];
   $sql_update = "UPDATE citas SET fechaCita = ?, motivoCita = ? WHERE idCita = ?";
   $stmt_update = $mysqli_connection->prepare($sql_update);
   if ($stmt_update) {
      $stmt_update->bind_param("ssi", $fecha, $motivo, $idCita);
      if ($stmt_update->execute()) {
         echo "<script>alert('Cita modificada exitosamente.');</script>";
         echo "<meta http-equiv='refresh' content='0'>";
      } else {
         echo "<script>alert('Error al modificar la cita. Por favor, inténtalo de nuevo.');</script>";
      }
      $stmt_update->close();
   } else {
      echo "<script>alert('Error en la preparación de la consulta de modificación.');</script>";
   }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>VitalCare Medical Centre</title>
   <link rel="stylesheet" type="text/css" href="../assets/css/citas.css">
   <link rel="stylesheet" type="text/css" href="../assets/css/navbar.css">
   <link rel="preconnect" href="https://fonts.googleapis.com">
   <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
   <link href="https://fonts.googleapis.com/css2?family=Raleway:ital,wght@0,100..900;1,100..900&display=swap"
      rel="stylesheet">
</head>

<body>
   <!-- Incluir la barra de navegación con php -->
   <?php include_once('navbar.php'); ?>
   <div class="espacio"></div>
   <!-- Sección para solicitar citas -->
   <section class="citas">
      <h2>Solicitar Cita</h2>
      <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post" onsubmit="return validarFecha()">
         <div class="form-group">
            <label for="fecha">Fecha:</label>
            <input type="date" id="fecha" name="fecha" min="<?php echo date("Y-m-d"); ?>" required>
         </div>
         <div class="form-group">
            <label for="motivo">Motivo:</label>
            <textarea id="motivo" name="motivo" rows="4" cols="50" required minlength="2"></textarea>
         </div>
         <button type="submit" name="solicitar_cita">Enviar Cita</button>
      </form>
      <?php
      # Comprueba si se ha enviado y si se ha procesado la cita
      if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['solicitar_cita'])) {
         # Datos del formulario
         $fecha = $_POST['fecha'];
         $motivo = $_POST['motivo'];

         # Ver que sesión está activa y por lo tanto, su id
         if (isset($_SESSION['idUser'])) {
            $idUser = $_SESSION['idUser'];


            # Validar que la fecha no sea anterior a hoy
            $hoy = date("Y-m-d");
            if ($fecha < $hoy) {
               echo "<script>alert('No puedes seleccionar una fecha anterior al día de hoy.');</script>";
            } elseif (empty($motivo)) {
               echo "<script>alert('El campo del motivo no puede estar vacío.');</script>";
            } else {
               # Consulta sql para insertar la cita.
               $sql = "INSERT INTO citas (idUser, fechaCita, motivoCita) VALUES (?, ?, ?)";

               $stmt = $mysqli_connection->prepare($sql);
               # Comprobar si la cita se ha hecho correctamente. Si es así, manda un script y sino un script que será un error. 
               if ($stmt) {
                  $stmt->bind_param("iss", $idUser, $fecha, $motivo);
                  if ($stmt->execute()) {
                     echo "<script>alert('Cita solicitada con éxito.');</script>";
                  } else {
                     echo "<script>alert('Error al solicitar la cita. Por favor, inténtalo de nuevo.');</script>";
                  }
                  $stmt->close();
               } else {
                  # Si no, error en la preparación de la consulta
                  echo "<script>alert('Error en la preparación de la consulta.');</script>";
               }
            }
         } else {
            # La sesión no se ha inciado.
            echo "<script>alert('Error: La sesión no está iniciada.');</script>";
         }
      }
      ?>
   </section>


   <!-- Sección para modificar citas planificadas -->
   <section class="citas-planificadas">
      <h2>Modificar Citas Planificadas</h2>
      <table>
         <tr>
            <th>Fecha</th>
            <th>Motivo</th>
            <th>Acciones</th>
         </tr>
         <?php
         # Consulta SQL para ver las citas que tiene el usuario en cuestión.
         # Ver las citas del usuario actual
         $sql = "SELECT idCita, fechaCita, motivoCita FROM citas WHERE idUser = ?";
         $stmt = $mysqli_connection->prepare($sql);
         if ($stmt) {
            $stmt->bind_param("i", $_SESSION['idUser']); // Aquí se debe usar $_SESSION['idUser']
            $stmt->execute();
            $result = $stmt->get_result();
            # Ver si hay citas para mostrar
            if ($result->num_rows > 0) {
               while ($row = $result->fetch_assoc()) {
                  # Aquí hacemos una validación para que no se pueda modificar ninguna cita con menos de 24 horas. 
                  $fechaCita = strtotime($row['fechaCita']);
                  $limiteModificacion = strtotime('-24 hours');
                  if ($fechaCita > $limiteModificacion) {
                     echo "<tr>";
                     echo "<td>" . $row['fechaCita'] . "</td>";
                     echo "<td>" . $row['motivoCita'] . "</td>";
                     echo "<td>";
                     echo "<form action='" . htmlspecialchars($_SERVER["PHP_SELF"]) . "' method='post'>";
                     echo "<input type='hidden' name='idCita' value='" . $row['idCita'] . "'>";
                     echo "<input type='date' name='fecha' value='" . $row['fechaCita'] . "' min='" . date("Y-m-d") . "' required>";
                     echo "<textarea name='motivo' rows='4' cols='30' required minlength='2'>" . $row['motivoCita'] . "</textarea>";
                     echo "<button type='submit' name='modificar'>Modificar</button>";
                     echo "</form>";
                     echo "</td>";
                     echo "</tr>";
                  }
               }
            } else {
               echo "<tr><td colspan='3'>No hay citas planificadas.</td></tr>";
            }
            $stmt->close();
         } else {
            echo "<tr><td colspan='3'>Error en la preparación de la consulta.</td></tr>";
         }

         ?>
      </table>
   </section>

   <!-- Sección para eliminar citas planificadas -->
   <section class="citas-eliminar">
      <h2>Eliminar Citas Planificadas</h2>
      <table>
         <tr>
            <th>Fecha</th>
            <th>Motivo</th>
            <th>Acciones</th>
         </tr>
         <?php
         # Ver las citas del usuario actual
         $sql = "SELECT idCita, fechaCita, motivoCita FROM citas WHERE idUser = ?";
         $stmt = $mysqli_connection->prepare($sql);
         if ($stmt) {
            $stmt->bind_param("i", $_SESSION['idUser']);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
               while ($row = $result->fetch_assoc()) {
                  # Aquí validamos si el usuario puede cancelar la cita ya que he puesto el limiteeliminacion para que no se pueda realizar con menos de 24h de antelacion.
                  $fechaCita = strtotime($row['fechaCita']);
                  $limiteEliminacion = strtotime('+24 hours');
                  if ($fechaCita > $limiteEliminacion) {
                     echo "<tr>";
                     echo "<td>" . $row['fechaCita'] . "</td>";
                     echo "<td>" . $row['motivoCita'] . "</td>";
                     echo "<td>";
                     echo "<form action='" . htmlspecialchars($_SERVER["PHP_SELF"]) . "' method='post'>";
                     echo "<input type='hidden' name='idCita' value='" . $row['idCita'] . "'>";
                     echo "<button type='submit' name='eliminar'>Eliminar</button>";
                     echo "</form>";
                     echo "</td>";
                     echo "</tr>";
                  }
               }
            } else {
               echo "<tr><td colspan='3'>No hay citas planificadas.</td></tr>";
            }
            $stmt->close();
         } else {
            echo "<tr><td colspan='3'>Error en la preparación de la consulta.</td></tr>";
         }

         # Eliminación, Manda una alerta si ha sido posible y si no ha sido posible, también
         if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['eliminar'])) {
            $idCita = $_POST['idCita'];
            $sql_delete = "DELETE FROM citas WHERE idCita = ?";
            $stmt_delete = $mysqli_connection->prepare($sql_delete);
            if ($stmt_delete) {
               $stmt_delete->bind_param("i", $idCita);
               if ($stmt_delete->execute()) {
                  echo "<script>alert('Cita eliminada exitosamente.');</script>";
                  echo "<meta http-equiv='refresh' content='0'>";
               } else {
                  echo "<script>alert('Error al eliminar la cita. Por favor, inténtalo de nuevo.');</script>";
               }
               $stmt_delete->close();
            } else {
               echo "<script>alert('Error en la preparación de la consulta de eliminación.');</script>";
            }
         }
         ?>
      </table>
   </section>

   <!-- Validación de fecha -->
   <script>
      function validarFecha() {
         var fechaSeleccionada = new Date(document.getElementById("fecha").value);
         var hoy = new Date();
         var limite = new Date(hoy);
         limite.setDate(limite.getDate() + 1); // Añadir un día para permitir la cita del día siguiente
         if (fechaSeleccionada < limite) {
            alert("Debes seleccionar una fecha para la cita que sea al menos 24 horas después del día de hoy.");
            return false;
         }
         return true;
      }
   </script>


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