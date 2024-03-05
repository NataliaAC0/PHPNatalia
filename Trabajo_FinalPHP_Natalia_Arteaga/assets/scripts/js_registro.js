document.addEventListener("DOMContentLoaded", function () {
    var formulario = document.getElementById("registro-form");

    //  Aquí voy a decidir que expresiones voy a utilizar para cada uno de los campos (el registro se valida con javascript y con php)
    var regexNombre = /^[A-Za-zÁáÉéÍíÓóÚúÜüÑñ\s]{3,25}$/; // mínimo 3 caracteres máximo 25
    var regexApellidos = /^[A-Za-zÁáÉéÍíÓóÚúÜüÑñ\s]{4,60}$/; // mínimo 4 caracteres máximo 60
    var regexEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/; // formato de email
    var regexTelefono = /^\d{9}$/; // 9 dígitos
    var regexFecha = /^\d{4}-\d{2}-\d{2}$/; // fecha 
    var regexDireccion = /^[\w\s]{5,50}$/; // De 5 a 50 caracteres, letras, números y espacios

    // Expresión regular para validar la contraseña
    var regexPassword = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*]).{6,}$/;
    // contraseña que contenga una letra minúscula, mayúscula, un número, un caracter de esta lista !@#$%^&* y una longitud mínima de 6 caracteres

    // Función para mostrar los mensajes de error
    function mostrarError(campo, errorElement, mensaje) {
        errorElement.textContent = mensaje;
        errorElement.style.display = "block";
    }

    // Función para ocultar los mensajes de error
    function ocultarError(errorElement) {
        errorElement.textContent = "";
        errorElement.style.display = "none";
    }

    // Función para validar el contenido de los campos
    function validarCampo(campo, regex, errorElement, mensaje) {
        if (!regex.test(campo.value)) {
            mostrarError(campo, errorElement, mensaje);
            return false;
        } else {
            ocultarError(errorElement);
            return true;
        }
    }

    // Función para validar la contraseña
    function validarPassword(password) {
        return regexPassword.test(password);
    }

    // Validación dinámica a la hora de escribir en los campos
    setTimeout(function () {
        formulario.addEventListener("input", function (event) {
            var campo = event.target;
            var errorElement = document.getElementById(campo.id + "-error");

            switch (campo.id) {
                case "nombre":
                    validarCampo(campo, regexNombre, errorElement, "El nombre es obligatorio y debe tener al menos 3 caracteres y un máximo de 25.");
                    break;
                case "apellidos":
                    validarCampo(campo, regexApellidos, errorElement, "Los apellidos son obligatorios y deben tener al menos 4 caracteres y un máximo de 60.");
                    break;
                case "email":
                    validarCampo(campo, regexEmail, errorElement, "El email es obligatorio y debe tener un formato válido.");
                    break;
                case "telefono":
                    validarCampo(campo, regexTelefono, errorElement, "El teléfono es obligatorio y debe tener 9 dígitos.");
                    break;
                case "fNac":
                    validarCampo(campo, regexFecha, errorElement, "La fecha de nacimiento es obligatoria.");
                    break;
                case "direccion":
                    validarCampo(campo, regexDireccion, errorElement, "La dirección es obligatoria y debe tener entre 5 y 50 caracteres.");
                    break;
                case "genero":
                    var generoError = document.getElementById("genero-error");
                    if (campo.value === "") {
                        mostrarError(campo, generoError, "Por favor, seleccione su género.");
                    } else {
                        ocultarError(generoError);
                    }
                    break;
                case "password":
                    if (!validarPassword(campo.value)) {
                        mostrarError(campo, errorElement, "La contraseña debe tener al menos 6 caracteres y contener al menos una letra mayúscula, una letra minúscula, un número y un carácter especial.");
                    } else {
                        ocultarError(errorElement);
                    }
                    break;
            }
        });
    }, 3000); 

    // Validación del formulario al enviar
    formulario.addEventListener("submit", function (event) {
        var nombre = document.getElementById("nombre");
        var apellidos = document.getElementById("apellidos");
        var email = document.getElementById("email");
        var telefono = document.getElementById("telefono");
        var fNac = document.getElementById("fNac");
        var direccion = document.getElementById("direccion");
        var genero = document.getElementById("genero");
        var acepto = document.getElementById("acepto");
        var password = document.getElementById("password");

        var nombreError = document.getElementById("nombre-error");
        var apellidosError = document.getElementById("apellidos-error");
        var emailError = document.getElementById("email-error");
        var telefonoError = document.getElementById("telefono-error");
        var fNacError = document.getElementById("fNac-error");
        var direccionError = document.getElementById("direccion-error");
        var generoError = document.getElementById("genero-error");
        var aceptoError = document.getElementById("acepto-error");
        var passwordError = document.getElementById("password-error");

        var formValido = true;

        // Validar campos
        if (!validarCampo(nombre, regexNombre, nombreError, "El nombre es obligatorio y debe tener al menos 3 caracteres y un máximo de 25.")) {
            formValido = false;
        }

        if (!validarCampo(apellidos, regexApellidos, apellidosError, "Los apellidos son obligatorios y deben tener al menos 4 caracteres y un máximo de 60.")) {
            formValido = false;
        }

        if (!validarCampo(email, regexEmail, emailError, "El email es obligatorio y debe tener un formato válido.")) {
            formValido = false;
        }

        if (!validarCampo(telefono, regexTelefono, telefonoError, "El teléfono es obligatorio y debe tener 9 dígitos.")) {
            formValido = false;
        }

        if (!validarCampo(fNac, regexFecha, fNacError, "La fecha de nacimiento es obligatoria.")) {
            formValido = false;
        }

        if (!validarCampo(direccion, regexDireccion, direccionError, "La dirección es obligatoria y debe tener entre 5 y 50 caracteres.")) {
            formValido = false;
        }

        if (genero.value === "") {
            mostrarError(genero, generoError, "Por favor, seleccione su género.");
            formValido = false;
        } else {
            ocultarError(generoError);
        }

        if (!acepto.checked) {
            mostrarError(acepto, aceptoError, "Debe aceptar los términos y condiciones para continuar.");
            formValido = false;
        } else {
            ocultarError(aceptoError);
        }

        if (!validarPassword(password.value)) {
            mostrarError(password, passwordError, "La contraseña debe tener al menos 6 caracteres, debe contener una letra minúscula, una mayúscula, un número y un carácter especial entre los siguientes (!@#$%^&*)");
            formValido = false;
        } else {
            ocultarError(passwordError);
        }

        if (!formValido) {
            // Si hay errores, el formulario no se enviará
            event.preventDefault(); 
        }
    });

    // Botón mostrar contraseña, cambia su visibilidad de ocultar contraseña a mostrar contraseña. Esto es para que el usuario pueda ver fácilmente que contraseña ha insertado. 
    var passwordInput = document.getElementById("password");
    var togglePasswordButton = document.getElementById("togglePassword");

    togglePasswordButton.addEventListener("click", function () {
        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            togglePasswordButton.textContent = "Ocultar contraseña";
        } else {
            passwordInput.type = "password";
            togglePasswordButton.textContent = "Mostrar contraseña";
        }
    });
});
