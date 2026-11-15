<?php
// Plantilla de pie de pagina y scripts JavaScript compartidos
?>
</main>

<footer class="bg-zinc-900 border-t-4 border-cyan-500 mt-auto">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-zinc-300">
        <span class="font-bold text-cyan-300 uppercase tracking-wider">Sistema ABM Jugadores y Puntajes // PHP + MySQL</span>
        <span class="text-amber-300 font-bold uppercase tracking-wider">Contrareloj - TP4</span>
    </div>
</footer>

<script>
// Confirmacion previa antes de enviar formularios de eliminacion
document.addEventListener('DOMContentLoaded', function () {
    const formulariosEliminar = document.querySelectorAll('.form-eliminar');
    formulariosEliminar.forEach(function (formulario) {
        formulario.addEventListener('submit', function (evento) {
            const mensaje = formulario.getAttribute('data-confirmacion') || '¿Confirmas que deseas eliminar este registro?';
            if (!window.confirm(mensaje)) {
                evento.preventDefault();
            }
        });
    });

    // Calculo en vivo del total en formularios de puntajes
    const inputJ1 = document.getElementById('puntaje_juego_1');
    const inputJ2 = document.getElementById('puntaje_juego_2');
    const inputJ3 = document.getElementById('puntaje_juego_3');
    const campoTotal = document.getElementById('total_preview');

    if (inputJ1 && inputJ2 && inputJ3 && campoTotal) {
        const actualizarTotal = function () {
            const v1 = parseInt(inputJ1.value, 10) || 0;
            const v2 = parseInt(inputJ2.value, 10) || 0;
            const v3 = parseInt(inputJ3.value, 10) || 0;
            campoTotal.textContent = (v1 + v2 + v3).toString();
        };

        inputJ1.addEventListener('input', actualizarTotal);
        inputJ2.addEventListener('input', actualizarTotal);
        inputJ3.addEventListener('input', actualizarTotal);
        actualizarTotal();
    }
});
</script>
</body>
</html>
