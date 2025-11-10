function $(id) { return document.getElementById(id); }

// Mostrar mensaje de error junto al campo o contenedor
function mostrarErrorCampo(campo, mensaje) {
    if (!campo) return;

    // si es un NodeList (grupo de radios) manejar su contenedor (fieldset o padre)
    if (campo.length && campo[0].name === 'tipo_anuncio') {
        // buscar el fieldset más cercano
        let fs = campo[0].closest('fieldset') || campo[0].parentNode;
        campo = fs;
    }

    // si nos pasan un campo simple (input/select/textarea), preferimos mostrar
    // el error debajo del contenedor más lógico (<p> o <div>) para que coincida
    // con el comportamiento de registro.js
    let target = campo;
    if (campo instanceof Element) {
        const p = campo.closest('p');
        const fs = campo.closest('fieldset');
        target = p || fs || campo;
    }

    target.classList.add('campo-error');

    // eliminar error anterior
    let eliminar = target.nextElementSibling;
    if (eliminar && eliminar.classList.contains('error-campo')) eliminar.remove();

    const span = document.createElement('span');
    span.className = 'error-campo';
    span.textContent = mensaje;
    target.insertAdjacentElement('afterend', span);
}

function validarNuevoAnuncio(event) {
    event.preventDefault();
    let ok = true;

    // limpiar errores previos
    const campos = document.querySelectorAll('input, select, textarea');
    campos.forEach(c => {
        c.classList.remove('campo-error');
        let elim = c.nextElementSibling;
        if (elim && elim.classList.contains('error-campo')) elim.remove();
    });

    // titulo
    const titulo = $('titulo').value.trim();
    if (titulo.length < 5) {
        mostrarErrorCampo($('titulo'), 'El título debe tener al menos 5 caracteres.');
        ok = false;
    }

    // descripcion
    const descripcion = $('descripcion').value.trim();
    if (descripcion.length < 20) {
        mostrarErrorCampo($('descripcion'), 'La descripción debe tener al menos 20 caracteres.');
        ok = false;
    }

    // precio (si se proporciona, debe ser >= 0)
    const precioVal = $('precio').value.trim();
    if (precioVal !== '') {
        const precio = Number(precioVal);
        if (isNaN(precio) || precio < 0) {
            mostrarErrorCampo($('precio'), 'Introduce un precio válido (número >= 0).');
            ok = false;
        }
    }

    // ciudad y pais
    const ciudad = $('ciudad').value.trim();
    const pais = $('pais').value.trim();
    if (ciudad !== '' && pais === '') {
        mostrarErrorCampo($('pais'), 'Si indicas una ciudad debes indicar también el país.');
        ok = false;
    }

    // superficie, habitaciones, banos, planta, anio (si existen, validar numerico)
    ['superficie','habitaciones','banos','planta','anio'].forEach(id => {
        const el = $(id);
        if (!el) return;
        const v = el.value.trim();
        if (v !== '') {
            const n = Number(v);
            if (isNaN(n) || n < 0) {
                mostrarErrorCampo(el, 'Introduce un número válido.');
                ok = false;
            }
        }
    });

    // año rango
    const anioEl = $('anio');
    if (anioEl && anioEl.value.trim() !== '') {
        const a = Number(anioEl.value);
        if (a < 1800 || a > 2100) {
            mostrarErrorCampo(anioEl, 'Introduce un año entre 1800 y 2100.');
            ok = false;
        }
    }

    // imágenes: comprobar tipo y número (máx 6)
    const imgs = $('imagenes');
    if (imgs && imgs.files && imgs.files.length > 0) {
        if (imgs.files.length > 6) {
            mostrarErrorCampo(imgs, 'Puedes subir hasta 6 imágenes.');
            ok = false;
        } else {
            for (let i = 0; i < imgs.files.length; i++) {
                const f = imgs.files[i];
                if (!f.type.startsWith('image/')) {
                    mostrarErrorCampo(imgs, 'Solo se permiten archivos de imagen.');
                    ok = false;
                    break;
                }
            }
        }
    }

    // usuario
    const usuarioEl = $('usuario');
    if (usuarioEl) {
        const usuario = usuarioEl.value.trim();
        if (usuario.length < 3) {
            mostrarErrorCampo(usuarioEl, 'El nombre de usuario debe tener al menos 3 caracteres.');
            ok = false;
        }
    }

    // tipo_anuncio (grupo de radios)
    const tipo = document.querySelector('input[name="tipo_anuncio"]:checked');
    if (!tipo) {
        const radios = document.getElementsByName('tipo_anuncio');
        mostrarErrorCampo(radios, 'Debes seleccionar el tipo de anuncio.');
        ok = false;
    }

    if (ok) {
        // enviar formulario
        $('formNuevoAnuncio').submit();
    }
}

function loadNuevoAnuncio() {
    const form = $('formNuevoAnuncio');
    if (form) form.addEventListener('submit', validarNuevoAnuncio);

    // quitar error al modificar campos
    const campos = document.querySelectorAll('input, select, textarea');
    campos.forEach(c => {
        c.addEventListener('input', () => {
            // limpiar posible error en el propio input y en su contenedor (<p> o <fieldset>)
            c.classList.remove('campo-error');
            const wrapper = c.closest('p') || c.closest('fieldset') || c;
            if (wrapper) wrapper.classList.remove('campo-error');
            let elim = wrapper.nextElementSibling;
            if (elim && elim.classList.contains('error-campo')) elim.remove();
        });
    });

    // limpiar error al cambiar radios de tipo_anuncio
    const radiosTipo = document.getElementsByName('tipo_anuncio');
    if (radiosTipo && radiosTipo.length) {
        Array.from(radiosTipo).forEach(r => {
            r.addEventListener('change', () => {
                // buscar el contenedor (fieldset) y eliminar error
                const cont = r.closest('fieldset') || r.parentNode;
                if (cont) {
                    cont.classList.remove('campo-error');
                    const elim = cont.nextElementSibling;
                    if (elim && elim.classList.contains('error-campo')) elim.remove();
                }
            });
        });
    }

    // preview de primera imagen
    const imgs = $('imagenes');
    if (imgs) {
        imgs.addEventListener('change', () => {
            // limpiar error en el contenedor
            const wrapper = imgs.closest('p') || imgs;
            if (wrapper) wrapper.classList.remove('campo-error');
            let elim = wrapper.nextElementSibling;
            if (elim && elim.classList.contains('error-campo')) elim.remove();

            // mostrar preview del primer archivo
            const file = imgs.files && imgs.files[0];
            const previewId = 'imagenesPreview';
            let prev = document.getElementById(previewId);
            if (!file) {
                if (prev) prev.remove();
                return;
            }
            if (!prev) {
                prev = document.createElement('img');
                prev.id = previewId;
                prev.style.maxWidth = '200px';
                prev.style.display = 'block';
                prev.style.marginTop = '0.5rem';
                imgs.parentNode.appendChild(prev);
            }
            const reader = new FileReader();
            reader.onload = e => { prev.src = e.target.result; };
            reader.readAsDataURL(file);
        });
    }
}

document.addEventListener('DOMContentLoaded', loadNuevoAnuncio);
