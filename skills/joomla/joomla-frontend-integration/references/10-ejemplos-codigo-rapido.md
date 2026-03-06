# Snippets de Código Rápido - Joomla Frontend

## Snippets PHP

### Obtener WebAssetManager
```php
$wa = \Joomla\CMS\Factory::getApplication()
      ->getDocument()
      ->getWebAssetManager();
```

### Cargar Script
```php
$wa->useScript('jquery');
$wa->useScript('my-custom-script');
```

### Cargar Style
```php
$wa->useStyle('bootstrap');
$wa->useStyle('my-custom-style');
```

### Registrar y usar Script dinámicamente
```php
$wa->registerAndUseScript(
    'my-dynamic-script',
    'js/dynamic.js',
    ['dependencies' => ['jquery']],
    ['defer' => true]
);
```

### Agregar Inline Script
```php
$wa->addInlineScript('
  console.log("Inline script");
');
```

### Agregar Inline Style
```php
$wa->addInlineStyle('
  .mi-clase { color: red; }
');
```

---

## JSON Assets

### Asset Simple
```json
{
  "name": "my-script",
  "type": "script",
  "uri": "js/myfile.js",
  "dependencies": ["jquery"],
  "attributes": {
    "defer": true,
    "async": false
  },
  "version": "auto"
}
```

### Style Asset
```json
{
  "name": "my-style",
  "type": "style",
  "uri": "css/myfile.css",
  "version": "auto"
}
```

### Script con CDN
```json
{
  "name": "cdnjs-lib",
  "type": "script",
  "uri": "https://cdn.example.com/lib.min.js",
  "attributes": {
    "defer": true
  },
  "version": "1.2.3"
}
```

### Preset (Grupo de Assets)
```json
{
  "name": "my-preset",
  "type": "preset",
  "uri": "",
  "dependencies": [
    "jquery#script",
    "bootstrap#script",
    "bootstrap#style"
  ]
}
```

---

## CSS Snippets

### Mobile First Base
```css
/* Mobile first - estilos base para mobile */
.elemento {
  padding: 1rem;
  font-size: 14px;
}

/* Tablet (768px) */
@media (min-width: 768px) {
  .elemento {
    padding: 2rem;
    font-size: 16px;
  }
}

/* Desktop (992px) */
@media (min-width: 992px) {
  .elemento {
    padding: 3rem;
    font-size: 18px;
  }
}
```

### Flexbox Responsivo
```css
.flex-container {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

@media (min-width: 768px) {
  .flex-container {
    flex-direction: row;
    justify-content: space-between;
  }
}
```

### Grid Responsivo
```css
.grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 2rem;
}

@media (min-width: 768px) {
  .grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (min-width: 992px) {
  .grid {
    grid-template-columns: repeat(3, 1fr);
  }
}
```

### Botón Personalizado
```css
.btn-custom {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  padding: 12px 24px;
  border-radius: 4px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.btn-custom:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

.btn-custom:active {
  transform: translateY(0);
}
```

### Card Bootstrap Mejorada
```css
.card-custom {
  border: none;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease;
}

.card-custom:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
}

.card-custom .card-header {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
}
```

### Imagen Responsiva
```css
.img-responsive {
  max-width: 100%;
  height: auto;
  display: block;
}

.img-contain {
  width: 100%;
  height: 300px;
  object-fit: contain;
  background: #f5f5f5;
}
```

### Tema Oscuro
```css
@media (prefers-color-scheme: dark) {
  body {
    background: #1a1a1a;
    color: #e0e0e0;
  }

  .card {
    background: #2a2a2a;
    color: #e0e0e0;
  }
}
```

### Accesibilidad - Reducir Movimiento
```css
@media (prefers-reduced-motion: reduce) {
  * {
    animation-duration: 0.01ms !important;
    animation-iteration-count: 1 !important;
    transition-duration: 0.01ms !important;
  }
}
```

---

## JavaScript Snippets

### DOM Ready
```javascript
document.addEventListener('DOMContentLoaded', function() {
  // Tu código aquí
  console.log('DOM listo');
});
```

### Seleccionar Elementos
```javascript
// Un elemento
const elemento = document.querySelector('.mi-clase');

// Múltiples elementos
const elementos = document.querySelectorAll('.items');

// Por ID
const elemento2 = document.getElementById('mi-id');
```

### Event Listeners
```javascript
// Click
elemento.addEventListener('click', function() {
  console.log('Clicked');
});

// Submit form
const form = document.querySelector('form');
form.addEventListener('submit', function(e) {
  e.preventDefault();
  console.log('Form enviado');
});

// Keyboard
document.addEventListener('keydown', function(e) {
  if (e.key === 'Enter') {
    console.log('Enter presionado');
  }
});
```

### Manipular Clases
```javascript
const elemento = document.querySelector('.elemento');

// Agregar clase
elemento.classList.add('activo');

// Quitar clase
elemento.classList.remove('oculto');

// Toggle clase
elemento.classList.toggle('visible');

// Verificar si tiene clase
if (elemento.classList.contains('activo')) {
  console.log('Tiene clase activo');
}
```

### Modificar Atributos
```javascript
const elemento = document.querySelector('.elemento');

// Obtener atributo
const valor = elemento.getAttribute('data-id');

// Setear atributo
elemento.setAttribute('data-id', '123');

// Verificar atributo
if (elemento.hasAttribute('data-toggle')) {
  console.log('Tiene atributo');
}
```

### AJAX / Fetch
```javascript
// Fetch GET
fetch('/api/data')
  .then(response => response.json())
  .then(data => {
    console.log(data);
  })
  .catch(error => console.error('Error:', error));

// Fetch POST
fetch('/api/submit', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({
    name: 'Juan',
    email: 'juan@example.com'
  })
})
.then(response => response.json())
.then(data => console.log('Success:', data));
```

### Custom Event
```javascript
// Crear evento custom
const evento = new CustomEvent('mi-evento', {
  detail: { mensaje: 'Hola' },
  bubbles: true,
  composed: true
});

// Disparar evento
elemento.dispatchEvent(evento);

// Escuchar evento custom
documento.addEventListener('mi-evento', function(e) {
  console.log(e.detail.mensaje);
});
```

### Smooth Scroll
```javascript
document.querySelectorAll('a[href^="#"]').forEach(link => {
  link.addEventListener('click', function(e) {
    e.preventDefault();
    const target = document.querySelector(this.getAttribute('href'));
    if(target) {
      target.scrollIntoView({
        behavior: 'smooth',
        block: 'start'
      });
    }
  });
});
```

### LocalStorage
```javascript
// Guardar dato
localStorage.setItem('miDato', 'valor');

// Obtener dato
const valor = localStorage.getItem('miDato');

// Remover dato
localStorage.removeItem('miDato');

// Limpiar todo
localStorage.clear();
```

### Validar Formulario
```javascript
const form = document.querySelector('form');

form.addEventListener('submit', function(e) {
  const email = document.querySelector('input[name="email"]');

  if (!email.value.includes('@')) {
    e.preventDefault();
    email.classList.add('is-invalid');
    console.log('Email inválido');
  }
});
```

### Throttle/Debounce
```javascript
// Debounce - ejecutar después de dejar de escribir
function debounce(func, wait) {
  let timeout;
  return function(...args) {
    clearTimeout(timeout);
    timeout = setTimeout(() => func.apply(this, args), wait);
  };
}

const buscar = debounce(function(query) {
  console.log('Buscando:', query);
}, 300);

// Throttle - ejecutar máximo cada X tiempo
function throttle(func, limit) {
  let inThrottle;
  return function(...args) {
    if (!inThrottle) {
      func.apply(this, args);
      inThrottle = true;
      setTimeout(() => inThrottle = false, limit);
    }
  };
}

const onScroll = throttle(function() {
  console.log('Scrolling');
}, 1000);
```

---

## jQuery (si estás usando)

### DOM Ready
```javascript
jQuery(document).ready(function($) {
  // Tu código con jQuery
});
```

### Seleccionar Elementos
```javascript
const elemento = $('.mi-clase');
const elementos = $('.items');
const elemento2 = $('#mi-id');
```

### Event Listeners
```javascript
$('.elemento').on('click', function() {
  console.log('Clicked');
});
```

### Modificar Clases
```javascript
$('.elemento').addClass('activo');
$('.elemento').removeClass('oculto');
$('.elemento').toggleClass('visible');
```

### AJAX jQuery
```javascript
$.ajax({
  url: '/api/data',
  type: 'GET',
  dataType: 'json',
  success: function(data) {
    console.log(data);
  },
  error: function(error) {
    console.error('Error:', error);
  }
});
```

---

## Web Components

### Usar Web Component
```html
<mi-componente
  titulo="Mi Componente"
  descripcion="Descripción"
  clickable="true">
</mi-componente>
```

### Escuchar Evento Custom
```javascript
document.addEventListener('mi-evento', function(e) {
  console.log(e.detail);
});
```

### Crear Web Component Simple
```javascript
class MiComponente extends HTMLElement {
  connectedCallback() {
    this.innerHTML = `
      <div class="mi-componente">
        <h2>${this.getAttribute('titulo')}</h2>
      </div>
    `;
  }
}

customElements.define('mi-componente', MiComponente);
```

---

## Helix Custom Code

### CSS en Helix
```css
/* Template Options → Custom Code → Custom CSS */
.my-navbar {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.my-button {
  background: #667eea;
  color: white;
  padding: 12px 24px;
}
```

### JavaScript en Helix
```javascript
/* Template Options → Custom Code → Custom JavaScript */
document.addEventListener('DOMContentLoaded', function() {
  console.log('Helix Custom JS');

  // Smooth scroll
  document.querySelectorAll('a[href^="#"]').forEach(link => {
    link.addEventListener('click', function(e) {
      e.preventDefault();
      const target = document.querySelector(this.getAttribute('href'));
      if(target) target.scrollIntoView({behavior: 'smooth'});
    });
  });
});
```

---

## Debugging

### Console
```javascript
console.log('Mensaje');          // Información
console.warn('Advertencia');     // Advertencia
console.error('Error');          // Error
console.table(datos);            // Tabla
console.time('nombre');          // Iniciar timer
console.timeEnd('nombre');       // Fin timer
```

### Breakpoints
```javascript
debugger; // El código se pausa aquí en DevTools
```

### Ver el DOM
```javascript
console.log(document.documentElement.outerHTML); // HTML completo
```

---

Última actualización: Marzo 2026
