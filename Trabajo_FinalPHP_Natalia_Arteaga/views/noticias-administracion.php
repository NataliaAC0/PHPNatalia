<?php
session_start();

# Ver si el usuario ha iniciado sesión, si no es así, redirigir a login.php
if (!isset($_SESSION['idUser'])) {
   header("Location: login.php");
   exit();
}

# Id inicio de sesión 
$idLogin = $_SESSION['idUser'];
# Archivo conexión a la base de datos
include_once '../controllers/db_conn.php';

# Validar url de la imagen 
function validarURLImagen($url)
{
   # Permitir extensiones como jpg, jpeg y png
   $extensiones_permitidas = array('jpg', 'jpeg', 'png');
   $url_extension = pathinfo($url, PATHINFO_EXTENSION);
   return in_array($url_extension, $extensiones_permitidas);
}

# Crear una nueva noticia. Obtener datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['crear_noticia'])) {
   $titulo = $_POST['titulo'];
   $imagen = $_POST['imagen'];
   $texto = $_POST['texto'];
   $fecha = date("Y-m-d");

   # Validación de los datos, título al menos 20 caracteres, imagen con extensión, y texto de un mínimo de 50 caracteres
   if (strlen($titulo) < 20) {
      echo "<script>alert('El título debe tener al menos 20 caracteres.');</script>";
   } elseif (!validarURLImagen($imagen)) {
      echo "<script>alert('La URL de la imagen debe terminar en .jpg, .jpeg o .png.');</script>";
   } elseif (strlen($texto) < 50) {
      echo "<script>alert('El texto debe tener al menos 50 caracteres.');</script>";
   } else {

      # Consulta sql para insertar la noticia en la base de datos
      $sql_insert = "INSERT INTO noticias (titulo, imagen, texto, fecha, idUser) VALUES (?, ?, ?, ?, ?)";
      $stmt_insert = $mysqli_connection->prepare($sql_insert);
      if ($stmt_insert) {
         # Obtener el id del usuario para poner el nombre del autor
         $idUser = $_SESSION['idUser'];
         $stmt_insert->bind_param("ssssi", $titulo, $imagen, $texto, $fecha, $idUser);
         if ($stmt_insert->execute()) {
            echo "<script>alert('Noticia creada exitosamente.');</script>";
            echo "<meta http-equiv='refresh' content='0'>"; 
         } else {
            echo "<script>alert('Error al crear la noticia. Por favor, inténtalo de nuevo.');</script>";
         }
         $stmt_insert->close();
      } else {
         echo "<script>alert('Error en la preparación de la consulta para crear la noticia.');</script>";
      }
   }
}

# Modificar una noticia y obtener los datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['modificar_noticia'])) {
   // Obtener los datos del formulario
   $idNoticia = $_POST['id_noticia'];
   $titulo = $_POST['titulo'];
   $texto = $_POST['texto'];

   # Validación de los datos
   if (strlen($titulo) < 20) {
      echo "<script>alert('El título debe tener al menos 20 caracteres.');</script>";
   } elseif (strlen($texto) < 50) {
      echo "<script>alert('El texto debe tener al menos 50 caracteres.');</script>";
   } else {
      if ($_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
         $imagen_temporal = $_FILES['imagen']['tmp_name'];
         $imagen_nombre = $_FILES['imagen']['name'];

         $ruta_destino = __DIR__ . "/Trabajo_FinalPHP_Natalia_Arteaga/assets/images/noticias/" . $imagen_nombre;
         if (move_uploaded_file($imagen_temporal, $ruta_destino)) {
            # Actualizar la base de datos
            $sql_update = "UPDATE noticias SET titulo=?, imagen=?, texto=? WHERE idNoticia=?";
            $stmt_update = $mysqli_connection->prepare($sql_update);
            if ($stmt_update) {
               $stmt_update->bind_param("sssi", $titulo, $ruta_destino, $texto, $idNoticia);
               if ($stmt_update->execute()) {
                  echo "<script>alert('Noticia modificada exitosamente.');</script>";
                  echo "<meta http-equiv='refresh' content='0'>"; 
               } else {
                  echo "<script>alert('Error al modificar la noticia. Por favor, inténtalo de nuevo.');</script>";
               }
               $stmt_update->close();
            } else {
               echo "<script>alert('Error en la preparación de la consulta para modificar la noticia.');</script>";
            }
         } else {
            echo "<script>alert('Error al mover el archivo de imagen. Por favor, inténtalo de nuevo.');</script>";
         }
      } else {
         $sql_update = "UPDATE noticias SET titulo=?, texto=? WHERE idNoticia=?";
         $stmt_update = $mysqli_connection->prepare($sql_update);
         if ($stmt_update) {
            $stmt_update->bind_param("ssi", $titulo, $texto, $idNoticia);
            if ($stmt_update->execute()) {
               echo "<script>alert('Noticia modificada exitosamente.');</script>";
               echo "<meta http-equiv='refresh' content='0'>"; 
            } else {
               echo "<script>alert('Error al modificar la noticia. Por favor, inténtalo de nuevo.');</script>";
            }
            $stmt_update->close();
         } else {
            echo "<script>alert('Error en la preparación de la consulta para modificar la noticia.');</script>";
         }
      }
   }
}

# Eliminar una noticia
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['eliminar_noticia'])) {
   $idNoticia = $_POST['id_noticia'];

   # Consulta sql para eliminar la noticia 
   $sql_delete = "DELETE FROM noticias WHERE idNoticia=?";
   $stmt_delete = $mysqli_connection->prepare($sql_delete);
   if ($stmt_delete) {
      $stmt_delete->bind_param("i", $idNoticia);
      if ($stmt_delete->execute()) {
         echo "<script>alert('Noticia eliminada exitosamente.');</script>";
         echo "<meta http-equiv='refresh' content='0'>";
      } else {
         echo "<script>alert('Error al eliminar la noticia. Por favor, inténtalo de nuevo.');</script>";
      }
      $stmt_delete->close();
   } else {
      echo "<script>alert('Error en la preparación de la consulta para eliminar la noticia.');</script>";
   }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>VitalCare Medical Centre</title>
   <link rel="stylesheet" type="text/css" href="../assets/css/n_admin.css">
   <link rel="stylesheet" type="text/css" href="../assets/css/navbar.css">
   <script src="../assets/scripts/js_noticias.js"></script>
   <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
   <link href="https://fonts.googleapis.com/css2?family=Raleway:ital,wght@0,100..900;1,100..900&display=swap"
      rel="stylesheet">
</head>

<body>
   <!-- Incluir barra de navegación con php -->
   <?php include_once('navbar.php'); ?>
   <div class="espacio"></div>
   <section class="noticias-mysql">
      <?php
      # Consulta para obtener las noticias con el nombre y apellidos del autor
      $sql = "SELECT noticias.idNoticia, noticias.titulo, noticias.imagen, noticias.texto, noticias.fecha, users_data.nombre, users_data.apellidos FROM noticias INNER JOIN users_data ON noticias.idUser = users_data.idUser";
      $result = $mysqli_connection->query($sql);

      if ($result->num_rows > 0) {
         # Mostrar noticias
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
      ?>
   </section>

   <!-- Sección para crear noticias -->
   <section class="crear-noticia-section">
      <h2>Crear Noticia</h2>
      <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
         <label for="titulo">Título:</label>
         <input type="text" id="titulo" name="titulo" required>
         <label for="imagen">URL de la imagen:</label>
         <input type="text" id="imagen" name="imagen" placeholder="800px x 526px" required>
         <label for="texto">Texto:</label>
         <textarea id="texto" name="texto" rows="4" cols="50" required></textarea>
         <button type="submit" name="crear_noticia">Crear Noticia</button>
      </form>
   </section>

   <!-- Sección para modificar noticias -->
   <section class="modificar-noticia-section">
      <h2>Modificar Noticia</h2>
      <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post" enctype="multipart/form-data">
         <label for="noticia">Selecciona una noticia:</label>
         <select id="noticia" name="id_noticia" required>
            <?php
            # Obtener todas las noticias
            $sql = "SELECT idNoticia, titulo FROM noticias";
            $result = $mysqli_connection->query($sql);
            if ($result->num_rows > 0) {
               # Menú desplegable 
               while ($row = $result->fetch_assoc()) {
                  echo "<option value='" . $row['idNoticia'] . "'>" . $row['titulo'] . "</option>";
               }
            } else {
               echo "<option value=''>No hay noticias disponibles</option>";
            }
            ?>
         </select>
         <label for="titulo">Nuevo título:</label>
         <input type="text" id="titulo" name="titulo" required>
         <label for="texto">Nuevo texto:</label>
         <textarea id="texto" name="texto" rows="4" cols="50" required></textarea>
         <label for="imagen">URL de la imagen:</label>
         <input type="text" id="imagen" name="imagen" placeholder="800px x 526px" required>
         <button type="submit" name="modificar_noticia">Modificar Noticia</button>
      </form>
   </section>


   <!-- Sección para eliminar noticias -->
   <section class="eliminar-noticia-section">
      <h2>Eliminar Noticia</h2>
      <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
         <label for="noticia_eliminar">Selecciona una noticia:</label>
         <select id="noticia_eliminar" name="id_noticia" required>
            <?php
            # Obtener todas las noticias CONSULTA SQL SELECT 
            $sql = "SELECT idNoticia, titulo FROM noticias";
            $result = $mysqli_connection->query($sql);
            if ($result->num_rows > 0) {
              # Noticias en menú desplegable 
               while ($row = $result->fetch_assoc()) {
                  echo "<option value='" . $row['idNoticia'] . "'>" . $row['titulo'] . "</option>";
               }
            } else {
               echo "<option value=''>No hay noticias disponibles</option>";
            }
            ?>
         </select>
         <button type="submit" name="eliminar_noticia">Eliminar Noticia</button>
      </form>
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