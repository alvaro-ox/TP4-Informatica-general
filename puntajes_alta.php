<?php
// Formulario y procesamiento de alta de puntaje para un jugador
require_once __DIR__ . '/conexion.php';

$pdo = obtener_conexion();
$jugadores = $pdo->query('SELECT id_jugador, nombre FROM jugadores ORDER BY nombre ASC')->fetchAll();

$errores = [];
$id_jugador = filter_input(INPUT_GET, 'id_jugador', FILTER_VALIDATE_INT) ?: 0;
$p1 = '0';
$p2 = '0';
$p3 = '0';
$fecha = date('Y-m-d');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $resultado = validar_datos_puntaje($pdo, $_POST);
    $errores = $resultado['errores'];
    $id_jugador = $resultado['datos']['id_jugador'];
    $p1 = $resultado['datos']['p1'];
    $p2 = $resultado['datos']['p2'];
    $p3 = $resultado['datos']['p3'];
    $fecha = $resultado['datos']['fecha'];

    if (empty($errores)) {
        $total = $resultado['datos']['total'];

        $stmt = $pdo->prepare(
            'INSERT INTO puntajes (id_jugador, puntaje_juego_1, puntaje_juego_2, puntaje_juego_3, total, fecha)
             VALUES (:id_jugador, :p1, :p2, :p3, :total, :fecha)'
        );
        $stmt->execute([
            ':id_jugador' => $id_jugador,
            ':p1'         => $resultado['datos']['v1'],
            ':p2'         => $resultado['datos']['v2'],
            ':p3'         => $resultado['datos']['v3'],
            ':total'      => $total,
            ':fecha'      => $fecha,
        ]);

        establecer_mensaje_flash('exito', 'Puntaje registrado correctamente (Total: ' . $total . ').');
        redirigir('puntajes.php');
    }
}

$titulo_pagina = 'Nuevo Puntaje';
$pagina_activa = 'alta_puntaje';
require_once __DIR__ . '/templates/header.php';
require_once __DIR__ . '/templates/nav.php';
?>

<div class="max-w-xl mx-auto">
    <div class="mb-6">
        <h1 class="font-arcade text-xl text-amber-300 tracking-wide">NUEVO PUNTAJE</h1>
        <p class="text-sm text-zinc-300 mt-2">Carga los puntajes de los tres juegos; el total se calcula automaticamente.</p>
    </div>

    <?php if (empty($jugadores)): ?>
        <div class="rounded border-2 border-amber-400 bg-amber-950 p-6 text-sm font-bold text-amber-100 shadow-[4px_4px_0px_#fbbf24]">
            No hay jugadores registrados todavia. Primero debes <a href="alta.php" class="underline text-cyan-300">dar de alta un jugador</a> antes de cargar puntajes.
        </div>
    <?php else: ?>
        <?php require __DIR__ . '/templates/errores.php'; ?>

        <form method="post" action="puntajes_alta.php" class="rounded border-2 border-cyan-400 bg-zinc-900 p-6 space-y-5 shadow-[6px_6px_0px_#d946ef]">
            <div>
                <label for="id_jugador" class="block text-xs font-bold uppercase tracking-wider text-cyan-300 mb-1.5">Jugador</label>
                <select
                    id="id_jugador"
                    name="id_jugador"
                    required
                    class="w-full rounded border-2 border-zinc-600 bg-zinc-950 px-3 py-2.5 text-sm text-white focus:border-cyan-400 focus:outline-none"
                >
                    <option value="">Selecciona un jugador...</option>
                    <?php foreach ($jugadores as $jug): ?>
                        <option value="<?php echo (int) $jug['id_jugador']; ?>" <?php echo ((int) $jug['id_jugador'] === $id_jugador) ? 'selected' : ''; ?>>
                            <?php echo limpiar_texto($jug['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="puntaje_juego_1" class="block text-xs font-bold uppercase tracking-wider text-cyan-300 mb-1.5">Juego 1</label>
                    <input
                        type="number"
                        id="puntaje_juego_1"
                        name="puntaje_juego_1"
                        min="0"
                        required
                        value="<?php echo limpiar_texto($p1); ?>"
                        class="w-full rounded border-2 border-zinc-600 bg-zinc-950 px-3 py-2.5 text-sm text-white focus:border-cyan-400 focus:outline-none"
                    >
                </div>

                <div>
                    <label for="puntaje_juego_2" class="block text-xs font-bold uppercase tracking-wider text-cyan-300 mb-1.5">Juego 2</label>
                    <input
                        type="number"
                        id="puntaje_juego_2"
                        name="puntaje_juego_2"
                        min="0"
                        required
                        value="<?php echo limpiar_texto($p2); ?>"
                        class="w-full rounded border-2 border-zinc-600 bg-zinc-950 px-3 py-2.5 text-sm text-white focus:border-cyan-400 focus:outline-none"
                    >
                </div>

                <div>
                    <label for="puntaje_juego_3" class="block text-xs font-bold uppercase tracking-wider text-cyan-300 mb-1.5">Juego 3</label>
                    <input
                        type="number"
                        id="puntaje_juego_3"
                        name="puntaje_juego_3"
                        min="0"
                        required
                        value="<?php echo limpiar_texto($p3); ?>"
                        class="w-full rounded border-2 border-zinc-600 bg-zinc-950 px-3 py-2.5 text-sm text-white focus:border-cyan-400 focus:outline-none"
                    >
                </div>
            </div>

            <div class="rounded border-2 border-amber-400 bg-zinc-950 px-4 py-3 flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-zinc-200">Total calculado:</span>
                <span id="total_preview" class="font-arcade text-lg text-amber-300 tabular-nums">0</span>
            </div>

            <div>
                <label for="fecha" class="block text-xs font-bold uppercase tracking-wider text-cyan-300 mb-1.5">Fecha</label>
                <input
                    type="date"
                    id="fecha"
                    name="fecha"
                    required
                    value="<?php echo limpiar_texto($fecha); ?>"
                    class="w-full rounded border-2 border-zinc-600 bg-zinc-950 px-3 py-2.5 text-sm text-white focus:border-cyan-400 focus:outline-none"
                >
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="puntajes.php" class="rounded border-2 border-zinc-500 bg-zinc-800 px-4 py-2.5 text-xs font-bold uppercase tracking-wider text-zinc-100 hover:bg-zinc-700 transicion-suave">
                    Cancelar
                </a>
                <button type="submit" class="rounded border-2 border-fuchsia-300 bg-fuchsia-600 px-5 py-2.5 text-xs font-bold uppercase tracking-wider text-white shadow-[3px_3px_0px_#22d3ee] hover:bg-fuchsia-500 transicion-suave">
                    Guardar Puntaje
                </button>
            </div>
        </form>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/templates/footer.php'; ?>
