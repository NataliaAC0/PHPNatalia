<?php
# Incluimos el archivo de conexión a la base de datos
require_once 'db_conn.php';

session_start(); // Iniciar sesión

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['username'], $_POST['password'])) {
   # Obtenemos el nombre de usuario y la contraseña del formulario
   $username = htmlspecialchars($_POST['username']);
   $password = htmlspecialchars($_POST['password']);

   # Realizamos una consulta SQL para obtener la información del usuario y el ID de inicio de sesión
   $sql = "SELECT ud.idUser, ud.nombre, ud.apellidos, ul.idLogin, ul.password, ul.rol 
           FROM users_data ud 
           INNER JOIN users_login ul ON ud.idUser = ul.idUser 
           WHERE ud.email = ?";
   
   if ($stmt = $mysqli_connection->prepare($sql)) {
       $stmt->bind_param("s", $username);
       $stmt->execute();
       $result = $stmt->get_result();
       
       if ($result->num_rows == 1) {
           # Obtenemos los datos del usuario
           $row = $result->fetch_assoc();
           $idUser = $row['idUser'];
           $nombre = $row['nombre'];
           $apellidos = $row['apellidos'];
           $hashed_password = $row['password'];
           $rol = $row['rol'];
           
           # Verificamos si la contraseña proporcionada coincide con la contraseña almacenada
           if (password_verify($password, $hashed_password)) {
               # Las credenciales son correctas, iniciamos la sesión del usuario
               $_SESSION['idUser'] = $idUser;
               $_SESSION['nombre'] = $nombre;
               $_SESSION['apellidos'] = $apellidos;
               $_SESSION['rol'] = $rol;
               
               # Redirigimos al usuario a la página de inicio
               header("Location: ../index.php");
               exit();
           } else {
               # Contraseña incorrecta, redirigimos con un mensaje de error
               $error_message = "Contraseña incorrecta";
               header("Location: ../views/login.php?error=$error_message");
               exit();
           }
       } else {
           # Usuario no encontrado, redirigimos con un mensaje de error
           $error_message = "Usuario no encontrado";
           header("Location: ../views/login.php?error=$error_message");
           exit();
       }
   } else {
       # Error en la preparación de la consulta SQL
       $error_message = "Error en la preparación de la consulta SQL";
       header("Location: ../views/login.php?error=$error_message");
       exit();
   }
}

# Cerramos la conexión a la base de datos
$mysqli_connection->close();
?>

