<?php
# Incluimos el archivo de conexión a la base de datos
require_once 'db_conn.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['nombre'], $_POST['apellidos'], $_POST['email'], $_POST['telefono'], $_POST['direccion'], $_POST['sexo'], $_POST['password'], $_POST['fecha_nacimiento'])) {
    # Saneando los datos del formulario
    $nombre = htmlspecialchars($_POST['nombre']);
    $apellidos = htmlspecialchars($_POST['apellidos']);
    $email = htmlspecialchars($_POST['email']);
    $telefono = htmlspecialchars($_POST['telefono']);
    $direccion = htmlspecialchars($_POST['direccion']);
    $sexo = htmlspecialchars($_POST['sexo']);
    $password = htmlspecialchars($_POST['password']);
    $fecha_nacimiento = htmlspecialchars($_POST['fecha_nacimiento']);

    # Las validaciones de los formularios son importantes para que no nos llenen la base de datos de incoherencias. Por lo tanto, en esta sección voy a validar todos los campos creados en registro.php

    # Validación del nombre - Usamos strlen para obtener la longitud de una cadena de texto. Por lo tanto, en este caso es de 3 a 25 caracteres. 
    if (empty($nombre) || strlen($nombre) < 3 || strlen($nombre) > 25) {
        $_SESSION['nombre_error'] = "El campo nombre es obligatorio y debe contener entre 3 y 25 caracteres.";
        header("Location: ../views/registro.php");
        exit();
    }

    # Aquí valido los apellidos. Con lo mismo que he especificado anteriormente para ver la longitud de los caracteres.
    if (empty($apellidos) || strlen($apellidos) < 4 || strlen($nombre) > 50) {
        $_SESSION['apellidos_error'] = "El campo apellidos es obligatorio y debe contener entre 4 y 50 caracteres";
        header("Location: ../views/registro.php");
        exit();
    }

    # Validación del correo - en este caso es diferente ya que con poner filter_validate_email nos bastaría para que pudiese validar si se trata de un email correcto o no. 
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['email_error'] = "El correo electrónico es obligatorio y debe tener un formato válido.";
        header("Location: ../views/registro.php");
        exit();
    }

    # Validación del teléfono, usamos el preg match y una expresión regular que pida 9 digitos. 
    if (empty($telefono) || !preg_match("/^\d{9}$/", $telefono)) {
        $_SESSION['telefono_error'] = "El teléfono es obligatorio y debe contener 9 dígitos numéricos.";
        header("Location: ../views/registro.php");
        exit();
    }

     # Validación de la fecha de nacimiento
     $fecha_nacimiento_obj = strtotime($fecha_nacimiento);
     $fecha_actual = time();
     if (!$fecha_nacimiento_obj || $fecha_nacimiento_obj > $fecha_actual) {
         $_SESSION['fecha_nacimiento_error'] = "La fecha de nacimiento no es válida.";
         header("Location: ../views/registro.php");
         exit();
     }

    # validación de la dirección - entre 5 a 50 caracteres
    if (empty($direccion) || strlen($direccion) < 5 || strlen($direccion) > 50) {
        $_SESSION['direccion_error'] = "El campo direccion es obligatorio y debe contener entre 5 y 50 caracteres";
        header("Location: ../views/registro.php");
        exit();
    }

    # Validamos el género
    if (empty($sexo)) {
        $_SESSION['sexo_error'] = "Por favor, seleccione su género.";
        header("Location: ../views/registro.php");
        exit();
    }

    # Validación dde la contraseña. Creamos una expresión que pida que tenga 1 caracter en mayúsucla, otro en minúsucula, otro numérico y un caracter especial. Además, esta contraseña no podrá tener una longitud menor de 6 digítos. 
    if (strlen($password) < 6 || !preg_match("/[A-Z]/", $password) || !preg_match("/[a-z]/", $password) || !preg_match("/[0-9]/", $password) || !preg_match("/[!@#\$%\^\&*\)\(+=._-]/", $password)) {
        $_SESSION['password_error'] = "La contraseña debe tener al menos 6 caracteres y contener al menos una letra mayúscula, una letra minúscula, un número y un carácter especial.";
        header("Location: ../views/registro.php");
        exit();
    }

    # Validación de la aceptación de términos y condiciones
    if (!isset($_POST['acepto'])) {
        $_SESSION['acepto_error'] = "Debe aceptar los términos y condiciones para continuar.";
        header("Location: ../views/registro.php");
        exit();
    }

    # Para hacer el registro correctamente, hay que hacer una consulta sql donde pedimos que nos busque el correo electrónico en nuestra base de datos. Si existe, saltará un error diciendo que el correo ya está registrado y no permitirá crear más de una cuenta con el mismo correo electrónico. 
    $sql_check_email = "SELECT email FROM users_data WHERE email = ?";
    if ($stmt = $mysqli_connection->prepare($sql_check_email)) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $_SESSION['mensaje_error'] = "El correo electrónico ya está registrado. Por favor, utiliza otro.";
            header("Location: ../views/registro.php");
            exit();
        }
        $stmt->close();
    } else {
        $_SESSION['mensaje_error'] = "Error en la preparación de la consulta SQL";
        header("Location: ../views/registro.php");
        exit();
    }

    // Hasheamos la contraseña para que no entre en la base de datos como un campo numérico
    $contrasena_hash = password_hash($password, PASSWORD_DEFAULT);

    // Hacemos una consulta sql para insertar la información del usuario en la base de datos.
    $sql_users_data = "INSERT INTO users_data (nombre, apellidos, email, telefono, direccion, sexo, password, fecha_nacimiento) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    if ($stmt = $mysqli_connection->prepare($sql_users_data)) {
        $stmt->bind_param("ssssssss", $nombre, $apellidos, $email, $telefono, $direccion, $sexo, $contrasena_hash, $fecha_nacimiento);
        # Abrimos la ejecución para users_data
        if ($stmt->execute()) {
            # Obtenemos el ID del usuario insertado
            $idUser = $mysqli_connection->insert_id;
            # Cerramos la declaración para users_data
            $stmt->close();
            # Hacemos una consulta sql para insertar en users_login (esto es porque si no insertamos su contraseña y su id correctamente y su rol, no podremos iniciar sesión)
            $sql_users_login = "INSERT INTO users_login (idUser, password, rol) VALUES (?, ?, ?)";
            if ($stmt = $mysqli_connection->prepare($sql_users_login)) {
                # He establecido que cualquier persona visitante del sitio web que se registre mediante el formulario, tendrá su rol como user, que posteriormente, en la página de administradores se podrá cambiar.
                $rol = "user";
                $stmt->bind_param("iss", $idUser, $contrasena_hash, $rol);
                if ($stmt->execute()) {
                    $_SESSION['mensaje_exito'] = "El usuario se ha registrado correctamente";
                    # Si el usuario se ha registrado correctamente, redirigirá a login.php
                    header("Location: ../views/login.php");
                    exit();
                } else {
                    # Si no sucede, apareceráun mensaje de error y redirigirá de nuevo a registro.php para que vuelva a intentar registrarse el usuario. 
                    $_SESSION['mensaje_error'] = "Error al intentar registrar el usuario";
                    header("Location: ../views/registro.php");
                    exit();
                }
            } else {
                # Y si no, es que algo ha fallado en la consulta sql para users_login
                $_SESSION['mensaje_error'] = "Error en la preparación de la consulta SQL para users_login";
                header("Location: ../views/registro.php");
                exit();
            }
        } else {
            # Y si no, error al registrar al usuario en cuestión
            $_SESSION['mensaje_error'] = "Error al intentar registrar el usuario";
            header("Location: ../views/registro.php");
            exit();
        }
    } else {
        # Y si no, es que algo ha fallado en la consulta sql para users_data
        $_SESSION['mensaje_error'] = "Error en la preparación de la consulta SQL para users_data";
        header("Location: ../views/registro.php");
        exit();
    }
}
# Finalmente, cerramos la conexión a la base de datos 
$mysqli_connection->close();
?>
