<?php
# Acceso a la base de datos
const SERVER_HOST = 'localhost';
const DATABASE_NAME = 'centro_medico';
const USER = 'root';
const PASSWORD = '';

# Definimos una función para realizar la conexión a la BBDD 
function connectToDatabase() {
   static $mysqli_conn = null; 
   if ($mysqli_conn === null) {
      try {
         # Crear la conexión a la BBDD 
         $mysqli_conn = new mysqli(SERVER_HOST, USER, PASSWORD, DATABASE_NAME);
         # Comprobar si ha funcionado correctamente 
         if($mysqli_conn -> connect_error) {
            # Registrar el error en el archivo log 
            error_log("Fallo al conectar a la base de datos : " . $mysqli_conn->connect_error);
            return null;
         } else {
            return $mysqli_conn; // Devolver la conexión establecida
         }
      } catch (Exception $e) {
         # Registrar la excepción en el archivo log 
         error_log("Error de conexión a la base de datos : " . $e->getMessage());
         return null;
      }
   }
   return $mysqli_conn;
}

# Conectarse a la base de datos
$mysqli_connection = connectToDatabase(); 

# Si la conexión es nula, redirigir
if ($mysqli_connection === null) {
   header('Location: ../views/errors/index.php');
}
?>
