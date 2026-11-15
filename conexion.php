<?php
// Archivo de conexion a la base de datos y funciones auxiliares compartidas

// Iniciar sesion para manejar mensajes de confirmacion y error entre vistas
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Configuracion de la base de datos MySQL (valores por defecto compatibles con XAMPP/LAMPP)
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'contrareloj_abm');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

/**
 * Obtiene una instancia segura de PDO conectada a MySQL.
 */
function obtener_conexion(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        $opciones = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $opciones);
        } catch (PDOException $e) {
            // Mostrar el detalle técnico exacto para diagnosticar el problema de conexión
            http_response_code(500);
            die('<strong>Error de conexion con la base de datos:</strong><br>' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
        }
    }

    return $pdo;
}

/**
 * Escapa caracteres especiales para prevenir inyeccion XSS en HTML.
 */
function limpiar_texto(?string $texto): string
{
    return htmlspecialchars((string) $texto, ENT_QUOTES, 'UTF-8');
}

/**
 * Verifica si una cadena corresponde a una fecha valida en formato YYYY-MM-DD.
 */
function validar_fecha(string $fecha): bool
{
    $objeto_fecha = DateTime::createFromFormat('Y-m-d', $fecha);
    return $objeto_fecha !== false && $objeto_fecha->format('Y-m-d') === $fecha;
}

/**
 * Lista de personajes disponibles en el videojuego.
 */
function lista_personajes(): array
{
    return ['perro', 'gato', 'persona'];
}

/**
 * Guarda un mensaje temporal en sesion para mostrarlo tras una redireccion.
 */
function establecer_mensaje_flash(string $tipo, string $texto): void
{
    $_SESSION['flash_mensaje'] = [
        'tipo'  => $tipo,
        'texto' => $texto,
    ];
}

/**
 * Recupera y elimina el mensaje temporal almacenado en sesion.
 */
function obtener_mensaje_flash(): ?array
{
    if (!isset($_SESSION['flash_mensaje'])) {
        return null;
    }

    $mensaje = $_SESSION['flash_mensaje'];
    unset($_SESSION['flash_mensaje']);
    return $mensaje;
}

/**
 * Redirige a una ruta relativa y finaliza el script actual.
 */
function redirigir(string $ruta): void
{
    header('Location: ' . $ruta);
    exit;
}
