$(document).ready(function () {
    /**
     * Función genérica para enviar formularios mediante AJAX.
     * @param {string} formId - Selector del formulario que se enviará.
     * @param {string} resultadoId - Selector del elemento donde se mostrarán los mensajes de respuesta.
     * @param {string|null} redireccion - (Opcional) URL a la que se redirige si la respuesta es exitosa.
     */
    function enviarFormulario(formId, resultadoId, redireccion = null) {
        $(formId).submit(function (event) {
            event.preventDefault(); // Evita el envío tradicional del formulario

            $.ajax({
                type: "POST",
                url: $(this).attr("action"), // Obtiene la URL de destino desde el atributo action
                data: $(this).serialize(), // Serializa los datos del formulario para enviarlos

                beforeSend: function () {
                    $(resultadoId).html("Procesando...").fadeIn(); // Muestra mensaje mientras se procesa la solicitud
                },

                success: function (msg) {
                    // Manejo de respuestas según el código recibido
                    if (msg === "1") {
                        limpiarCampos(formId);
                        mostrarMensaje(resultadoId, "Registro exitoso! <a href='formlogin.php'>Iniciar Sesión</a>", "alert-success");
                    } else if (msg === "3") {
                        window.location.href = "../atenciones/index.php"; // Redirige a la sección de atenciones si el usuario es operador
                    } else if (msg === "1" || msg === "2") {
                        window.location.href = "../dashboard.php"; // Redirige al panel de administración si el usuario es admin o contralor
                    } else {
                        mostrarMensaje(resultadoId, msg, "alert-warning"); // Muestra un mensaje de error si la respuesta no es exitosa
                    }

                    // Si se pasa una URL de redirección y la respuesta es "success", se redirige después de 1.5 segundos
                    if (redireccion && msg === "success") {
                        setTimeout(() => {
                            window.location.href = redireccion;
                        }, 1500);
                    }
                },

                error: function () {
                    alert("Error: Ha ocurrido un problema, por favor intenta nuevamente."); // Muestra una alerta en caso de fallo
                },
            });
        });
    }

    /**
     * Muestra un mensaje en un contenedor específico con una determinada clase de alerta.
     * @param {string} resultadoId - Selector del elemento donde se mostrará el mensaje.
     * @param {string} mensaje - Texto que se mostrará en el mensaje.
     * @param {string} clase - Clase de Bootstrap para dar estilo al mensaje (alert-success, alert-warning, etc.).
     */
    function mostrarMensaje(resultadoId, mensaje, clase) {
        $(resultadoId).removeClass().addClass("alert " + clase).html(mensaje).fadeIn(1000);
    }

    /**
     * Limpia todos los campos de un formulario.
     * @param {string} formId - Selector del formulario que se limpiará.
     */
    function limpiarCampos(formId) {
        $(formId)[0].reset(); // Reinicia los valores de los campos del formulario
    }

    // Asocia la función enviarFormulario() a cada formulario específico
    enviarFormulario("#regusu", "#result"); // Registro de usuario
    enviarFormulario("#bususu", "#resultado"); // Búsqueda de usuario
    enviarFormulario("#inisesusu", "#result"); // Inicio de sesión
    enviarFormulario("#actusu", "#result"); // Actualización de usuario
});
