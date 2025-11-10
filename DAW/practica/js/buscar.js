function $(id) { return document.getElementById(id); }

// Mostrar error debajo de un campo o grupo
function mostrarErrorCampo(campo, mensaje) {
    if (!campo) {
        return;
    }

    // si es un grupo de radios, mostrar en el contenedor padre <p>
    if (campo.length && campo[0].name === "tipo_anuncio") {
        campo = campo[0].closest("p.anuncio");
    }

    const contenedor = campo.closest("p") || campo;
    contenedor.classList.add('campo-error');

    // eliminar error anterior
    let eliminar = contenedor.nextElementSibling;
    if (eliminar && eliminar.classList.contains('error-campo')) {
        eliminar.remove();
    }

    const span = document.createElement('span');
    span.className = 'error-campo';
    span.textContent = mensaje;
    campo.insertAdjacentElement('afterend', span);
}

function validarBusqueda(event) {
    event.preventDefault();
    let ok = true;

    // limpiar errores previos
    document.querySelectorAll('.error-campo').forEach(e => e.remove());
    document.querySelectorAll('.campo-error').forEach(e => e.classList.remove('campo-error'));

    // Tipo de anuncio
    const radios = document.getElementsByName("tipo_anuncio");
    if (!Array.from(radios).some(r => r.checked)) {
        mostrarErrorCampo(radios, "Debes seleccionar un tipo de anuncio.");
        ok = false;
    }

    // Rango de precios (si se indica el máximo, debe haber mínimo)
    const pmin = $("precio-min").value.trim();
    const pmax = $("precio-max").value.trim();
    if (pmin !== "" && Number(pmin) < 0) {
        mostrarErrorCampo($("precio-min"), "El precio mínimo no puede ser negativo.");
        ok = false;
    }
    if (pmax !== "" && Number(pmax) < 0) {
        mostrarErrorCampo($("precio-max"), "El precio máximo no puede ser negativo.");
        ok = false;
    }
    if (pmax !== "" && pmin === "") {
        mostrarErrorCampo($("precio-min"), "Si indicas un precio máximo, debes indicar también el mínimo.");
        ok = false;
    }
    if (pmin !== "" && pmax !== "" && Number(pmin) > Number(pmax)) {
        mostrarErrorCampo($("precio-max"), "El precio máximo no puede ser menor que el mínimo.");
        ok = false;
    }

    // Fechas (si se indica una, debe indicarse la otra)
    const fdesdeCampo = $("fecha-desde");
    const fhastaCampo = $("fecha-hasta");
    const fdesde = fdesdeCampo.value.trim();
    const fhasta = fhastaCampo.value.trim();
    const fechaActual = new Date();

    if (fhasta !== "" && fdesde === "") {
        mostrarErrorCampo(fdesdeCampo, "Si indicas una fecha final, debes indicar también la inicial.");
        ok = false;
    }

    if (fdesde !== "" && fhasta !== "") {
        const d1 = new Date(fdesde);
        const d2 = new Date(fhasta);
        if (d1 > d2) {
            mostrarErrorCampo(fhastaCampo, "La fecha final no puede ser anterior a la inicial.");
            ok = false;
        }
    }

    // Comprobar que ninguna fecha es posterior a la actual
    if (fdesde !== "" && new Date(fdesde) > fechaActual) {
        mostrarErrorCampo(fdesdeCampo, "La fecha inicial no puede ser posterior a hoy.");
        ok = false;
    }
    if (fhasta !== "" && new Date(fhasta) > fechaActual) {
        mostrarErrorCampo(fhastaCampo, "La fecha final no puede ser posterior a hoy.");
        ok = false;
    }

    // Ciudad y país: si hay ciudad, país obligatorio
    const ciudadCampo = $("ciudad");
    const paisCampo = $("pais");
    const ciudad = ciudadCampo ? ciudadCampo.value.trim() : "";
    const pais = paisCampo ? paisCampo.value.trim() : "";
    if (ciudad !== "" && pais === "") {
        mostrarErrorCampo(paisCampo, "Si indicas una ciudad, debes indicar también un país.");
        ok = false;
    }

    if (ok) {
        $("formBuscar").submit();
    }
}

function load() {
    const formBuscar = $("formBuscar");
    if (formBuscar) {
        formBuscar.addEventListener("submit", validarBusqueda);
    }

    // Radios para "tipo_anuncio"
    const contenedor = document.querySelector("p.anuncio");
    if (contenedor && !contenedor.querySelector('input[name="tipo_anuncio"]')) {
        const opciones = [
            { valor: "venta", texto: " Venta" },
            { valor: "alquiler", texto: " Alquiler" }
        ];

        opciones.forEach(op => {
            const input = document.createElement("input");
            input.name = "tipo_anuncio";
            input.value = op.valor;
            input.setAttribute("type", "radio"); // asignado dinámicamente

            const label = document.createElement("label");
            label.appendChild(input);
            label.appendChild(document.createTextNode(op.texto));

            contenedor.appendChild(label);
            contenedor.appendChild(document.createTextNode(" "));
        });
    }

    // Option para los países
    const selectVivienda = $("vivienda");
    if (selectVivienda) {
        const opcionesVivienda = [
            { valor: "", texto: "Seleccione un tipo de vivienda" },
            { valor: "obra-nueva", texto: "Obra nueva" },
            { valor: "vivienda", texto: "Vivienda" },
            { valor: "oficina", texto: "Oficina" },
            { valor: "local", texto: "Local" },
            { valor: "garaje", texto: "Garaje" }
        ];

        opcionesVivienda.forEach(op => {
            const option = document.createElement("option");
            option.value = op.valor;
            option.textContent = op.texto;
            selectVivienda.appendChild(option);
        });
    }

    // Option para los países
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

    // Atributos type para inputs de precio y fecha
    const pmin = $("precio-min");
    if (pmin) {
        try {
            pmin.setAttribute("type", "number");
            pmin.setAttribute("min", "0");
        } catch(_) {}
    }
    const pmax = $("precio-max");
    if (pmax) {
        try {
            pmax.setAttribute("type", "number");
            pmax.setAttribute("min", "0");
        } catch(_) {}
    }

    const fdesde = $("fecha-desde");
    if (fdesde) {
        try {
            fdesde.setAttribute("type", "date");
        } catch(_) {}
        fdesde.placeholder = "Desde";
    }
    const fhasta = $("fecha-hasta");
    if (fhasta) {
        try {
            fhasta.setAttribute("type", "date");
        } catch(_) {}
        fhasta.placeholder = "Hasta";
    }

    // Limpiar errores visuales al escribir o cambiar
    document.querySelectorAll("input, select").forEach(campo => {
        campo.addEventListener("input", () => {
            campo.classList.remove("campo-error");
            let eliminar = campo.nextElementSibling;
            if (eliminar && eliminar.classList.contains("error-campo")) {
                eliminar.remove();
            }
        });
        campo.addEventListener("change", () => {
            campo.classList.remove("campo-error");
            let eliminar = campo.nextElementSibling;
            if (eliminar && eliminar.classList.contains("error-campo")) {
                eliminar.remove();
            }
        });
    });
}

document.addEventListener("DOMContentLoaded", load);
