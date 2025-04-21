<div class="contenedor login">

    <?php include_once __DIR__ . '/../templates/nombre-sitio.php'; ?>

    <div class="contenedor-sm">
        <p class="descripcion-pagina">Inicia sesión</p>

        <?php include_once __DIR__ . '/../templates/alertas.php'; ?>


        <form action="/" class="formulario" method="POST">
            <div class="campo">
                <label for="email">Email</label>
                <input 
                type="email"
                id="email"
                placeholder="Tu email"
                name="email"
                />
            </div>

            <div class="campo">
                <label for="password">Password</label>
                <input 
                type="password"
                id="password"
                placeholder="Tu Password"
                name="password"
                />
            </div>

            <input type="submit" class="boton" value="Iniciar Sesion">
        </form>
        <div class="acciones">
            <a href="/crear">¿Aun no tienes una Cuenta?, Crea una.</a>
            <a href="/olvide">¿Olvidaste Tu Password?</a>
        </div>
    </div> <!--contenedor-sm -->
</div>