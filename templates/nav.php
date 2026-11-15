<?php
// Plantilla de barra de navegacion y mensajes de estado con estilo retro arcade
$pagina_actual = $pagina_activa ?? '';
$flash = obtener_mensaje_flash();

// Funcion auxiliar para resaltar el enlace activo en la barra de navegacion
function clase_enlace_nav(string $clave, string $actual): string
{
    $base = 'px-3 py-2 rounded border-2 text-xs font-bold uppercase tracking-wider transicion-suave ';
    if ($clave === $actual) {
        return $base . 'bg-cyan-400 text-zinc-950 border-cyan-200 shadow-[3px_3px_0px_#d946ef]';
    }
    return $base . 'bg-zinc-900 text-zinc-100 border-zinc-700 hover:border-cyan-400 hover:text-cyan-300';
}
?>
<header class="bg-zinc-900 border-b-4 border-fuchsia-500 shadow-[0_4px_0_#000]">
    <nav class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-wrap items-center justify-between gap-4 py-4">
            <a href="index.php" class="font-arcade text-sm sm:text-base tracking-wide text-amber-300 hover:text-cyan-300 transicion-suave">
                ARCADE // ABM
            </a>
            <div class="flex flex-wrap items-center gap-2.5">
                <a href="index.php" class="<?php echo clase_enlace_nav('jugadores', $pagina_actual); ?>">
                    Jugadores
                </a>
                <a href="alta.php" class="<?php echo clase_enlace_nav('alta_jugador', $pagina_actual); ?>">
                    Nuevo Jugador
                </a>
                <a href="puntajes.php" class="<?php echo clase_enlace_nav('puntajes', $pagina_actual); ?>">
                    Puntajes
                </a>
                <a href="puntajes_alta.php" class="<?php echo clase_enlace_nav('alta_puntaje', $pagina_actual); ?>">
                    Nuevo Puntaje
                </a>
            </div>
        </div>
    </nav>
</header>

<main class="flex-1 max-w-6xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <?php if ($flash !== null): ?>
        <?php
        $es_error = ($flash['tipo'] === 'error');
        $clases_alerta = $es_error
            ? 'bg-rose-950 border-2 border-rose-400 text-rose-100 shadow-[4px_4px_0px_#fb7185]'
            : 'bg-emerald-950 border-2 border-emerald-400 text-emerald-100 shadow-[4px_4px_0px_#34d399]';
        ?>
        <div class="mb-6 rounded px-4 py-3 text-sm font-bold <?php echo $clases_alerta; ?>" role="alert">
            <?php echo limpiar_texto($flash['texto']); ?>
        </div>
    <?php endif; ?>
