function $(id) { return document.getElementById(id); }

// Mostrar error debajo de un campo o grupo
function mostrarErrorCampo(campo, mensaje) {
    if (!campo) return;

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

function validarFormulario(event) {
    event.preventDefault();
    let ok = true;

    // limpiar errores previos
    const campos = document.querySelectorAll("input, select");
    campos.forEach(campo => {
        campo.classList.remove("campo-error");
        let eliminar = campo.nextElementSibling;
        if (eliminar && eliminar.classList.contains('error-campo')) {
            eliminar.remove();
        }
    });

    // Usuario
    const usuario = $("usuario").value.trim();
    if (usuario.length < 3 || usuario.length > 15) {
        mostrarErrorCampo($("usuario"), "El nombre de usuario debe tener entre 3 y 15 caracteres.");
        ok = false;
    }
    if (/^[0-9]/.test(usuario)) {
        mostrarErrorCampo($("usuario"), "El usuario no puede comenzar con un número.");
        ok = false;
    }

    // Contraseña (mensajes específicos)
    const password = $("password").value;
    const password2 = $("password2").value;
    if (password.length < 6 || password.length > 15) {
        mostrarErrorCampo($("password"), "La contraseña debe tener entre 6 y 15 caracteres.");
        ok = false;
    }
    const faltas = [];
    if (!/[A-Z]/.test(password)) { faltas.push("una mayúscula"); }
    if (!/[a-z]/.test(password)) { faltas.push("una minúscula"); }
    if (!/[0-9]/.test(password)) { faltas.push("un número"); }
    if (faltas.length > 0) {
        const msg = `La contraseña debe incluir al menos ${faltas.join(", ")}.`;
        mostrarErrorCampo($("password"), msg);
        ok = false;
    }
    if (password !== password2) {
        mostrarErrorCampo($("password2"), "Las contraseñas no coinciden.");
        ok = false;
    }

    // Email
    const emailCampo = $("email");
    const email = emailCampo ? emailCampo.value.trim() : "";
    let emailError = null;
    if (email.length === 0) {
        emailError = "El correo electrónico es obligatorio.";
    } else {
        const at1 = email.indexOf("@");
        const at2 = email.indexOf("@", at1 + 1);
        if (at1 === -1 || at2 !== -1) {
            emailError = "Debe tener el formato parte-local@dominio.";
        } else {
            const local = email.slice(0, at1);
            const dominio = email.slice(at1 + 1);

            // longitudes de partes
            if (!emailError && (local.length < 1 || local.length > 64)) {
                emailError = "La parte local debe tener entre 1 y 64 caracteres.";
            }
            if (!emailError && (dominio.length < 1 || dominio.length > 255)) {
                emailError = "El dominio debe tener entre 1 y 255 caracteres.";
            }

            // parte-local: caracteres permitidos y reglas del punto
            if (!emailError) {
                const localRegex = /^[A-Za-z0-9!#$%&'*+\-/=?^_`{|}~.]+$/;
                if (!localRegex.test(local)) {
                    emailError = "La parte local contiene caracteres no permitidos.";
                } else if (local.startsWith('.') || local.endsWith('.')) {
                    emailError = "La parte local no puede empezar ni terminar con punto.";
                } else if (local.includes('..')) {
                    emailError = "La parte local no puede contener puntos consecutivos.";
                }
            }

            // dominio: subdominios separados por punto, cada uno 1..63, [A-Za-z0-9-], sin guion al inicio/fin
            if (!emailError) {
                const labels = dominio.split('.');
                if (labels.length < 1) {
                    emailError = "El dominio debe tener al menos un subdominio.";
                } else {
                    for (let i = 0; i < labels.length; i++) {
                        const lab = labels[i];
                        if (lab.length < 1 || lab.length > 63) {
                            emailError = `El subdominio "${lab}" debe tener entre 1 y 63 caracteres.`;
                            break;
                        }
                        if (!/^[A-Za-z0-9-]+$/.test(lab)) {
                            emailError = `El subdominio "${lab}" contiene caracteres no permitidos.`;
                            break;
                        }
                        if (lab.startsWith('-') || lab.endsWith('-')) {
                            emailError = `El subdominio "${lab}" no puede empezar ni terminar con guion.`;
                            break;
                        }
                    }
                }
            }
        }
    }
    if (emailError) {
        mostrarErrorCampo(emailCampo, emailError);
        ok = false;
    }

    // Sexo
    const sexos = document.getElementsByName("sexo");
    const sexoSeleccionado = document.querySelector('input[name="sexo"]:checked');
    if (!sexoSeleccionado) {
        const contenedorSexo = document.querySelector('p.sexo');
        mostrarErrorCampo(contenedorSexo || sexos, "Debes seleccionar un sexo.");
        ok = false;
    }

    // Fecha de nacimiento (mensajes específicos)
    const fechaIni = $("nacimiento").value;
    if (fechaIni) {
        const fechaNacimiento = new Date(fechaIni);
        const hoy = new Date();
        // Normalizar horas para evitar desfases
        fechaNacimiento.setHours(0,0,0,0);
        hoy.setHours(0,0,0,0);
        if (fechaNacimiento > hoy) {
            mostrarErrorCampo($("nacimiento"), "La fecha de nacimiento no puede ser posterior a hoy.");
            ok = false;
        } else {
            let edad = hoy.getFullYear() - fechaNacimiento.getFullYear();
            const mes = hoy.getMonth() - fechaNacimiento.getMonth();
            if (mes < 0 || (mes === 0 && hoy.getDate() < fechaNacimiento.getDate())) {
                edad--;
            }
            if (edad < 18) {
                mostrarErrorCampo($("nacimiento"), "Debes ser mayor de edad para registrarte.");
                ok = false;
            }
        }
    } else {
        mostrarErrorCampo($("nacimiento"), "Debes ingresar una fecha de nacimiento.");
        ok = false;
    }

    // Ciudad y país
    const ciudad = $("ciudad").value.trim();
    const pais = $("pais").value.trim();
    if(ciudad !== "" && pais === "") {
        mostrarErrorCampo($("pais"), "Si ingresas una ciudad, debes ingresar también un país.");
        ok = false;
    }

    // Foto de perfil
    const perfil = $("foto");

    if(ok) {
        $("formRegistro").submit();
    }
}

function load() {
    const formRegistro = $("formRegistro");
    if (formRegistro) {
        formRegistro.addEventListener("submit", validarFormulario);
    }

    // eliminar error al escribir
    const campos = document.querySelectorAll("input, select");
    campos.forEach(campo => {
        campo.addEventListener("input", () => {
            campo.classList.remove("campo-error");
            let eliminar = campo.nextElementSibling;
            if (eliminar && eliminar.classList.contains('error-campo')) {
                eliminar.remove();
            }
        });
    });

    // limpiar error al enfocar nacimiento
    const nacimiento = $("nacimiento");
    if (nacimiento) {
        nacimiento.addEventListener('focus', () => nacimiento.classList.remove('campo-error'));
    }

    // Activar validación nativa para email (sin usar type en HTML)
    const emailCampoLoad = $("email");
    if (emailCampoLoad) {
        try { 
            emailCampoLoad.setAttribute('type', 'email'); 
        } catch (e) {}
    }

    // Asignar tipo password por JS (sin usar type en HTML)
    const pass1 = $("password");
    if (pass1) { 
        try { 
            pass1.setAttribute('type', 'password'); 
        } catch (e) {} 
    }
    const pass2 = $("password2");
    if (pass2) { 
        try { 
            pass2.setAttribute('type', 'password'); 
        } catch (e) {} 
    }

    //Foto de perfil
    const perfil = $("foto");
    if (perfil) {
        try {
            perfil.setAttribute('type', 'file');
        } catch(e) {
            console.warn('No se pudo establecer type=file dinámicamente:', e);
        }

        perfil.addEventListener('change', () => {
            perfil.classList.remove('campo-error');
            let eliminar = perfil.nextElementSibling;
            if (eliminar && eliminar.classList.contains('error-campo')) {
                eliminar.remove();
            }

            //
            const previewId = 'fotoPreview';
            let prev = document.getElementById(previewId);
            if (!prev) {
                prev = document.createElement('img');
                prev.id = previewId;
                prev.style.maxWidth = '150px';
                prev.style.display = 'block';
                prev.style.marginTop = '0.5rem';
                perfil.parentNode.appendChild(prev);
            }
            const file = perfil.files && perfil.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = e => { prev.src = e.target.result; };
                reader.readAsDataURL(file);
            } else if (prev) {
                prev.remove();
            }
        });
    }

    //Opcion radio
    const contenedorSexo = document.querySelector("p.sexo");
    if (contenedorSexo) {
        const opcionesSexo = [
            { valor: "H", texto: "Hombre" },
            { valor: "M", texto: "Mujer" },
            { valor: "O", texto: "Otro" }
        ];

        opcionesSexo.forEach(op => {
            const input = document.createElement("input");
            input.name = "sexo";
            input.value = op.valor;
            try { input.setAttribute("type", "radio"); } catch (_) { input.type = 'radio'; }

            const label = document.createElement("label");
            label.textContent = " " + op.texto;
            label.prepend(input);

            contenedorSexo.appendChild(label);
            contenedorSexo.appendChild(document.createTextNode(" "));
        });

        // limpiar error al cambiar selección
        contenedorSexo.querySelectorAll('input[name="sexo"]').forEach(r => {
            r.addEventListener('change', () => {
                const cont = document.querySelector('p.sexo');
                if (cont) {
                    cont.classList.remove('campo-error');
                    const elim = cont.nextElementSibling;
                    if (elim && elim.classList.contains('error-campo')) { elim.remove(); }
                }
            });
        });
    }

    //Opcion option pais
    const selectPais = $("pais");
    if (selectPais) {
        const opcionesPais = [
            { valor: "", texto: "Seleccione un país" },
            { valor: "de", texto: "Alemania" },
            { valor: "es", texto: "España" },
            { valor: "fr", texto: "Francia" },
            { valor: "it", texto: "Italia" },
            { valor: "pt", texto: "Portugal" }
        ];

        opcionesPais.forEach(op => {
            const option = document.createElement("option");
            option.value = op.valor;
            option.textContent = op.texto;
            selectPais.appendChild(option);
        });
    }

    // Tipo date simple (sin UI adicional)
    const nacimientoInput = $("nacimiento");
    if (nacimientoInput) {
        try {
            nacimientoInput.setAttribute("type", "date");
        } catch (e) {
            console.warn("No se pudo establecer type=date dinámicamente:", e);
        }
    }
}

document.addEventListener("DOMContentLoaded", load);
