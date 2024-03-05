<?php 
# Vinculamos la ruta absoluta a directorio config.php 
require_once __DIR__ . '/../config/config.php';

/**
 * Datos a tener en cuenta para comprobar si un usuario existe en la BBDD:
 *
 * @param string $email El email del usuario a comprobar.
 * @param mysqli $mysqli_connection La conexión a la base de datos.
 * @return bool Verdadero si el usuario existe, falso en caso contrario.
 **/

function check_user($email, $mysqli_connection, &$exception_error){
   # Declaramos la sentencia $select_stmt como nula y luego trabajaremos sobre ella para prevenir errores y gestionar de forma más correcta la gestion de excepciones
   $select_stmt = null;
   # Se inicia la gestión del control de excepciones 
   try {
      # Preparar la sentencia para buscar el email en la BBDD
      $select_stmt = $mysqli_connection->prepare('SELECT email FROM users_data WHERE email = ?');
      # Comprobamos si la sentencia se ha podido preparar correctamente
      if ($select_stmt === false) {
         error_log("No se pudo preparar la sentencia: " . $mysqli_connection->error);
         $exception_error = true;
         return false;
     }
     # Vinculamos el email a la sentencia
     $select_stmt->bind_param("s", $email);
     # Comprobar si se puede ejecutar la sentencia una vez preparada y se ejecuta
     if (!$select_stmt->execute()) {
         error_log("No se pudo ejecutar la sentencia: " . $select_stmt->error);
         $exception_error = true;
         return false;
     }
     # Guardamos el resulado de la sentencia tras su ejecución
     $select_stmt->store_result();
     # Se devuelve como resultado de la función un valor booleano
     # true si se ha encontrado que el usuario existe
     # false si no se ha encontrado el usuario en la BBDD
     return $select_stmt->num_rows > 0;
 } catch(Exception $e) {
     error_log("Error en la función check_user: " . $e->getMessage());
     $exception_error = true;
     return false;
 } finally {
     if ($select_stmt !== null) {
         $select_stmt->close();
     }
 }
}

function get_user_by_email($email, $mysqli_connection, &$exception_error){
 # Inicializar la sentencia de selección como nula
 $select_stmt = null;
 # Inicializamos la variable de error asumiendo que inicialmente no hay ningún error
 #$exception_error = false;


 try{
     # Preparar la sentencia SQL necesaria para buscar al usuario a través su correo electrónico
     $query = "SELECT * FROM users_data WHERE email = ? LIMIT 1";
     $select_stmt = $mysqli_connection -> prepare($query);

     if($select_stmt === false){
         error_log("No se pudo preparar la sentencia " . $mysqli_connection -> error);
         $exception_error = true;
         return false;
     }

     # Vincular el correo electrónico a la sentencia
     $select_stmt -> bind_param('s', $email);

     # Intentar ejecutar la sentencia de selección
     if(!$select_stmt -> execute()){
         error_log("No se puede ejecutar la sentencia " . $mysqli_connection -> error);
         $exception_error = true;
         return false;
     }

     # Obtener el resultado de la consulta
     $result = $select_stmt -> get_result();

     if($result -> num_rows > 0){
         $user = $result -> fetch_assoc(); 
        
         return $user;
     }else{
         // Si no se encuentra el usuario o no existe
         return false;
     }

 }catch(Exception $e){
     error_log("Error al ejecutar la función get_user_by_email(): " . $e -> getMessage());
     $exception_error = true;
     return false;

 }finally{
     // Nos aseguramos de cerrar la sentencia si existe
     if($select_stmt !== null){
         $select_stmt -> close();
     }
 }
}
?>