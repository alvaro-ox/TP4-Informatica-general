<?php
// Plantilla de cabecera HTML compartida por todas las vistas
$titulo = $titulo_pagina ?? 'ABM Videojuegos';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo limpiar_texto($titulo); ?> - ABM Arcade</title>
    <!-- Fuente retro pixelada para titulos y detalles arcade -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Hoja de estilos propia complementaria -->
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body class="bg-zinc-950 text-zinc-100 min-h-screen flex flex-col arcade-fondo font-mono">
