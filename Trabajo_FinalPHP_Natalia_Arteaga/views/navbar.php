<!-- BARRA NAVEGACIÓN -->
<div class="main-container">
    <nav class="navbar <?php echo isset($_SESSION['rol']) && $_SESSION['rol'] == 'admin' ? 'admin' : ''; ?>">
        <div class="container">
            <div class="logo">
                <a href="/Trabajo_FinalPHP_Natalia_Arteaga/index.php"><img src="/Trabajo_FinalPHP_Natalia_Arteaga/assets/images/VC.png" alt="Logo" height="150" width="84"></a>
            </div>
            <div class="menu-btn" id="menu-btn">
                <div class="bar"></div>
                <div class="bar"></div>
                <div class="bar"></div>
            </div>
            <ul class="navbar-items" id="navbar-items">
                <li><a href="/Trabajo_FinalPHP_Natalia_Arteaga/index.php">Inicio</a></li>
                <li><a href="/Trabajo_FinalPHP_Natalia_Arteaga/views/noticias.php">Noticias</a></li>
                <li><a href="/Trabajo_FinalPHP_Natalia_Arteaga/views/registro.php">Registro</a></li>
                <?php
                // Opciones para administradores
                if (isset($_SESSION['rol']) && $_SESSION['rol'] == 'admin') {
                    echo '<li><a href="/Trabajo_FinalPHP_Natalia_Arteaga/views/usuarios-administracion.php">Usuarios Admin</a></li>';
                    echo '<li><a href="/Trabajo_FinalPHP_Natalia_Arteaga/views/citaciones-administracion.php">Citaciones Admin</a></li>';
                    echo '<li><a href="/Trabajo_FinalPHP_Natalia_Arteaga/views/noticias-administracion.php">Noticias Admin</a></li>';
                    echo '<li><a href="/Trabajo_FinalPHP_Natalia_Arteaga/views/perfil.php">Perfil</a></li>';
                    echo '<li><a href="/Trabajo_FinalPHP_Natalia_Arteaga/views/cerrar_sesion.php">Cerrar Sesión</a></li>';
                }
                // Opciones para usuarios normales
                elseif (isset($_SESSION['rol']) && $_SESSION['rol'] == 'user') {
                    echo '<li><a href="/Trabajo_FinalPHP_Natalia_Arteaga/views/perfil.php">Perfil</a></li>';
                    echo '<li><a href="/Trabajo_FinalPHP_Natalia_Arteaga/views/citaciones.php">Citaciones</a></li>';
                    echo '<li><a href="/Trabajo_FinalPHP_Natalia_Arteaga/views/cerrar_sesion.php">Cerrar Sesión</a></li>';
                }
                // Si el usuario no está autenticado, mostrar opción de inicio de sesión
                else {
                    echo '<li><a href="/Trabajo_FinalPHP_Natalia_Arteaga/views/login.php">Inicio de Sesión</a></li>';
                }
                ?>
            </ul>
        </div>
    </nav>
</div>




