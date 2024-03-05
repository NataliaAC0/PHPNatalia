<?php


# Declaramos como constantes las expresiones regulares que van a filtrar o comprobar los datos
define("NOMBRE_REGEX", "/^[a-zA-Z ]{2,45}$/");
define("CONTRASENA_REGEX", "/^(?=.*[A-Z])(?=.*\d)(?=.*[.,_\-])[a-zA-Z\d.,_\-]{4,10}$/");


# Definimos la función validar_registro()
function validar_registro($nombre, $correo, $contrasena){
    # Declarar un array asociativo
    $errores = [];


    # Validación del nombre haciendo uso de la constante NOMBRE_REGEX
    if(!preg_match(NOMBRE_REGEX, $nombre)){
        $errores['nombre'] = "- El nombre deberá contener entre 2 y 45 letras y se podrá hacer uso de un único espacio en caso de introducir un nombre compuesto";
    }


    # Validación del correo electrónico
    if(!filter_var($correo, FILTER_VALIDATE_EMAIL)){
        $errores['correo'] = "- El formato del correo electrónico no es válido";
    }


    # Validación de la contraseña haciendo uso de la constante CONTRASENA_REGEX
    if(!preg_match(CONTRASENA_REGEX, $contrasena)){
        $errores['constrasena'] = "- La contraseña deberá contener entre 4 y 10 caracteres e incluir de forma obligatoria una letra mayúscula, un número y un símbolo entre los siguientes (.,_-)";
    }


    return $errores;
}

# Definimos la función validar_login()
function validar_login($correo, $contrasena){
    # Declarar un array asociativo
    $errores = [];

    # Validación del correo electrónico
    if(!filter_var($correo, FILTER_VALIDATE_EMAIL)){
        $errores['correo'] = "- El formato del correo electrónico no es válido";
    }

    # Validación de la contraseña haciendo uso de la constante CONTRASENA_REGEX
    if(!preg_match(CONTRASENA_REGEX, $contrasena)){
        $errores['constrasena'] = "- La contraseña deberá contener entre 4 y 10 caracteres e incluir de forma obligatoria una letra mayúscula, un número y un símbolo entre los siguientes (.,_-)";
    }

    # Verificar si hay errores en la autenticación
    if (isset($_SESSION['auth_error'])) {
        $errores['general'] = $_SESSION['auth_error'];
        unset($_SESSION['auth_error']); // Limpiar el mensaje de error de autenticación después de usarlo
    }

    return $errores;
}
?>
