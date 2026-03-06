---
name: joomla-frontend-integration
description: Domina integración frontend en Joomla 5/6 con Helix Framework.
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
