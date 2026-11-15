<?php
// Archivo de conexion a la base de datos y funciones auxiliares compartidas

// Iniciar sesion para manejar mensajes de confirmacion y error entre vistas
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Configuracion de la base de datos MySQL (toma variables de entorno en Docker o valores por defecto en XAMPP)
define('DB_HOST', getenv('DB_HOST') ?: '127.0.0.1');
define('DB_NAME', getenv('DB_NAME') ?: 'contrareloj_abm');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') !== false ? getenv('DB_PASS') : '');
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

/**
 * Valida y normaliza los datos enviados para un jugador (alta o edicion).
 */
function validar_datos_jugador(array $datos): array
{
    $errores = [];
    $nombre = trim($datos['nombre'] ?? '');
    $edad_raw = trim($datos['edad'] ?? '');
    $fecha = trim($datos['fecha'] ?? '');

    if ($nombre === '' || mb_strlen($nombre) > 100) {
        $errores[] = 'El nombre es obligatorio y debe tener como maximo 100 caracteres.';
    }

    $edad_validada = filter_var($edad_raw, FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1, 'max_range' => 120],
    ]);
    if ($edad_validada === false) {
        $errores[] = 'La edad debe ser un numero entero valido entre 1 y 120.';
    }

    if (!validar_fecha($fecha)) {
        $errores[] = 'La fecha ingresada no es valida.';
    }

    return [
        'datos' => [
            'nombre' => $nombre,
            'edad'   => $edad_raw,
            'edad_int' => $edad_validada,
            'fecha'  => $fecha,
        ],
        'errores' => $errores,
    ];
}

/**
 * Valida los datos de un puntaje, verifica el jugador y calcula el total.
 */
function validar_datos_puntaje(PDO $pdo, array $datos): array
{
    $errores = [];
    $id_jugador = filter_var($datos['id_jugador'] ?? 0, FILTER_VALIDATE_INT) ?: 0;
    $p1 = trim($datos['puntaje_juego_1'] ?? '0');
    $p2 = trim($datos['puntaje_juego_2'] ?? '0');
    $p3 = trim($datos['puntaje_juego_3'] ?? '0');
    $fecha = trim($datos['fecha'] ?? '');

    // Verificar que el jugador seleccionado exista en la tabla jugadores
    $stmt_jug = $pdo->prepare('SELECT id_jugador FROM jugadores WHERE id_jugador = :id');
    $stmt_jug->execute([':id' => $id_jugador]);
    if (!$stmt_jug->fetch()) {
        $errores[] = 'Debes seleccionar un jugador valido.';
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

    $total = ($v1 !== false && $v2 !== false && $v3 !== false) ? ($v1 + $v2 + $v3) : 0;

    return [
        'datos' => [
            'id_jugador' => $id_jugador,
            'p1'         => $p1,
            'p2'         => $p2,
            'p3'         => $p3,
            'v1'         => $v1,
            'v2'         => $v2,
            'v3'         => $v3,
            'total'      => $total,
            'fecha'      => $fecha,
        ],
        'errores' => $errores,
    ];
}
