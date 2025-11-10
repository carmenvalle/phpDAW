function $(id) { return document.getElementById(id); }

/** Muestra mensaje de error debajo del campo (versión unificada) */
function mostrarErrorCampo(campo, mensaje) {
    if (!campo) {
        return;
    }

    // si es un grupo de radios, mostrar en el contenedor padre <p>
    if (campo.length && campo[0].name === "sexo") {
        campo = campo[0].closest("p.sexo");
    }

    campo.classList.add('campo-error');

    // eliminar error anterior
    let eliminar = campo.nextElementSibling;
    if (eliminar && eliminar.classList.contains('error-campo')) {
        eliminar.remove();
    }

    const span = document.createElement('span');
    span.className = 'error-campo';
    span.textContent = mensaje;
    campo.insertAdjacentElement('afterend', span);
}

/** Validación Login */
function validarLogin(e) {
    e.preventDefault();
    const form = e.target;
    const user = form.querySelector('#usuario');
    const pass = form.querySelector('#password');

    // limpiar errores anteriores
    [user, pass].forEach(el => {
        if (el) {
            el.classList.remove('campo-error');
            let eliminar = el.nextElementSibling;
            if (eliminar && eliminar.classList.contains('error-campo')) {
                eliminar.remove();
            }
        }
    });

    let ok = true;

    if (!user || !user.value.trim()) {
        mostrarErrorCampo(user, 'Introduce tu nombre de usuario.');
        ok = false;
    }

    // Validación de contraseña igual que en registro
    if (!pass || !pass.value) {
        mostrarErrorCampo(pass, 'Introduce tu contraseña.');
        ok = false;
    }

    if (ok) {
        form.submit();
    }
}

/** Validación Búsqueda */
function validarBusqueda(e) {
    const input = document.querySelector('#consulta');
    if (!input) {
        return;
    }

    // limpiar error anterior
    input.classList.remove('campo-error');
    let eliminar = input.nextElementSibling;
    if (eliminar && eliminar.classList.contains('error-campo')) {
        eliminar.remove();
    }

    if (!input.value.trim() || input.value.trim().length < 3) {
        e.preventDefault();
        mostrarErrorCampo(input, 'Escribe al menos 3 caracteres para buscar.');
        input.focus();
    }
}

/** Inicialización de formulario en index.html */
function initIndex() {
    const formLogin = document.querySelector('main section form[action="index_logueado.html"]');
    if (formLogin) {
        formLogin.addEventListener('submit', validarLogin);
    }

    const formSearch = document.querySelector('main section form[action="resultados.html"]');
    if (formSearch) {
        formSearch.addEventListener('submit', validarBusqueda);
    }

    // limpiar errores al escribir
    document.querySelectorAll('input').forEach(i => i.addEventListener('input', () => {
        i.classList.remove('campo-error');
        let eliminar = i.nextElementSibling;
        if (eliminar && eliminar.classList.contains('error-campo')) {
            eliminar.remove();
        }
    }));

    // Asignar tipo password por JS para el campo de login
    const pass = document.querySelector('#password');
    if (pass) {
        try { 
            pass.setAttribute('type', 'password'); 
        } catch (e) {}
    }
}

document.addEventListener('DOMContentLoaded', initIndex);
