// ===== TABLA DE COSTES DESPLEGABLE =====

document.addEventListener('DOMContentLoaded', function () {
  const toggleBtn = document.getElementById('toggle-cost-table');
  const container = document.getElementById('cost-table-container');

  if (!toggleBtn || !container) return;

  toggleBtn.addEventListener('click', function () {
    if (container.childElementCount === 0) {
      container.appendChild(buildCostTable());
      container.classList.add('open');
      toggleBtn.setAttribute('aria-expanded', 'true');
      return;
    }

    const isOpen = container.classList.contains('open');
    container.classList.toggle('open');
    toggleBtn.setAttribute('aria-expanded', !isOpen);
  });
});

function buildCostTable() {
  const table = document.createElement('table');
  table.className = 'costes-folleto';

  const thead = document.createElement('thead');

  const row1 = document.createElement('tr');
  row1.appendChild(createTh('Número de páginas'));
  row1.appendChild(createTh('Número de fotos'));
  row1.appendChild(createTh('Blanco y negro', 2));
  row1.appendChild(createTh('Color', 2));
  thead.appendChild(row1);

  const row2 = document.createElement('tr');
  row2.appendChild(createTh(''));
  row2.appendChild(createTh(''));
  row2.appendChild(createTh('150-300 dpi'));
  row2.appendChild(createTh('450-900 dpi'));
  row2.appendChild(createTh('150-300 dpi'));
  row2.appendChild(createTh('450-900 dpi'));
  thead.appendChild(row2);

  table.appendChild(thead);

  const tbody = document.createElement('tbody');
  for (let pages = 1; pages <= 15; pages++) {
    const photos = pages * 3;
    const tr = document.createElement('tr');

    tr.appendChild(createTd(pages));
    tr.appendChild(createTd(photos));
    tr.appendChild(createTd(formatEuro(computeTotal(pages, photos, false, 300))));
    tr.appendChild(createTd(formatEuro(computeTotal(pages, photos, false, 450))));
    tr.appendChild(createTd(formatEuro(computeTotal(pages, photos, true, 300))));
    tr.appendChild(createTd(formatEuro(computeTotal(pages, photos, true, 450))));

    tbody.appendChild(tr);
  }

  table.appendChild(tbody);
  return table;
}

function createTh(text, colspan) {
  const th = document.createElement('th');
  if (colspan) th.colSpan = colspan;
  th.textContent = text;
  return th;
}

function createTd(text) {
  const td = document.createElement('td');
  td.textContent = text;
  return td;
}

// Cálculo de costes con sistema de bloques
function computeTotal(pages, photos, isColor, dpi) {
  const processing = 10.0;
  const pagesCost = calculatePagesCost(pages);
  const colorCost = isColor ? photos * 0.5 : 0.0;
  const resolutionCost = dpi > 300 ? photos * 0.2 : 0.0;
  return processing + pagesCost + colorCost + resolutionCost;
}

function calculatePagesCost(pages) {
  let cost = 0;
  let remaining = pages;

  // Primeras 4 páginas: 2€/pág
  const block1 = Math.min(remaining, 4);
  cost += block1 * 2.0;
  remaining -= block1;

  // Páginas 5-10: 1.8€/pág
  if (remaining > 0) {
    const block2 = Math.min(remaining, 6);
    cost += block2 * 1.8;
    remaining -= block2;
  }

  // Páginas >10: 1.6€/pág
  if (remaining > 0) {
    cost += remaining * 1.6;
  }

  return cost;
}

function formatEuro(value) {
  return value.toFixed(2).replace('.', ',') + ' €';
}

// ===== VALIDACIÓN DEL FORMULARIO =====

document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('formFolleto');
  if (!form) return;

  form.addEventListener('submit', validarFormulario);

  // Limpiar errores al escribir
  form.querySelectorAll('input, select, textarea').forEach(campo => {
    campo.addEventListener('input', () => limpiarError(campo));
  });
});

function validarFormulario(e) {
  e.preventDefault();
  limpiarTodosLosErrores();

  let valido = true;

  valido = validarCampo('nombre', 'El nombre no puede estar vacío') && valido;
  valido = validarEmail() && valido;
  valido = validarTelefono() && valido;
  valido = validarDireccion() && valido;
  valido = validarCopias() && valido;
  valido = validarResolucion() && valido;
  valido = validarAnuncio() && valido;
  valido = validarRadios('impresion_color', 'Selecciona tipo de impresión') && valido;
  valido = validarRadios('mostrar_precio', 'Indica si mostrar el precio') && valido;

  if (valido) {
    e.target.submit();
  }
}

function validarCampo(id, mensaje) {
  const campo = document.getElementById(id);
  if (!campo || campo.value.trim() === '') {
    mostrarError(campo, mensaje);
    return false;
  }
  return true;
}

function validarEmail() {
  const campo = document.getElementById('correo');
  const email = campo.value.trim();

  if (!email || email.length > 254) {
    mostrarError(campo, 'Correo electrónico inválido');
    return false;
  }

  const partes = email.split('@');
  if (partes.length !== 2 || !partes[0] || !partes[1]) {
    mostrarError(campo, 'Correo electrónico inválido');
    return false;
  }

  // Comprobación adicional: el dominio debe tener un TLD (p.ej. .com, .org)
  const domain = partes[1];
  const domainParts = domain.split('.');
  if (domainParts.length < 2) {
    mostrarError(campo, 'El correo debe incluir un dominio válido (p.ej. .com, .org)');
    return false;
  }

  const tld = domainParts[domainParts.length - 1];
  // TLD: al menos 2 letras y solo letras (ASCII)
  if (tld.length < 2) {
    mostrarError(campo, 'Dominio de nivel superior inválido');
    return false;
  }
  return true;
}

function validarTelefono() {
  const campo = document.getElementById('telefono');
  const telefono = campo.value.trim();

  if (telefono === '') return true; // Opcional

  if (telefono.length !== 9) {
    mostrarError(campo, 'El teléfono debe tener 9 dígitos');
    return false;
  }

  for (let i = 0; i < telefono.length; i++) {
    const codigo = telefono.charCodeAt(i);
    if (codigo < 48 || codigo > 57) {
      mostrarError(campo, 'Solo se permiten dígitos');
      return false;
    }
  }

  return true;
}

function validarDireccion() {
  let valido = true;
    valido = validarCampo('calle', 'La calle es obligatoria.') && valido;
    // Número: obligatorio y solo dígitos
    if (validarCampo('numero', 'El número es obligatorio.') && valido) {
      const numero = document.getElementById('numero').value.trim();
      for (let i = 0; i < numero.length; i++) {
        const code = numero.charCodeAt(i);
        if (code < 48 || code > 57) {
          mostrarError(document.getElementById('numero'), 'El número debe contener solo dígitos.');
          valido = false;
          break;
        }
      }
    }

    // Código postal: obligatorio y solo dígitos
    if (validarCampo('codigo_postal', 'El código postal es obligatorio.') && valido) {
      const codigo_postal = document.getElementById('codigo_postal').value.trim();
      for (let i = 0; i < codigo_postal.length; i++) {
        const code = codigo_postal.charCodeAt(i);
        if (code < 48 || code > 57) {
          mostrarError(document.getElementById('codigo_postal'), 'El código postal debe contener solo dígitos.');
          valido = false;
          break;
        }
      }
    }

  return valido;
}

function validarCopias() {
  const campo = document.getElementById('copias');
  const valor = parseInt(campo.value);

  if (isNaN(valor) || valor < 1 || valor > 99) {
    mostrarError(campo, 'Debe ser un número entre 1 y 99');
    return false;
  }
  return true;
}

function validarResolucion() {
  const campo = document.getElementById('resolucion');
  const permitidas = ['150', '300', '450', '600', '750', '900'];

  if (permitidas.indexOf(campo.value) === -1) {
    mostrarError(campo, 'Resolución inválida');
    return false;
  }
  return true;
}

function validarAnuncio() {
  const campo = document.getElementById('anuncio');
  if (!campo.value) {
    mostrarError(campo, 'Debes seleccionar un anuncio');
    return false;
  }
  return true;
}

function validarRadios(nombre, mensaje) {
  const radios = document.getElementsByName(nombre);
  const checked = Array.from(radios).some(r => r.checked);

  if (!checked) {
    mostrarError(radios[0].closest('p'), mensaje);
    return false;
  }
  return true;
}

function mostrarError(elemento, mensaje) {
  elemento.classList.add('campo-error');

  const span = document.createElement('span');
  span.className = 'error-campo';
  span.textContent = mensaje;
  elemento.insertAdjacentElement('afterend', span);
}

function limpiarError(elemento) {
  elemento.classList.remove('campo-error');
  const error = elemento.nextElementSibling;
  if (error && error.classList.contains('error-campo')) {
    error.remove();
  }
}

function limpiarTodosLosErrores() {
  document.querySelectorAll('.campo-error').forEach(e => e.classList.remove('campo-error'));
  document.querySelectorAll('.error-campo').forEach(e => e.remove());
}