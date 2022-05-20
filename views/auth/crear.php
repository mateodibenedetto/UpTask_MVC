<div class="contenedor crear">

<?php include_once __DIR__ . '/../templates/nombre-sitio.php'; ?>

    <div class="contenedor-sm">
        <p class="descripcion-pagina">Crea tu cuenta en UpTask</p>

        <?php include_once __DIR__ . '/../templates/alertas.php'; ?>

        <form method="POST" class="formulario" action="/crear">
            <div class="campo">
                <label for="nombre">Nombre</label>
                <input 
                    type="nombre"
                    id="nombre"
                    placeholder="Tu nombre"
                    name="nombre"
                    spellcheck="false"
                    value="<?php echo $usuario->nombre; ?>"
                />
            </div>

            <div class="campo">
                <label for="email">Email</label>
                <input 
                    type="email"
                    id="email"
                    placeholder="Tu Email"
                    name="email"
                    spellcheck="false"
                    value="<?php echo $usuario->email; ?>"
                />
            </div>

            <div class="campo">
                <label for="password">Contraseña</label>
                <input 
                    type="password"
                    id="password"
                    placeholder="Tu Contraseña"
                    name="password"
                />
            </div>

            <div class="campo">
                <label for="password2">Repetir Contraseña</label>
                <input 
                    type="password"
                    id="password2"
                    placeholder="Repite tu Contraseña"
                    name="password2"
                />
            </div>

            <input type="submit" class="boton" value="Crear Cuenta">
            <div class="contact-form-loader none">
                <img src="assets/loader.svg" alt="Cargando">
            </div>
        </form>

        <div class="acciones">
            <a href="/">¿Ya tienes cuenta? Iniciar Sesión</a>
            <a href="/olvide">¿Olvidaste tu Contraseña?</a>
        </div>
    </div> <!--.contenedor-sm-->
</div> <!--.contenedor-->