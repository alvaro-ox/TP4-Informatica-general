<?php
// Formulario y procesamiento de alta de un nuevo jugador
require_once __DIR__ . '/conexion.php';

$errores = [];
$nombre = '';
$edad = '';
$fecha = date('Y-m-d');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $resultado = validar_datos_jugador($_POST);
    $errores = $resultado['errores'];
    $nombre = $resultado['datos']['nombre'];
    $edad = $resultado['datos']['edad'];
    $fecha = $resultado['datos']['fecha'];

    // Si no hay errores, insertar el registro en la tabla jugadores
    if (empty($errores)) {
        $pdo = obtener_conexion();
        $stmt = $pdo->prepare('INSERT INTO jugadores (nombre, edad, fecha) VALUES (:nombre, :edad, :fecha)');
        $stmt->execute([
            ':nombre' => $nombre,
            ':edad'   => $resultado['datos']['edad_int'],
            ':fecha'  => $fecha,
        ]);

        establecer_mensaje_flash('exito', 'Jugador "' . $nombre . '" registrado correctamente.');
        redirigir('index.php');
    }
}

$titulo_pagina = 'Nuevo Jugador';
$pagina_activa = 'alta_jugador';
require_once __DIR__ . '/templates/header.php';
require_once __DIR__ . '/templates/nav.php';
?>

<div class="max-w-xl mx-auto">
    <div class="mb-6">
        <h1 class="font-arcade text-xl text-amber-300 tracking-wide">NUEVO JUGADOR</h1>
        <p class="text-sm text-zinc-300 mt-2">Completa los datos para registrar un jugador en la base de datos.</p>
    </div>

    <?php require __DIR__ . '/templates/errores.php'; ?>

    <form method="post" action="alta.php" class="rounded border-2 border-cyan-400 bg-zinc-900 p-6 space-y-5 shadow-[6px_6px_0px_#d946ef]">
        <div>
            <label for="nombre" class="block text-xs font-bold uppercase tracking-wider text-cyan-300 mb-1.5">Nombre</label>
            <input
                type="text"
                id="nombre"
                name="nombre"
                maxlength="100"
                required
                value="<?php echo limpiar_texto($nombre); ?>"
                placeholder="Ej. Lucas Martinez"
                class="w-full rounded border-2 border-zinc-600 bg-zinc-950 px-3 py-2.5 text-sm text-white placeholder-zinc-500 focus:border-cyan-400 focus:outline-none"
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
                placeholder="Ej. 22"
                class="w-full rounded border-2 border-zinc-600 bg-zinc-950 px-3 py-2.5 text-sm text-white placeholder-zinc-500 focus:border-cyan-400 focus:outline-none"
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
                Guardar Jugador
            </button>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/templates/footer.php'; ?>
