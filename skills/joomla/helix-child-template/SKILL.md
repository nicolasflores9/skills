---
name: helix-child-template
description: Crea y personaliza templates hijo con Helix Ultimate para Joomla 5/6. Domina overrides protegidos, custom CSS/JS, posiciones de módulos, megamenús y actualizaciones seguras. Triggers - template hijo helix, child template joomla, helix ultimate, helix override, custom.css helix, template helix personalizar, helix framework
---

# Templates Hijo con Helix Ultimate para Joomla 5/6

## 1. Introducción Rápida

Helix Ultimate 2.x es el framework moderno para Joomla 4.4+, 5.x y 6.x. Los child templates permiten personalizar sin perder cambios en actualizaciones. Estructura mínima, máxima protección.

**Ventajas Clave:**
- Cambios separados de la plantilla padre
- Actualizaciones NO sobrescriben personalizaciones
- Sistema de overrides mejorado (v2.0.3+)
- Custom CSS/JS automaticamente cargados
- Herencia automática del padre

## 2. Estructura de Carpetas del Child Template

Crear estructura mínima:

```
templates/tu_child_template/
├── templateDetails.xml          (Obligatorio)
├── index.php                    (Opcional - solo si modificas)
├── css/
│   └── custom.css              (Crear manualmente)
├── js/
│   └── custom.js               (Crear manualmente)
├── html/
│   └── com_content/
│       └── article/
│           └── default.php     (Cargar override)
└── overrides/
    └── com_content/
        └── article/
            └── default.php     (Tu código personalizado)
```

**Solo incluye archivos que modificas.** El resto se hereda automáticamente del padre.

## 3. Archivo templateDetails.xml

Identificar la plantilla padre y registrar carpetas:

```xml
<?xml version="1.0" encoding="utf-8"?>
<extension type="template" client="site">
    <name>Mi Sitio Child</name>
    <version>1.0.0</version>
    <creationDate>2025-01-01</creationDate>
    <author>Tu Nombre</author>
    <copyright>Tu Copyright</copyright>
    <license>GNU General Public License v2.0 or later</license>
    <description>Child template personalizado para tu sitio</description>

    <!-- CRUCIAL: Especificar plantilla padre -->
    <inherits>shaper_helixultimate</inherits>

    <!-- Archivos a incluir en el paquete -->
    <files>
        <filename>index.php</filename>
        <filename>offline.php</filename>
        <filename>error.php</filename>
        <folder>css</folder>
        <folder>js</folder>
        <folder>html</folder>
        <folder>overrides</folder>
    </files>
</extension>
```

## 4. Sistema de Overrides (Nuevo v2.0.3+)

**Antiguo (< v2.0.3):** `/templates/template/html/` - Sobrescribible en updates
**Nuevo (v2.0.3+):** `/templates/template/overrides/` - PROTEGIDO en updates

Implementar nuevo sistema:

```php
// Archivo: /templates/tu_child_template/html/com_content/article/default.php
<?php
defined('_JEXEC') or die;
require HelixUltimate\Framework\Platform\HTMLOverride::loadTemplate();
?>

// Archivo: /templates/tu_child_template/overrides/com_content/article/default.php
<?php
defined('_JEXEC') or die;
$article = $this->item;
?>

<article class="article-custom">
    <h1><?php echo htmlspecialchars($article->title); ?></h1>

    <div class="metadata">
        Por: <?php echo $article->author; ?> |
        <?php echo JHtml::_('date', $article->publish_up, 'd/m/Y'); ?>
    </div>

    <div class="article-body">
        <?php echo $article->introtext; ?>
        <?php echo $article->fulltext; ?>
    </div>
</article>
```

## 5. Personalización: Custom CSS

Crear `/templates/tu_child_template/css/custom.css` con tus estilos:

```css
:root {
    --primary: #2c3e50;
    --secondary: #3498db;
    --spacing: 8px;
}

body {
    background-color: #f5f5f5;
    font-family: 'Open Sans', sans-serif;
}

h1, h2, h3 {
    color: var(--primary);
    font-weight: 600;
    margin: calc(var(--spacing) * 2) 0;
}

.article-custom {
    background: white;
    padding: calc(var(--spacing) * 3);
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Responsive */
@media (max-width: 768px) {
    h1 { font-size: 1.8rem; }
    body { font-size: 0.95rem; }
}
```

Se carga automáticamente DESPUÉS de template.css - tus estilos sobrescriben padres.

## 6. Personalización: Custom JavaScript

Crear `/templates/tu_child_template/js/custom.js`:

```javascript
(function() {
    'use strict';

    const MyTemplate = {
        init: function() {
            console.log('[MyTemplate] Inicializando');
            this.setupMenus();
            this.setupForms();
        },

        setupMenus: function() {
            const toggle = document.querySelector('.menu-toggle');
            if (toggle) {
                toggle.addEventListener('click', (e) => {
                    e.preventDefault();
                    document.querySelector('.mobile-menu')?.classList.toggle('active');
                });
            }
        },

        setupForms: function() {
            const forms = document.querySelectorAll('form[data-validate]');
            forms.forEach(form => {
                form.addEventListener('submit', (e) => {
                    if (!this.validateForm(form)) {
                        e.preventDefault();
                    }
                });
            });
        },

        validateForm: function(form) {
            return Array.from(form.querySelectorAll('[required]'))
                .every(input => input.value.trim() !== '');
        }
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => MyTemplate.init());
    } else {
        MyTemplate.init();
    }

    window.MyTemplate = MyTemplate;
})();
```

Carga DESPUÉS de todos los otros scripts - seguro para sobrescribir comportamientos.

## 7. Template Options: Inyección de Código

Panel Administrativo > System > Templates > Tu Template > Custom Code tab:

- **Before </head>:** Meta tags, link tags, CSS inline
- **Before </body>:** Google Analytics, tracking code
- **Custom CSS:** Estilos CSS inline (< 30 líneas)
- **Custom Javascript:** Scripts inline (ejecutan al final)

Usar para cambios pequeños. Para cambios medianos/grandes usar custom.css/js files.

## 8. Posiciones de Módulos

Helix include 30+ posiciones predeterminadas:

```
logo, logo-title, logo-tagline
menu, menu-modal, search
slide, title, breadcrumb
top1, top2, top3, user1, user2, user3, user4, feature
left, right
content-top, content-bottom
footer1, footer2, bottom1, bottom2, bottom3, bottom4
offcanvas, pagebuilder, 404, debug
```

**Agregar nueva posición en index.php:**

```php
<?php
if ($this->countModules('mi-posicion-custom')) {
    echo '<div class="custom-section">';
    echo $this->getBuffer('module', 'mi-posicion-custom');
    echo '</div>';
}
?>
```

**Registrar en templateDetails.xml:**

```xml
<positions>
    <position>mi-posicion-custom</position>
</positions>
```

## 9. Configuración de Opciones Helix

Panel Template Options en admin:

**Tipografía:** 950+ Google Fonts disponibles - configurar Body, H1-H6, Navigation

**Colores:** 8 presets visuales + Custom Style para colores personalizados

**Layout Builder:** Agregar/remover posiciones, ajustar ancho de columnas por dispositivo

**Megamenú:** Tipo (Standard/Grid/Accordion), ancho dropdown, animación, menu builder integrado

**Blog Settings:** Layout, items por página, mostrar/ocultar elementos

**Custom Code:** CSS y Javascript inline para cambios pequeños

## 10. Mantener Actualizaciones Sin Perder Cambios

**Qué se sobrescribe:**
- index.php (archivo padre)
- css/template.css
- js/template.js
- html/ (sistema antiguo - NO usar)

**Qué NO se sobrescribe:**
- overrides/ (NUEVO sistema protegido)
- css/custom.css
- js/custom.js
- scss/custom.scss
- Template parameters guardados
- Child templates

**Checklist de actualización:**

```
ANTES:
☐ Backup del template actual
☐ Documentar overrides en /overrides/
☐ Verificar custom.css/js existen
☐ Anotar cambios con git diff

ACTUALIZAR:
☐ Admin > Templates > actualizar template
☐ Revisar qué archivos cambiaron
☐ Verificar /overrides/ intacto
☐ Probar en navegadores múltiples

DESPUÉS:
☐ Revisar error_log de PHP
☐ Probar todos los overrides
☐ Probar módulos y componentes
☐ Guardar en control versiones
```

## 11. Compatibilidad Joomla 6

Helix Ultimate 2.2.x+ es compatible con Joomla 6.

**Requisitos:**
- PHP 8.1+ (recomendado 8.3+)
- Deshabilitar plugin "Behaviour - Backward Compatibility"
- Actualizar extensiones que usan APIs deprecadas

**Verificar:**
- Todas las posiciones de módulos funcionan
- Overrides personalizados se cargan correctamente
- No hay errores en console de navegador
- No hay errores en error_log de PHP

## 12. Estructura Completa de Ejemplo

```
templates/mi_tienda_child/
├── templateDetails.xml
├── css/
│   ├── custom.css
│   └── ecommerce.css
├── js/
│   ├── custom.js
│   └── cart-handler.js
├── html/
│   ├── com_content/
│   │   └── article/
│   │       └── default.php
│   ├── com_virtuemart/
│   │   └── productdetails/
│   │       └── default.php
│   └── mod_menu/
│       └── default.php
├── overrides/
│   ├── com_content/
│   │   └── article/
│   │       └── default.php
│   ├── com_virtuemart/
│   │   └── productdetails/
│   │       └── default.php
│   └── mod_menu/
│       └── default.php
└── images/
    └── [imágenes custom]
```

## 13. Troubleshooting Común

**Overrides no aparecen:**
- Verificar ruta exacta `/overrides/com_nombre/vista/default.php`
- Revisar que /html/ carga correctamente con `loadTemplate()`
- Verificar permisos de archivo (644 típico)
- Limpiar caché Joomla > System > Clear Cache

**CSS no aplica:**
- Verificar custom.css está en `/css/custom.css`
- CSS debe estar DESPUÉS de template.css (especificidad)
- Usar `!important` solo si necesario
- Verificar no hay minificación conflictiva

**JavaScript no ejecuta:**
- Custom.js carga ÚLTIMO - seguro para jQuery/plugins
- Usar IIFE `(function() {...})()` para evitar conflictos globales
- Verificar `DOMContentLoaded` antes de acceder a elementos
- Revisar console del navegador por errores

**Posiciones no visibles:**
- Usar `<?php if ($this->countModules('nombre')) ?>` para verificar
- Módulos deben estar asignados a menú item
- Verificar nombre exacto de posición
- Revisar HTML generado con inspector

## 14. Best Practices

1. **Versionado Git:** Trackea cambios en `/css/custom.css`, `/js/custom.js`, `/overrides/`
2. **Documentación:** Comenta código complejo con autor y fecha
3. **Modularidad:** Separar CSS/JS por función (menu.css, footer.js)
4. **Testing:** Probar en Chrome, Firefox, Safari, móvil
5. **Seguridad:** Sanitizar outputs con `htmlspecialchars()`, validar inputs
6. **Performance:** Minificar CSS/JS en producción, lazy loading para imágenes
7. **Accesibilidad:** ARIA labels, keyboard navigation, contrast ratios WCAG 2.1
8. **Naming:** Usar kebab-case para clases CSS, camelCase para JavaScript

## 15. Recursos & Documentación

**Oficial:**
- https://www.joomshaper.com/documentation
- https://docs.joomla.org (Joomla Core)

**Comunidad:**
- JoomShaper Forum: helix-ultimate support
- Joomla Forum: Template discussions
- GitHub: JoomShaper/helix-ultimate

**Tools Útiles:**
- Browser DevTools (F12) - debugging
- XAMPP/Local Joomla - testing local
- Git - control de versiones
- VS Code/Sublime - editor código

---

**Actualizado:** Marzo 2025 | Helix Ultimate 2.x | Joomla 5/6
