-- Migracion para bases ya importadas (sin perder datos)
-- Agrega jugadores.alias y puntajes.personaje
-- Ejecutar una sola vez desde phpMyAdmin (pestana SQL) con la base contrareloj_abm seleccionada.

USE contrareloj_abm;

ALTER TABLE jugadores
    ADD COLUMN alias VARCHAR(50) NOT NULL DEFAULT '' AFTER nombre;

-- Los jugadores existentes usan su nombre como alias inicial
UPDATE jugadores SET alias = LEFT(nombre, 50) WHERE alias = '';

ALTER TABLE puntajes
    ADD COLUMN personaje ENUM('perro', 'gato', 'persona') NOT NULL DEFAULT 'persona' AFTER id_jugador;
