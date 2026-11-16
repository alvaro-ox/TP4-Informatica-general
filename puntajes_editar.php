<?php
// Formulario y procesamiento de modificacion de un puntaje existente
require_once __DIR__ . '/conexion.php';

$pdo = obtener_conexion();

$id_puntaje = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_puntaje = filter_input(INPUT_POST, 'id_puntaje', FILTER_VALIDATE_INT);
}

if (!$id_puntaje || $id_puntaje <= 0) {
    establecer_mensaje_flash('error', 'Identificador de puntaje invalido.');
    redirigir('puntajes.php');
}

// Buscar el puntaje existente
$stmt = $pdo->prepare(
    'SELECT id_puntaje, id_jugador, personaje, puntaje_juego_1, puntaje_juego_2, puntaje_juego_3, total, fecha
     FROM puntajes WHERE id_puntaje = :id'
);
$stmt->execute([':id' => $id_puntaje]);
$puntaje = $stmt->fetch();

if (!$puntaje) {
    establecer_mensaje_flash('error', 'El registro de puntaje solicitado no existe.');
    redirigir('puntajes.php');
}

$jugadores = $pdo->query('SELECT id_jugador, nombre FROM jugadores ORDER BY nombre ASC')->fetchAll();

$errores = [];
$id_jugador = (int) $puntaje['id_jugador'];
$personaje = $puntaje['personaje'];
$p1 = (string) $puntaje['puntaje_juego_1'];
$p2 = (string) $puntaje['puntaje_juego_2'];
$p3 = (string) $puntaje['puntaje_juego_3'];
$fecha = $puntaje['fecha'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_jugador = filter_input(INPUT_POST, 'id_jugador', FILTER_VALIDATE_INT) ?: 0;
    $personaje = trim($_POST['personaje'] ?? '');
    $p1 = trim($_POST['puntaje_juego_1'] ?? '0');
    $p2 = trim($_POST['puntaje_juego_2'] ?? '0');
    $p3 = trim($_POST['puntaje_juego_3'] ?? '0');
    $fecha = trim($_POST['fecha'] ?? '');

    $stmt_jug = $pdo->prepare('SELECT id_jugador FROM jugadores WHERE id_jugador = :id');
    $stmt_jug->execute([':id' => $id_jugador]);
    if (!$stmt_jug->fetch()) {
        $errores[] = 'Debes seleccionar un jugador valido.';
    }

    if (!in_array($personaje, lista_personajes(), true)) {
        $errores[] = 'Debes seleccionar un personaje valido.';
    }

    $v1 = filter_var($p1, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
    $v2 = filter_var($p2, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
    $v3 = filter_var($p3, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);

    if ($v1 === false || $v2 === false || $v3 === false) {
        $errores[] = 'Todos los puntajes deben ser numeros enteros iguales o mayores a 0.';
    }

    if (!validar_fecha($fecha)) {
        $errores[] = 'La fecha ingresada no es valida.';
    }

    if (empty($errores)) {
        $total = $resultado['datos']['total'];

        $stmt_update = $pdo->prepare(
            'UPDATE puntajes
             SET id_jugador = :id_jugador,
                 personaje = :personaje,
                 puntaje_juego_1 = :p1,
                 puntaje_juego_2 = :p2,
                 puntaje_juego_3 = :p3,
                 total = :total,
                 fecha = :fecha
             WHERE id_puntaje = :id_puntaje'
        );
        $stmt_update->execute([
            ':id_jugador' => $id_jugador,
            ':personaje'  => $personaje,
            ':p1'         => $v1,
            ':p2'         => $v2,
            ':p3'         => $v3,
            ':total'      => $total,
            ':fecha'      => $fecha,
            ':id_puntaje' => $id_puntaje,
        ]);

        establecer_mensaje_flash('exito', 'Puntaje actualizado correctamente (Nuevo total: ' . $total . ').');
        redirigir('puntajes.php');
    }
}

$titulo_pagina = 'Editar Puntaje';
$pagina_activa = 'puntajes';
require_once __DIR__ . '/templates/header.php';
require_once __DIR__ . '/templates/nav.php';
?>

<div class="max-w-xl mx-auto">
    <div class="mb-6">
        <h1 class="font-arcade text-xl text-amber-300 tracking-wide">EDITAR PUNTAJE #<?php echo (int) $id_puntaje; ?></h1>
        <p class="text-sm text-zinc-300 mt-2">Actualiza los valores; el total se recalculara automaticamente.</p>
    </div>

    <?php require __DIR__ . '/templates/errores.php'; ?>

    <form method="post" action="puntajes_editar.php" class="rounded border-2 border-cyan-400 bg-zinc-900 p-6 space-y-5 shadow-[6px_6px_0px_#d946ef]">
        <input type="hidden" name="id_puntaje" value="<?php echo (int) $id_puntaje; ?>">

        <div>
            <label for="id_jugador" class="block text-xs font-bold uppercase tracking-wider text-cyan-300 mb-1.5">Jugador</label>
            <select
                id="id_jugador"
                name="id_jugador"
                required
                class="w-full rounded border-2 border-zinc-600 bg-zinc-950 px-3 py-2.5 text-sm text-white focus:border-cyan-400 focus:outline-none"
            >
                <?php foreach ($jugadores as $jug): ?>
                    <option value="<?php echo (int) $jug['id_jugador']; ?>" <?php echo ((int) $jug['id_jugador'] === $id_jugador) ? 'selected' : ''; ?>>
                        <?php echo limpiar_texto($jug['nombre']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label for="personaje" class="block text-xs font-bold uppercase tracking-wider text-cyan-300 mb-1.5">Personaje</label>
            <select
                id="personaje"
                name="personaje"
                required
                class="w-full rounded border-2 border-zinc-600 bg-zinc-950 px-3 py-2.5 text-sm text-white focus:border-cyan-400 focus:outline-none"
            >
                <?php foreach (lista_personajes() as $opcion): ?>
                    <option value="<?php echo limpiar_texto($opcion); ?>" <?php echo ($opcion === $personaje) ? 'selected' : ''; ?>>
                        <?php echo limpiar_texto(ucfirst($opcion)); ?>
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
                Guardar Cambios
            </button>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/templates/footer.php'; ?>
