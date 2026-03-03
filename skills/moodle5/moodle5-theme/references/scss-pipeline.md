# Pipeline de compilación SCSS y presets

## Tabla de contenidos
1. [Las tres fases del pipeline](#las-tres-fases)
2. [Implementación de lib.php](#implementación-de-libphp)
3. [Cómo sobreescribir variables Bootstrap](#sobreescribir-variables)
4. [Presets y Bootswatch](#presets)
5. [Archivos SCSS del theme](#archivos-scss)

## Las tres fases

Moodle usa `scssphp/scssphp` (o el binario `sassc` si está configurado). La compilación sigue un orden estricto:

```
┌────────────────────────────────────────────────────┐
│  1. prescsscallback (get_pre_scss)                  │
│     → Variables SCSS sin !default: $primary: #e74c3c│
│     → SCSS crudo del textarea de admin               │
│                                                      │
│  2. $THEME->scss (get_main_scss_content)             │
│     → Preset con variables (!default)                │
│     → @import "moodle" (Bootstrap + Moodle)          │
│     → Reglas CSS del preset                          │
│                                                      │
│  3. extrascsscallback (get_extra_scss)               │
│     → SCSS dinámico (imágenes de fondo, etc.)        │
│     → SCSS crudo del textarea de admin               │
├────────────────────────────────────────────────────┤
│  Concatenado → Compilador SCSS → CSS en disco        │
└────────────────────────────────────────────────────┘
```

El orden es crítico: las variables en fase 1 (sin `!default`) sobreescriben las de Bootstrap (con `!default`). La fase 3 tiene la mayor prioridad por cascada CSS.

## Implementación de lib.php

Estas son las tres funciones principales que todo theme hijo de Boost necesita:

```php
<?php
defined('MOODLE_INTERNAL') || die();

/**
 * Fase 2: Retorna el SCSS principal (preset + pre/post propios).
 */
function theme_mytheme_get_main_scss_content($theme) {
    global $CFG;
    $scss = '';

    // Seleccionar preset (por defecto el de Boost)
    $filename = !empty($theme->settings->preset) ? $theme->settings->preset : null;
    $fs = get_file_storage();
    $context = context_system::instance();

    if ($filename == 'default.scss') {
        $scss .= file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/default.scss');
    } else if ($filename && ($presetfile = $fs->get_file(
            $context->id, 'theme_mytheme', 'preset', 0, '/', $filename))) {
        $scss .= $presetfile->get_content();
    } else {
        // Fallback al preset por defecto de Boost
        $scss .= file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/default.scss');
    }

    // Envolver con pre.scss y post.scss propios
    $pre = file_get_contents($CFG->dirroot . '/theme/mytheme/scss/pre.scss');
    $post = file_get_contents($CFG->dirroot . '/theme/mytheme/scss/post.scss');
    return $pre . "\n" . $scss . "\n" . $post;
}

/**
 * Fase 1: Variables SCSS que sobreescriben los !default de Bootstrap.
 * Se inyecta ANTES del preset.
 */
function theme_mytheme_get_pre_scss($theme) {
    $scss = '';

    // Mapeo setting → variable SCSS
    $configurable = [
        'brandcolor'     => ['primary'],
        'secondarycolor' => ['secondary'],
    ];

    foreach ($configurable as $configkey => $targets) {
        $value = $theme->settings->{$configkey} ?? null;
        if (empty($value)) continue;
        foreach ($targets as $target) {
            $scss .= '$' . $target . ': ' . $value . ";\n";
        }
    }

    // SCSS crudo del textarea de admin (pre)
    if (!empty($theme->settings->scsspre)) {
        $scss .= $theme->settings->scsspre;
    }

    return $scss;
}

/**
 * Fase 3: SCSS adicional después de todo (mayor prioridad por cascada).
 */
function theme_mytheme_get_extra_scss($theme) {
    $content = '';

    // SCSS crudo del textarea de admin (post)
    if (!empty($theme->settings->scss)) {
        $content .= $theme->settings->scss;
    }

    // Imagen de fondo de login desde settings
    $loginbgurl = $theme->setting_file_url('loginbackgroundimage', 'loginbackgroundimage');
    if (!empty($loginbgurl)) {
        $content .= "body.pagelayout-login #page {
            background-image: url('$loginbgurl');
            background-size: cover; }";
    }

    return $content;
}
```

## Sobreescribir variables

El mecanismo de override de variables funciona así:

1. **En pre-SCSS**: Definir sin `!default` → fuerza el valor
   ```scss
   $primary: #e74c3c;  // Sobreescribe el default de Bootstrap
   ```

2. **En el preset**: Bootstrap define con `!default` → solo aplica si no está definida
   ```scss
   $primary: #0d6efd !default;  // Se ignora porque ya existe de fase 1
   ```

**Flujo cuando un admin cambia el color de marca**:
1. Se guarda `#e74c3c` en `mdl_config_plugins`
2. `theme_reset_all_caches` incrementa `themerev`
3. Siguiente carga dispara recompilación
4. `get_pre_scss()` emite `$primary: #e74c3c;`
5. Bootstrap omite su `!default`
6. Todos los componentes con `$primary` se renderizan en el nuevo color

### Variables Bootstrap útiles para themes

```scss
// Colores principales
$primary: #0d6efd !default;
$secondary: #6c757d !default;
$success: #198754 !default;
$info: #0dcaf0 !default;
$warning: #ffc107 !default;
$danger: #dc3545 !default;
$light: #f8f9fa !default;
$dark: #212529 !default;

// Tipografía
$font-family-base: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif !default;
$font-size-base: 1rem !default;
$headings-font-weight: 500 !default;

// Espaciado y bordes
$border-radius: 0.375rem !default;
$border-radius-lg: 0.5rem !default;
$spacer: 1rem !default;

// Colores de actividad de Moodle (novedad 5.x)
$activity-icon-assessment-bg: #17857f !default;
$activity-icon-collaboration-bg: #f7634d !default;
$activity-icon-communication-bg: #eb66a2 !default;
$activity-icon-content-bg: #399be2 !default;
$activity-icon-interactivecontent-bg: #a378b4 !default;
```

## Presets

Un preset es un archivo `.scss` con tres secciones: variables → `@import "moodle"` → reglas personalizadas.

```scss
// Ejemplo de preset personalizado: mytheme/scss/preset/campus.scss
$primary: #1a5276;
$secondary: #2e86c1;
$body-bg: #fafafa;
$navbar-light-bg: #1a5276;

@import "moodle";

// Reglas posteriores al import
.navbar {
    box-shadow: 0 2px 4px rgba(0, 0, 0, .1);
}
```

Los presets pueden subirse desde la interfaz de admin usando `admin_setting_configstoredfile`.

## Archivos SCSS del theme

Organiza el SCSS del theme en dos archivos principales:

- **`scss/pre.scss`**: Imports, mixins y variables propias que deben estar disponibles antes del preset
- **`scss/post.scss`**: Reglas CSS personalizadas que se aplican después del preset

Ejemplo de `post.scss`:
```scss
// Personalización del header
.navbar {
    background-color: $primary;
    .nav-link {
        color: rgba(255, 255, 255, 0.85);
        &:hover { color: #fff; }
    }
}

// Personalización del footer
#page-footer {
    background-color: $dark;
    color: $light;
    padding: 2rem 0;
}

// Card de curso personalizado
.card.dashboard-card {
    border: none;
    border-radius: $border-radius-lg;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12);
    transition: box-shadow 0.2s ease;
    &:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
}
```
