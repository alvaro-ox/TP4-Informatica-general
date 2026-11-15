<?php
// Plantilla parcial para mostrar la lista de errores de validacion en formularios
if (!empty($errores)): ?>
    <div class="mb-6 rounded border-2 border-rose-400 bg-rose-950 p-4 text-sm font-bold text-rose-100 shadow-[4px_4px_0px_#fb7185]">
        <ul class="list-disc list-inside space-y-1">
            <?php foreach ($errores as $error): ?>
                <li><?php echo limpiar_texto($error); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
