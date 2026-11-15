<?php
// Procesamiento de baja de un puntaje mediante solicitud POST
require_once __DIR__ . '/conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    establecer_mensaje_flash('error', 'Metodo no permitido para eliminar puntajes.');
    redirigir('puntajes.php');
}

$id_puntaje = filter_input(INPUT_POST, 'id_puntaje', FILTER_VALIDATE_INT);

if (!$id_puntaje || $id_puntaje <= 0) {
    establecer_mensaje_flash('error', 'Identificador de puntaje invalido.');
    redirigir('puntajes.php');
}

$pdo = obtener_conexion();

$stmt_check = $pdo->prepare('SELECT id_puntaje FROM puntajes WHERE id_puntaje = :id');
$stmt_check->execute([':id' => $id_puntaje]);

if (!$stmt_check->fetch()) {
    establecer_mensaje_flash('error', 'El puntaje que intentas eliminar no existe.');
    redirigir('puntajes.php');
}

$stmt_delete = $pdo->prepare('DELETE FROM puntajes WHERE id_puntaje = :id');
$stmt_delete->execute([':id' => $id_puntaje]);

establecer_mensaje_flash('exito', 'Registro de puntaje eliminado correctamente.');
redirigir('puntajes.php');
