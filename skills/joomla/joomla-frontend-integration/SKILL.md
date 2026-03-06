---
name: joomla-frontend-integration
description: Domina integración frontend (CSS/JS) en Joomla 5/6 con Helix Framework. WebAssetManager, Bootstrap 5, módulos custom, SP Page Builder, responsive design, Web Components. Triggers: css joomla, javascript joomla, WebAssetManager, cargar css joomla, assets joomla, bootstrap helix, frontend joomla, joomla.asset.json, helix custom code, componentes responsive
level: intermediate-advanced
---

# Integración Frontend en Joomla 5/6 con Helix Framework

Guía completa para dominar CSS/JavaScript, WebAssetManager y diseño responsive en Joomla 5/6 utilizando Helix Framework y Bootstrap 5.

---

## 1. WebAssetManager: El Sistema Moderno

El **WebAssetManager** (`\Joomla\CMS\WebAsset\WebAssetManager`) es la forma oficial de gestionar assets en Joomla 5/6. Reemplaza HTMLHelper y Document API (deprecados en 5.3+).

### Ciclo de vida
1. **Registered**: Asset declarado en `joomla.asset.json`
2. **Used**: Activado con `$wa->useScript()` o `$wa->useStyle()`
3. **Rendered**: Insertado en HTML por `<jdoc:include type="head" />`
4. **Loaded**: Descargado por navegador con versionado automático

### Obtener instancia
```php
$wa = \Joomla\CMS\Factory::getApplication()
      ->getDocument()
      ->getWebAssetManager();

// Cargar assets
$wa->useScript('jquery');
$wa->useStyle('bootstrap');
```

### Niveles de carga en Joomla
```
1. media/vendor/joomla.asset.json
2. media/system/joomla.asset.json
3. media/legacy/joomla.asset.json
4. media/com_active/joomla.asset.json
5. templates/active_template/joomla.asset.json
```

---

## 2. Estructura joomla.asset.json

Archivo JSON que declara todos los assets de forma centralizada. Se carga automáticamente.

### Estructura Básica
```json
{
  "$schema": "https://json.schemastore.org/joomla-manifest-schema.json",
  "name": "myext",
  "version": "1.0.0",
  "assets": [
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
    },
    {
      "name": "my-style",
      "type": "style",
      "uri": "css/myfile.css",
      "dependencies": [],
      "version": "auto"
    }
  ]
}
```

### Ubicaciones según tipo
- **Módulo**: `/mod_mymod/joomla.asset.json`
- **Componente**: `/com_mycomp/joomla.asset.json`
- **Plugin**: `/plg_group_name/joomla.asset.json`
- **Template**: `/templates/mytemplate/joomla.asset.json`

### Versioning automático
```json
"version": "auto"  // Usa fecha de última modificación
"version": "1.0.0" // Versión manual
```

---

## 3. Cargar CSS y JavaScript Correctamente

### Método 1: WebAssetManager (RECOMENDADO)
```php
// En helper.php, layout o componente
$wa = \Joomla\CMS\Factory::getApplication()
      ->getDocument()
      ->getWebAssetManager();

// Scripts
$wa->useScript('jquery');
$wa->useScript('bootstrap');
$wa->useScript('my-custom-script');

// Styles
$wa->useStyle('bootstrap');
$wa->useStyle('my-custom-style');
```

### Alternativas (Dinámico, CDN, Legacy)
```php
// Registrar dinámicamente
$wa->registerAndUseScript('my-dynamic', 'js/dynamic.js',
    ['dependencies' => ['jquery']], ['defer' => true]);

// Desde CDN en joomla.asset.json
{"name": "lib", "type": "script",
 "uri": "https://cdn.example.com/lib.min.js"}

// Legacy (evitar)
\Joomla\CMS\HTML\HTMLHelper::script('js/file.js');
```

---

## 4. Bootstrap 5 con Helix Framework

Helix incluye **Bootstrap 5.2.3+** automáticamente. Grid de 12 columnas, flexbox, utilidades.

### Grid Responsivo
```html
<!-- Bootstrap grid integrado -->
<div class="container">
  <div class="row">
    <div class="col-12 col-md-6 col-lg-4">
      <!-- Contenido: 100% mobile, 50% tablet, 33% desktop -->
    </div>
  </div>
</div>
```

### Breakpoints Bootstrap
```css
/* xs: 0px (mobile first) */
/* sm: 576px (landscape phones) */
/* md: 768px (tablets) */
/* lg: 992px (desktops) */
/* xl: 1200px (large desktops) */
/* xxl: 1400px (very large) */
```

### Media Queries
```css
/* Mobile first: estilos base para móvil */
.card {
  padding: 1rem;
  font-size: 14px;
}

/* Mejorar en tablets */
@media (min-width: 768px) {
  .card {
    padding: 2rem;
    font-size: 16px;
  }
}

/* Mejorar en desktops */
@media (min-width: 992px) {
  .card {
    padding: 3rem;
    font-size: 18px;
  }
}
```

### No romper Bootstrap
```css
/* INCORRECTO: sobrescribir clases Bootstrap */
.btn { background: red; }

/* CORRECTO: crear clases custom */
.btn-custom { background: red; }
.btn-custom:hover { background: darkred; }
```

---

## 5. Módulos Custom con Assets

**Estructura:**
```
/mod_mymodule/
  ├── joomla.asset.json
  ├── helper.php
  ├── mod_mymodule.php
  ├── tmpl/default.php
  ├── js/module.js
  └── css/module.css
```

**Activar en helper.php o template:**
```php
$wa = \Joomla\CMS\Factory::getApplication()
      ->getDocument()
      ->getWebAssetManager();
$wa->useScript('mod_mymodule.script');
$wa->useStyle('mod_mymodule.style');
```

Ver ejemplo completo en `references/02-modulo-testimonios-completo.php`.

---

## 6. SP Page Builder: Assets en Addons

**CSS Nativo:** Row/Column → Custom CSS (CodeMirror editor disponible).

**JS Custom:** Crear plugin tipo sppagebuilder (no soporta JS nativo).

Ver ejemplo completo en `references/06-sp-page-builder-addon-plugin.php`.

---

## 7. Dependencias Entre Assets

Sistema automático de resolución de orden de carga.

### Dependencia simple
```json
{
  "name": "my-plugin",
  "type": "script",
  "uri": "js/plugin.js",
  "dependencies": ["jquery"]
}
```

### Dependencias múltiples
```json
{
  "name": "advanced-plugin",
  "type": "script",
  "uri": "js/advanced.js",
  "dependencies": [
    "jquery",
    "bootstrap.util",
    "popper"
  ]
}
```

### Dependencia con tipo específico
```json
{
  "name": "my-component",
  "type": "script",
  "uri": "js/component.js",
  "dependencies": [
    "jquery#script",
    "bootstrap.util#script",
    "theme-dark#style"
  ]
}
```

### Preset (grupo de dependencias)
```json
{
  "name": "admin-bundle",
  "type": "preset",
  "uri": "",
  "dependencies": [
    "jquery#script",
    "popper#script",
    "bootstrap#script",
    "bootstrap#style"
  ]
}
```

---

## 8. Inline Scripts y Styles

Para código pequeño o dinámico.

### Inline Script
```php
$wa = Factory::getApplication()
      ->getDocument()
      ->getWebAssetManager();

$wa->addInlineScript('
  document.addEventListener("DOMContentLoaded", function() {
    console.log("Inline script ejecutado");
  });
');
```

### Inline Style
```php
$wa->addInlineStyle('
  .banner-custom {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px;
  }
');
```

### Inline nombrado (como dependencia)
```php
$wa->registerAndUseScript(
    'my-init',
    'document.addEventListener("DOMContentLoaded", function() { /* init */ });'
);

// Usar como dependencia
$wa->registerAndUseScript(
    'my-app',
    'js/app.js',
    ['dependencies' => ['my-init']]
);
```

---

## 9. Defer, Async y Optimización

Atributos críticos para rendimiento.

### Defer
Se ejecuta DESPUÉS de cargar HTML. **Recomendado para la mayoría.**

```json
{
  "name": "my-script",
  "type": "script",
  "uri": "js/my-script.js",
  "attributes": {
    "defer": true,
    "async": false
  }
}
```

**Usar para:**
- jQuery
- Bootstrap
- Scripts custom que dependen de jQuery

### Async
Se ejecuta CUANDO está listo. **Para código independiente.**

```json
{
  "name": "analytics",
  "type": "script",
  "uri": "https://analytics.example.com/track.js",
  "attributes": {
    "defer": false,
    "async": true
  }
}
```

**Usar para:**
- Google Analytics
- Tracking scripts
- Chat widgets
- Code independiente

### Matriz de decisión
```
jQuery / Bootstrap          → defer: true, async: false
Custom JS que usa jQuery    → defer: true, async: false
Analytics / Tracking        → defer: false, async: true
3rd party widget           → async: true
```

---

## 10. Web Components

Estándar W3C nativo. Joomla incluye `joomla-core-loader`.

```javascript
// Crear Web Component
class MiComponente extends HTMLElement {
  connectedCallback() {
    this.innerHTML = `<div>${this.getAttribute('titulo')}</div>`;
  }
}
customElements.define('mi-componente', MiComponente);

// Registrar en joomla.asset.json
// {"name": "mi-comp", "type": "script", "uri": "js/mi-comp.js",
//  "attributes": {"type": "module", "defer": true}}
```

Ver ejemplo completo en `references/07-web-component-custom.js`.

---

## 11. Custom Code en Helix

Admin → Templates → [Tu Template] → Template Options → Custom Code tab.

**CSS Global:**
```css
.site-header {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
.custom-button {
  padding: 12px 24px;
  border-radius: 4px;
  transition: all 0.3s ease;
}
```

**JS Global & Google Fonts:** Ver ejemplos en `references/08-template-helix-custom.css` y `references/10-ejemplos-codigo-rapido.md`.

---

## 12. Buenas Prácticas: No Romper Helix

### NUNCA editar
```
/template/css/template.css        ✗
/template/css/bootstrap.css        ✗
/template/js/template.js           ✗
```

### SIEMPRE crear/usar
```
/template/css/custom.css           ✓
/template/scss/custom.scss         ✓
/template/js/custom.js             ✓
Custom Code section en admin       ✓
```

### CSS Cascade seguro
```css
/* template.css carga primero */
.btn { padding: 8px 12px; }

/* custom.css carga al final - tiene prioridad */
.btn { padding: 10px 16px; }
```

### Preservar clases Bootstrap
```css
/* INCORRECTO: modifica clase Bootstrap */
.btn { background: purple; }

/* CORRECTO: crea clase custom */
.btn-primary-custom { background: purple; }

<!-- Usar clase custom -->
<button class="btn btn-primary-custom">Botón</button>
```

---

## 13. Troubleshooting Rápido

**Assets no cargan:** Revisar `<jdoc:include type="head" />` en template, path en joomla.asset.json, permisos (755), cache Joomla.

**Orden incorrecto:** Verificar dependencias, revisar Network tab en DevTools.

**Bootstrap no funciona:** Confirmar `$wa->useStyle('bootstrap')`, no sobrescribir variables, usar clases correctamente.

**Helix se rompe:** No editar archivos core, usar custom.css, revisar Custom Code.

Más detalles en `references/09-checklist-desarrollo.md`.

---

## 14. Referencias Rápidas y Recursos

**Documentación oficial:**
- [Joomla 5 Web Asset Manager Manual](https://manual.joomla.org/docs/5.0/general-concepts/web-asset-manager/)
- [Helix Framework Docs](https://www.joomshaper.com/documentation/helix-framework/)
- [Bootstrap 5 Docs](https://getbootstrap.com/docs/5.0/)
- [SP Page Builder Docs](https://www.joomshaper.com/documentation/sp-page-builder/how-to-tips)

**Guías comunitarias:**
- [Kevin's Guides - Web Asset Manager](https://kevinsguides.com/guides/webdev/joomla/ref/webassetmanager/)
- [Dionysopoulos - Web Asset Management](https://www.dionysopoulos.me/component/docimport/article/concepts-webassetmanager.html)

**Repositorios:**
- [Joomla Custom Elements](https://github.com/joomla-projects/custom-elements)
- [Joomla CMS GitHub](https://github.com/joomla/joomla-cms)

**Performance:**
- [Google PageSpeed Insights](https://pagespeed.web.dev/)
- [WebPageTest](https://www.webpagetest.org/)

---

**Version:** 1.0
**Última actualización:** Marzo 2026
**Autor:** Claude
**Joomla:** 5.6+
**Framework:** Helix Ultimate 2.x+
**Bootstrap:** 5.2.3+
