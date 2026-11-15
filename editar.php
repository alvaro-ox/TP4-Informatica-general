<?php
// Formulario y procesamiento de modificacion de un jugador existente
require_once __DIR__ . '/conexion.php';

$pdo = obtener_conexion();

// Obtener el ID del jugador por GET o POST
$id_jugador = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_jugador = filter_input(INPUT_POST, 'id_jugador', FILTER_VALIDATE_INT);
}

if (!$id_jugador || $id_jugador <= 0) {
    establecer_mensaje_flash('error', 'Identificador de jugador invalido.');
    redirigir('index.php');
}

// Buscar el jugador en la base de datos
$stmt = $pdo->prepare('SELECT id_jugador, nombre, edad, fecha FROM jugadores WHERE id_jugador = :id');
$stmt->execute([':id' => $id_jugador]);
$jugador = $stmt->fetch();

if (!$jugador) {
    establecer_mensaje_flash('error', 'El jugador solicitado no existe.');
    redirigir('index.php');
}

$errores = [];
$nombre = $jugador['nombre'];
$edad = (string) $jugador['edad'];
$fecha = $jugador['fecha'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $edad_raw = trim($_POST['edad'] ?? '');
    $fecha = trim($_POST['fecha'] ?? '');

    if ($nombre === '' || mb_strlen($nombre) > 100) {
        $errores[] = 'El nombre es obligatorio y debe tener como maximo 100 caracteres.';
    }

    $edad_validada = filter_var($edad_raw, FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1, 'max_range' => 120],
    ]);
    if ($edad_validada === false) {
        $errores[] = 'La edad debe ser un numero entero valido entre 1 y 120.';
    }
    $edad = $edad_raw;

    if (!validar_fecha($fecha)) {
        $errores[] = 'La fecha ingresada no es valida.';
    }

    if (empty($errores)) {
        $stmt_update = $pdo->prepare(
            'UPDATE jugadores SET nombre = :nombre, edad = :edad, fecha = :fecha WHERE id_jugador = :id'
        );
        $stmt_update->execute([
            ':nombre' => $nombre,
            ':edad'   => $edad_validada,
            ':fecha'  => $fecha,
            ':id'     => $id_jugador,
        ]);

        establecer_mensaje_flash('exito', 'Jugador actualizado correctamente.');
        redirigir('index.php');
    }
}

$titulo_pagina = 'Editar Jugador';
$pagina_activa = 'jugadores';
require_once __DIR__ . '/templates/header.php';
require_once __DIR__ . '/templates/nav.php';
?>

<div class="max-w-xl mx-auto">
    <div class="mb-6">
        <h1 class="font-arcade text-xl text-amber-300 tracking-wide">EDITAR JUGADOR #<?php echo (int) $id_jugador; ?></h1>
        <p class="text-sm text-zinc-300 mt-2">Modifica los datos del jugador y guarda los cambios.</p>
    </div>

    <?php if (!empty($errores)): ?>
        <div class="mb-6 rounded border-2 border-rose-400 bg-rose-950 p-4 text-sm font-bold text-rose-100 shadow-[4px_4px_0px_#fb7185]">
            <ul class="list-disc list-inside space-y-1">
                <?php foreach ($errores as $error): ?>
                    <li><?php echo limpiar_texto($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" action="editar.php" class="rounded border-2 border-cyan-400 bg-zinc-900 p-6 space-y-5 shadow-[6px_6px_0px_#d946ef]">
        <input type="hidden" name="id_jugador" value="<?php echo (int) $id_jugador; ?>">

        <div>
            <label for="nombre" class="block text-xs font-bold uppercase tracking-wider text-cyan-300 mb-1.5">Nombre</label>
            <input
                type="text"
                id="nombre"
                name="nombre"
                maxlength="100"
                required
                value="<?php echo limpiar_texto($nombre); ?>"
                class="w-full rounded border-2 border-zinc-600 bg-zinc-950 px-3 py-2.5 text-sm text-white focus:border-cyan-400 focus:outline-none"
            >
        </div>

        <div>
            <label for="edad" class="block text-xs font-bold uppercase tracking-wider text-cyan-300 mb-1.5">Edad</label>
            <input
                type="number"
                id="edad"
                name="edad"
                min="1"
                max="120"
                required
                value="<?php echo limpiar_texto($edad); ?>"
                class="w-full rounded border-2 border-zinc-600 bg-zinc-950 px-3 py-2.5 text-sm text-white focus:border-cyan-400 focus:outline-none"
            >
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
            <a href="index.php" class="rounded border-2 border-zinc-500 bg-zinc-800 px-4 py-2.5 text-xs font-bold uppercase tracking-wider text-zinc-100 hover:bg-zinc-700 transicion-suave">
                Cancelar
            </a>
            <button type="submit" class="rounded border-2 border-fuchsia-300 bg-fuchsia-600 px-5 py-2.5 text-xs font-bold uppercase tracking-wider text-white shadow-[3px_3px_0px_#22d3ee] hover:bg-fuchsia-500 transicion-suave">
                Guardar Cambios
            </button>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/templates/footer.php'; ?>
