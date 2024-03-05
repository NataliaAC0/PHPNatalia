<?php
# Archivo conexión a la base de datos
include_once 'db_conn.php';

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
      $error_titulo = "El título debe tener al menos 20 caracteres.";
   } elseif (!validarURLImagen($imagen)) {
      $error_imagen = "La URL de la imagen debe terminar en .jpg, .jpeg o .png.";
   } elseif (strlen($texto) < 50) {
      $error_texto = "El texto debe tener al menos 50 caracteres.";
   } else {
      # Consulta sql para insertar la noticia en la base de datos
      $sql_insert = "INSERT INTO noticias (titulo, imagen, texto, fecha, idUser) VALUES (?, ?, ?, ?, ?)";
      $stmt_insert = $mysqli_connection->prepare($sql_insert);
      if ($stmt_insert) {
         # Obtener el id del usuario para poner el nombre del autor
         $idUser = $_SESSION['user_id'];
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
      $error_titulo = "El título debe tener al menos 20 caracteres.";
   } elseif (!validarURLImagen($imagen)) {
      $error_imagen = "La URL de la imagen debe terminar en .jpg, .jpeg o .png.";
   } elseif (strlen($texto) < 50) {
      $error_texto = "El texto debe tener al menos 50 caracteres.";
   }else {
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
