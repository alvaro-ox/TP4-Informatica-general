<?php
// Listado y ranking de puntajes con filtro por jugador y ordenamiento
require_once __DIR__ . '/conexion.php';

$pdo = obtener_conexion();

// Obtener lista de jugadores para el selector de filtro
$jugadores_lista = $pdo->query('SELECT id_jugador, nombre FROM jugadores ORDER BY nombre ASC')->fetchAll();

// Leer filtros de la URL
$filtro_jugador = filter_input(INPUT_GET, 'id_jugador', FILTER_VALIDATE_INT) ?: 0;
$orden = $_GET['orden'] ?? 'total';
if (!in_array($orden, ['total', 'fecha'], true)) {
    $orden = 'total';
}

// Construir consulta con JOIN a jugadores
$sql = 'SELECT p.id_puntaje, p.id_jugador, j.nombre AS nombre_jugador,
               p.puntaje_juego_1, p.puntaje_juego_2, p.puntaje_juego_3,
               p.total, p.fecha
        FROM puntajes p
        INNER JOIN jugadores j ON p.id_jugador = j.id_jugador';
$parametros = [];

if ($filtro_jugador > 0) {
    $sql .= ' WHERE p.id_jugador = :id_jugador';
    $parametros[':id_jugador'] = $filtro_jugador;
}

if ($orden === 'fecha') {
    $sql .= ' ORDER BY p.fecha DESC, p.total DESC';
} else {
    $sql .= ' ORDER BY p.total DESC, p.fecha DESC';
}

$stmt = $pdo->prepare($sql);
$stmt->execute($parametros);
$puntajes = $stmt->fetchAll();

$titulo_pagina = 'Listado y Ranking de Puntajes';
$pagina_activa = 'puntajes';
require_once __DIR__ . '/templates/header.php';
require_once __DIR__ . '/templates/nav.php';
?>

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="font-arcade text-xl sm:text-2xl text-amber-300 tracking-wide">PUNTAJES // RANKING</h1>
        <p class="text-sm text-zinc-300 mt-2">Registro de puntajes por juego, calculo automatico de total y clasificacion.</p>
    </div>
    <a href="puntajes_alta.php<?php echo $filtro_jugador > 0 ? '?id_jugador=' . (int) $filtro_jugador : ''; ?>" class="inline-flex items-center justify-center rounded border-2 border-fuchsia-300 bg-fuchsia-600 px-4 py-2.5 text-xs font-bold uppercase tracking-wider text-white shadow-[4px_4px_0px_#22d3ee] hover:bg-fuchsia-500 active:translate-x-[2px] active:translate-y-[2px] transicion-suave">
        + Nuevo Puntaje
    </a>
</div>

<!-- Filtros de jugador y ordenamiento -->
<form method="get" action="puntajes.php" class="mb-6 grid grid-cols-1 sm:grid-cols-3 gap-3">
    <select name="id_jugador" class="rounded border-2 border-zinc-600 bg-zinc-900 px-3 py-2.5 text-sm text-white focus:border-cyan-400 focus:outline-none">
        <option value="0">Todos los jugadores</option>
        <?php foreach ($jugadores_lista as $jug): ?>
            <option value="<?php echo (int) $jug['id_jugador']; ?>" <?php echo ((int) $jug['id_jugador'] === $filtro_jugador) ? 'selected' : ''; ?>>
                <?php echo limpiar_texto($jug['nombre']); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <select name="orden" class="rounded border-2 border-zinc-600 bg-zinc-900 px-3 py-2.5 text-sm text-white focus:border-cyan-400 focus:outline-none">
        <option value="total" <?php echo ($orden === 'total') ? 'selected' : ''; ?>>Ordenar por Ranking (Mayor Total)</option>
        <option value="fecha" <?php echo ($orden === 'fecha') ? 'selected' : ''; ?>>Ordenar por Fecha (Mas reciente)</option>
    </select>

    <div class="flex gap-2">
        <button type="submit" class="flex-1 rounded border-2 border-cyan-200 bg-cyan-400 px-4 py-2.5 text-xs font-bold uppercase tracking-wider text-zinc-950 shadow-[3px_3px_0px_#000] hover:bg-cyan-300 transicion-suave">
            Aplicar
        </button>
        <?php if ($filtro_jugador > 0 || $orden !== 'total'): ?>
            <a href="puntajes.php" class="rounded border-2 border-zinc-500 bg-zinc-800 px-4 py-2.5 text-xs font-bold uppercase tracking-wider text-zinc-100 hover:bg-zinc-700 transicion-suave">
                Limpiar
            </a>
        <?php endif; ?>
    </div>
</form>

<!-- Tabla de puntajes -->
<div class="overflow-x-auto rounded border-2 border-cyan-400 bg-zinc-900 shadow-[6px_6px_0px_#d946ef]">
    <table class="min-w-full divide-y-2 divide-zinc-700 text-sm">
        <thead class="bg-zinc-950 text-cyan-300 uppercase text-xs tracking-wider">
            <tr>
                <th class="px-4 py-3.5 text-left font-bold"><?php echo ($orden === 'total') ? 'Pos.' : 'ID'; ?></th>
                <th class="px-4 py-3.5 text-left font-bold">Jugador</th>
                <th class="px-4 py-3.5 text-right font-bold">Juego 1</th>
                <th class="px-4 py-3.5 text-right font-bold">Juego 2</th>
                <th class="px-4 py-3.5 text-right font-bold">Juego 3</th>
                <th class="px-4 py-3.5 text-right font-bold">Total</th>
                <th class="px-4 py-3.5 text-left font-bold">Fecha</th>
                <th class="px-4 py-3.5 text-right font-bold">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-zinc-800">
            <?php if (empty($puntajes)): ?>
                <tr>
                    <td colspan="8" class="px-4 py-8 text-center text-zinc-300">
                        No hay puntajes registrados para los criterios seleccionados.
                    </td>
                </tr>
            <?php else: ?>
                <?php $posicion = 1; ?>
                <?php foreach ($puntajes as $fila): ?>
                    <tr class="hover:bg-zinc-800/80 transicion-suave">
                        <td class="px-4 py-3.5 tabular-nums">
                            <?php if ($orden === 'total'): ?>
                                <span class="inline-flex items-center justify-center rounded border px-2 py-1 text-xs font-bold <?php echo ($posicion <= 3) ? 'border-amber-300 bg-amber-400 text-zinc-950' : 'border-zinc-600 bg-zinc-950 text-zinc-200'; ?>">
                                    #<?php echo $posicion; ?>
                                </span>
                            <?php else: ?>
                                <span class="text-amber-300 font-bold">#<?php echo (int) $fila['id_puntaje']; ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3.5 font-bold text-white">
                            <?php echo limpiar_texto($fila['nombre_jugador']); ?>
                        </td>
                        <td class="px-4 py-3.5 text-right text-zinc-200 tabular-nums"><?php echo number_format((int) $fila['puntaje_juego_1'], 0, ',', '.'); ?></td>
                        <td class="px-4 py-3.5 text-right text-zinc-200 tabular-nums"><?php echo number_format((int) $fila['puntaje_juego_2'], 0, ',', '.'); ?></td>
                        <td class="px-4 py-3.5 text-right text-zinc-200 tabular-nums"><?php echo number_format((int) $fila['puntaje_juego_3'], 0, ',', '.'); ?></td>
                        <td class="px-4 py-3.5 text-right font-bold text-amber-300 tabular-nums"><?php echo number_format((int) $fila['total'], 0, ',', '.'); ?></td>
                        <td class="px-4 py-3.5 text-zinc-200 tabular-nums"><?php echo limpiar_texto($fila['fecha']); ?></td>
                        <td class="px-4 py-3.5 text-right">
                            <div class="inline-flex items-center gap-2">
                                <a href="puntajes_editar.php?id=<?php echo (int) $fila['id_puntaje']; ?>" class="rounded border-2 border-amber-200 bg-amber-400 px-2.5 py-1 text-xs font-bold uppercase text-zinc-950 hover:bg-amber-300 transicion-suave">
                                    Editar
                                </a>
                                <form
                                    method="post"
                                    action="puntajes_eliminar.php"
                                    class="inline form-eliminar"
                                    data-confirmacion="¿Seguro que deseas eliminar este registro de puntaje de <?php echo limpiar_texto($fila['nombre_jugador']); ?>?"
                                >
                                    <input type="hidden" name="id_puntaje" value="<?php echo (int) $fila['id_puntaje']; ?>">
                                    <button type="submit" class="rounded border-2 border-rose-300 bg-rose-600 px-2.5 py-1 text-xs font-bold uppercase text-white hover:bg-rose-500 transicion-suave">
                                        Eliminar
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php $posicion++; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/templates/footer.php'; ?>
