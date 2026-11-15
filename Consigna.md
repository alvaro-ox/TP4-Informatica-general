Consigna TP4

Desarrollar una aplicación web dinámica que implemente un ABM (Alta, Baja, Modificación y Listado / Borrado) completo de una entidad relacionada con el universo de los videojuegos (por ejemplo: puntajes/scores, personajes, inventario, ítems o jugadores), utilizando PHP y una base de datos relacional MySQL.

Requisitos Técnicos y Estructurales

    Base de Datos (MySQL):


    Crear una tabla relacional en MySQL con los campos necesarios para la entidad elegida (ej. id, nombre, puntaje, fecha, id_jugador o estructura relacional equivalente).

    Incluir scripts de conexión seguros a la base de datos (por ejemplo, utilizando PDO o MySQLi).

    Estructura del Proyecto y Templates:


    Organizar el código modularmente utilizando la estructura de templates con include o require (separando la cabecera, el pie de página, la navegación y las vistas de contenido).

    Implementar un formulario simple para las acciones de alta y modificación de registros.

    Funcionalidades del ABM (PHP):

    Alta (Create): Formulario para registrar un nuevo elemento e insertarlo en la base de datos.

    Baja / Borrado (Delete): Mecanismo para eliminar un registro existente mediante su identificador.

    Modificación (Update): Formulario para editar los datos de un registro existente y actualizarlos en la base de datos.

    Listado (Read/Select): Visualización en forma de tabla de los registros almacenados actualmente en la base de datos.

Ítems de Entrega Requeridos

    Carpeta de código fuente comprimida (o repositorio en GitHub) conteniendo los archivos PHP (conexion.php, index.php, alta.php, editar.php, eliminar.php, etc.), la estructura de templates y la hoja de estilos CSS.

    Exportación de la base de datos (.sql) con la estructura de la tabla relacional y, opcionalmente, datos de prueba.

    Breve memoria descriptiva o archivo README que explique cómo configurar y poner en marcha el proyecto localmente.