<?php
// Listado principal de jugadores con busqueda por nombre y acceso al ABM
require_once __DIR__ . '/conexion.php';

$pdo = obtener_conexion();
$busqueda = trim($_GET['buscar'] ?? '');

// Consulta de jugadores junto con la cantidad de registros de puntajes asociados
$sql = 'SELECT j.id_jugador, j.nombre, j.alias, j.edad, j.fecha, COUNT(p.id_puntaje) AS cantidad_puntajes
        FROM jugadores j
        LEFT JOIN puntajes p ON j.id_jugador = p.id_jugador';
$parametros = [];

if ($busqueda !== '') {
    $sql .= ' WHERE j.nombre LIKE :busqueda OR j.alias LIKE :busqueda';
    $parametros[':busqueda'] = '%' . $busqueda . '%';
}

$sql .= ' GROUP BY j.id_jugador, j.nombre, j.alias, j.edad, j.fecha
          ORDER BY j.id_jugador DESC';

$stmt = $pdo->prepare($sql);
$stmt->execute($parametros);
$jugadores = $stmt->fetchAll();

$titulo_pagina = 'Listado de Jugadores';
$pagina_activa = 'jugadores';
require_once __DIR__ . '/templates/header.php';
require_once __DIR__ . '/templates/nav.php';
?>

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="font-arcade text-xl sm:text-2xl text-amber-300 tracking-wide">JUGADORES</h1>
        <p class="text-sm text-zinc-300 mt-2">Administracion de jugadores registrados en la sala arcade.</p>
    </div>
    <a href="alta.php" class="inline-flex items-center justify-center rounded border-2 border-fuchsia-300 bg-fuchsia-600 px-4 py-2.5 text-xs font-bold uppercase tracking-wider text-white shadow-[4px_4px_0px_#22d3ee] hover:bg-fuchsia-500 active:translate-x-[2px] active:translate-y-[2px] transicion-suave">
        + Nuevo Jugador
    </a>
</div>

<!-- Formulario de busqueda por nombre -->
<form method="get" action="index.php" class="mb-6 flex flex-col sm:flex-row gap-3">
    <input
        type="text"
        name="buscar"
        value="<?php echo limpiar_texto($busqueda); ?>"
        placeholder="Buscar jugador por nombre o alias..."
        class="flex-1 rounded border-2 border-zinc-600 bg-zinc-900 px-4 py-2.5 text-sm text-white placeholder-zinc-400 focus:border-cyan-400 focus:outline-none"
    >
    <div class="flex gap-2">
        <button type="submit" class="rounded border-2 border-cyan-200 bg-cyan-400 px-5 py-2.5 text-xs font-bold uppercase tracking-wider text-zinc-950 shadow-[3px_3px_0px_#000] hover:bg-cyan-300 transicion-suave">
            Buscar
        </button>
        <?php if ($busqueda !== ''): ?>
            <a href="index.php" class="rounded border-2 border-zinc-500 bg-zinc-800 px-4 py-2.5 text-xs font-bold uppercase tracking-wider text-zinc-100 hover:bg-zinc-700 transicion-suave">
                Limpiar
            </a>
        <?php endif; ?>
    </div>
</form>

<!-- Tabla de jugadores -->
<div class="overflow-x-auto rounded border-2 border-cyan-400 bg-zinc-900 shadow-[6px_6px_0px_#d946ef]">
    <table class="min-w-full divide-y-2 divide-zinc-700 text-sm">
        <thead class="bg-zinc-950 text-cyan-300 uppercase text-xs tracking-wider">
            <tr>
                <th class="px-4 py-3.5 text-left font-bold">ID</th>
                <th class="px-4 py-3.5 text-left font-bold">Alias</th>
                <th class="px-4 py-3.5 text-left font-bold">Nombre</th>
                <th class="px-4 py-3.5 text-left font-bold">Edad</th>
                <th class="px-4 py-3.5 text-left font-bold">Fecha</th>
                <th class="px-4 py-3.5 text-center font-bold">Puntajes</th>
                <th class="px-4 py-3.5 text-right font-bold">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-zinc-800">
            <?php if (empty($jugadores)): ?>
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-zinc-300">
                        No se encontraron jugadores registrados.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($jugadores as $jugador): ?>
                    <tr class="hover:bg-zinc-800/80 transicion-suave">
                        <td class="px-4 py-3.5 text-amber-300 font-bold tabular-nums">#<?php echo (int) $jugador['id_jugador']; ?></td>
                        <td class="px-4 py-3.5 font-bold text-cyan-300"><?php echo limpiar_texto($jugador['alias']); ?></td>
                        <td class="px-4 py-3.5 font-bold text-white"><?php echo limpiar_texto($jugador['nombre']); ?></td>
                        <td class="px-4 py-3.5 text-zinc-200 tabular-nums"><?php echo (int) $jugador['edad']; ?> anos</td>
                        <td class="px-4 py-3.5 text-zinc-200 tabular-nums"><?php echo limpiar_texto($jugador['fecha']); ?></td>
                        <td class="px-4 py-3.5 text-center">
                            <a href="puntajes.php?id_jugador=<?php echo (int) $jugador['id_jugador']; ?>" class="inline-flex items-center rounded border border-cyan-400 bg-zinc-950 px-3 py-1 text-xs font-bold text-cyan-300 hover:bg-cyan-400 hover:text-zinc-950 transicion-suave">
                                <?php echo (int) $jugador['cantidad_puntajes']; ?> registro(s)
                            </a>
                        </td>
                        <td class="px-4 py-3.5 text-right">
                            <div class="inline-flex items-center gap-2">
                                <a href="puntajes_alta.php?id_jugador=<?php echo (int) $jugador['id_jugador']; ?>" class="rounded border-2 border-emerald-200 bg-emerald-400 px-2.5 py-1 text-xs font-bold uppercase text-zinc-950 hover:bg-emerald-300 transicion-suave">
                                    + Puntaje
                                </a>
                                <a href="editar.php?id=<?php echo (int) $jugador['id_jugador']; ?>" class="rounded border-2 border-amber-200 bg-amber-400 px-2.5 py-1 text-xs font-bold uppercase text-zinc-950 hover:bg-amber-300 transicion-suave">
                                    Editar
                                </a>
                                <form
                                    method="post"
                                    action="eliminar.php"
                                    class="inline form-eliminar"
                                    data-confirmacion="¿Seguro que deseas eliminar al jugador <?php echo limpiar_texto($jugador['nombre']); ?>? Se eliminaran tambien todos sus puntajes asociados."
                                >
                                    <input type="hidden" name="id_jugador" value="<?php echo (int) $jugador['id_jugador']; ?>">
                                    <button type="submit" class="rounded border-2 border-rose-300 bg-rose-600 px-2.5 py-1 text-xs font-bold uppercase text-white hover:bg-rose-500 transicion-suave">
                                        Eliminar
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/templates/footer.php'; ?>
