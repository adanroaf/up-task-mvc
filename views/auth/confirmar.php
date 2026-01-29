<div class="contenedor confirmar">
    <?php include_once __DIR__ . '/../templates/nombre-sitio.php'; ?>

    <div class="contenedor-sm">
    <?php include_once __DIR__ . '/../templates/alertas.php'; ?>
    <?php if (empty($alertas['error'])) : ?>
        <div class="acciones">
            <a href="/">Iniciar Sesión</a>
        </div>
    <?php endif; ?>
        
    </div><!-- fin div contenedor-sm -->
</div><!-- fin div contenedor -->