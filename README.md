# ABM Videojuegos Arcade - Jugadores y Puntajes (PHP + MySQL)

Aplicacion web dinamica que implementa un ABM completo (Alta, Baja, Modificacion y Listado) sobre dos tablas relacionadas en MySQL (`jugadores` y `puntajes`) utilizando PHP con PDO, diseno modular de plantillas y estilos retro arcade mediante Tailwind CSS.

## Estructura del Proyecto

- `conexion.php`: Configuracion de conexion segura a MySQL mediante PDO (usando `127.0.0.1` y la base `contrareloj_abm`) y funciones auxiliares compartidas.
- `index.php`: Listado principal de jugadores, busqueda por nombre, conteo de puntajes y acceso a acciones del ABM.
- `alta.php`: Formulario de registro de nuevos jugadores con validaciones de servidor.
- `editar.php`: Formulario de modificacion de datos de jugadores existentes.
- `eliminar.php`: Baja de jugadores por POST (eliminacion en cascada de sus puntajes asociados via foreign key).
- `puntajes.php`: Listado y ranking de puntajes, con filtro por jugador y ordenamiento por total o fecha.
- `puntajes_alta.php`: Alta de puntajes asociada a un jugador con vista previa y calculo automatico del total.
- `puntajes_editar.php`: Modificacion de puntajes con recalculo automatico del total.
- `puntajes_eliminar.php`: Baja de puntajes por POST.
- `templates/`: Plantillas modulares reutilizables (`header.php`, `nav.php`, `footer.php`) incluidas mediante `require_once`.
- `css/estilos.css`: Estilos retro arcade y soporte para tipografia pixelada (`Press Start 2P`).
- `database/videojuegos_abm.sql`: Script SQL con la creacion de la base de datos `contrareloj_abm`, tablas relacionales y datos de prueba.
- `funcional.html`: Memoria tecnica y descriptiva del funcionamiento del proyecto y catalogo de queries SQL.
- `guia_windows.html`: Guia grafica paso a paso para la instalacion y ejecucion en Windows con XAMPP.

## Requisitos

- Servidor web local con PHP 8.0 o superior (por ejemplo, XAMPP o LAMPP) con la extension `pdo_mysql` habilitada.
- Servidor MySQL o MariaDB.
- Conexion a internet para cargar Tailwind CSS y la fuente retro desde CDN.

## Puesta en Marcha Local (XAMPP / LAMPP)

1. **Copiar el proyecto** en la carpeta publica del servidor web:
   - En Windows (XAMPP): `C:\xampp\htdocs\TP4-Informatica-general\`
   - En Linux (LAMPP): `/opt/lampp/htdocs/TP4-Informatica-general/`
   *(En Linux, asegurarse de otorgar permisos de lectura: `sudo chmod -R 755 /opt/lampp/htdocs/TP4-Informatica-general`)*.

2. **Iniciar Apache y MySQL** desde el panel de control de XAMPP/LAMPP o mediante terminal (`sudo /opt/lampp/lampp start`).

3. **Importar la base de datos**:
   - Abrir `http://localhost/phpmyadmin` en el navegador.
   - Ir a la pestana **Importar**, seleccionar el archivo `database/videojuegos_abm.sql` y ejecutar la importacion.
   - Esto creara la base de datos `contrareloj_abm`, la tabla `jugadores` (primera tabla), la tabla `puntajes` (segunda tabla vinculada por `id_jugador`) y registros de ejemplo.

4. **Verificar credenciales de conexion**:
   - En `conexion.php`, los valores por defecto son:
     - `DB_HOST`: `'127.0.0.1'`
     - `DB_NAME`: `'contrareloj_abm'`
     - `DB_USER`: `'root'`
     - `DB_PASS`: `''`
   - Si tu MySQL tiene clave o un usuario distinto, configuralo en esas constantes.

5. **Abrir la aplicacion**:
   - Ingresar desde el navegador a `http://localhost/TP4-Informatica-general/index.php`.

## Puesta en Marcha con Docker (Sin XAMPP)

Si tenes Docker y Docker Compose instalados, podes levantar PHP, Apache, MariaDB y phpMyAdmin con un solo comando desde la raiz del proyecto:

```bash
docker compose up -d --build
```

La base de datos `contrareloj_abm` se crea e inicializa automaticamente con [database/videojuegos_abm.sql](database/videojuegos_abm.sql).

- **Aplicacion web**: `http://localhost:8080/index.php`
- **phpMyAdmin**: `http://localhost:8081`

Para detener los contenedores:

```bash
docker compose down
```
