<?php
// Procesamiento de baja de un jugador mediante solicitud POST
require_once __DIR__ . '/conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    establecer_mensaje_flash('error', 'Metodo no permitido para eliminar registros.');
    redirigir('index.php');
}

$id_jugador = filter_input(INPUT_POST, 'id_jugador', FILTER_VALIDATE_INT);

if (!$id_jugador || $id_jugador <= 0) {
    establecer_mensaje_flash('error', 'Identificador de jugador invalido.');
    redirigir('index.php');
}

$pdo = obtener_conexion();

// Verificar existencia antes de eliminar para brindar un mensaje preciso
$stmt_check = $pdo->prepare('SELECT nombre FROM jugadores WHERE id_jugador = :id');
$stmt_check->execute([':id' => $id_jugador]);
$jugador = $stmt_check->fetch();

if (!$jugador) {
    establecer_mensaje_flash('error', 'El jugador que intentas eliminar no existe.');
    redirigir('index.php');
}

// La clave foranea ON DELETE CASCADE elimina automaticamente sus puntajes asociados
$stmt_delete = $pdo->prepare('DELETE FROM jugadores WHERE id_jugador = :id');
$stmt_delete->execute([':id' => $id_jugador]);

establecer_mensaje_flash('exito', 'Jugador "' . $jugador['nombre'] . '" y sus puntajes asociados fueron eliminados.');
redirigir('index.php');
