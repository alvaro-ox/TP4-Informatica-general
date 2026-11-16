-- Script de creacion de base de datos, tablas y datos de prueba
-- Proyecto: ABM Videojuegos (Jugadores y Puntajes)

-- 1. Creacion de la base de datos
CREATE DATABASE IF NOT EXISTS contrareloj_abm
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE contrareloj_abm;

-- 2. Primera tabla: jugadores
CREATE TABLE IF NOT EXISTS jugadores (
    id_jugador INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    alias VARCHAR(50) NOT NULL,
    edad INT NOT NULL,
    fecha DATE NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Segunda tabla: puntajes (relacionada 1:N con jugadores)
CREATE TABLE IF NOT EXISTS puntajes (
    id_puntaje INT AUTO_INCREMENT PRIMARY KEY,
    id_jugador INT NOT NULL,
    personaje ENUM('perro', 'gato', 'persona') NOT NULL DEFAULT 'persona',
    puntaje_juego_1 INT NOT NULL DEFAULT 0,
    puntaje_juego_2 INT NOT NULL DEFAULT 0,
    puntaje_juego_3 INT NOT NULL DEFAULT 0,
    total INT NOT NULL,
    fecha DATE NOT NULL,
    CONSTRAINT fk_puntajes_jugador
        FOREIGN KEY (id_jugador)
        REFERENCES jugadores(id_jugador)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Datos de prueba para jugadores
INSERT INTO jugadores (nombre, alias, edad, fecha) VALUES
    ('Lucas Martinez', 'LuMa', 22, '2026-10-01'),
    ('Sofia Gomez', 'SofiGo', 19, '2026-10-05'),
    ('Mateo Fernandez', 'Teo99', 25, '2026-10-12'),
    ('Valentina Diaz', 'Vale_D', 21, '2026-10-18'),
    ('Tomas Alvarez', 'TomiAl', 27, '2026-10-25');

-- 5. Datos de prueba para puntajes
INSERT INTO puntajes (id_jugador, personaje, puntaje_juego_1, puntaje_juego_2, puntaje_juego_3, total, fecha) VALUES
    (1, 'perro', 1500, 2300, 1850, 5650, '2026-11-01'),
    (2, 'gato', 2100, 1950, 2400, 6450, '2026-11-02'),
    (3, 'persona', 1200, 1400, 1600, 4200, '2026-11-03'),
    (1, 'gato', 1800, 2500, 2100, 6400, '2026-11-05'),
    (4, 'perro', 2600, 2200, 2750, 7550, '2026-11-08'),
    (5, 'persona', 950, 1300, 1100, 3350, '2026-11-10');
