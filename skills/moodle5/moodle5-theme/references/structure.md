# Estructura de directorios y archivos obligatorios

## Tabla de contenidos
1. [Estructura completa de un theme hijo de Boost](#estructura-completa)
2. [Archivos obligatorios](#archivos-obligatorios)
3. [Herencia de themes](#herencia-de-themes)
4. [Scaffolding de un theme nuevo](#scaffolding)

## Estructura completa

En Moodle 5.1+, los themes residen bajo `public/theme/` (el DocumentRoot apunta a `/public/`):

```
public/theme/mytheme/
├── amd/
│   └── src/                    # Módulos JavaScript AMD (ES6)
├── classes/
│   └── output/                 # Renderers personalizados
├── fonts/                      # Fuentes personalizadas
├── fonts_core/                 # Sobreescritura de fuentes del core
├── fonts_plugins/
│   └── plugintype/pluginname/
├── lang/
│   └── en/
│       └── theme_mytheme.php   # Cadenas de idioma (OBLIGATORIO)
├── layout/                     # Archivos PHP de layout
├── pix/
│   ├── favicon.ico
│   └── screenshot.png          # ~500x400px para el selector de themes
├── pix_core/                   # Override de iconos del core
├── pix_plugins/                # Override de iconos de plugins
├── scss/                       # Archivos fuente SCSS
├── style/                      # CSS compilado (solo si no se usa SCSS)
├── templates/                  # Overrides de templates Mustache
│   └── core/
│       └── templatename.mustache
├── config.php                  # OBLIGATORIO
├── lib.php                     # OBLIGATORIO
├── settings.php                # Opcional: ajustes de admin
└── version.php                 # OBLIGATORIO
```

## Archivos obligatorios

### version.php

Declara metadatos del plugin. `$plugin->supported` limita ramas compatibles, `$plugin->dependencies` garantiza que el theme padre esté presente:

```php
<?php
defined('MOODLE_INTERNAL') || die();

$plugin->version   = 2025100600;        // Formato YYYYMMDDXX
$plugin->requires  = 2025041600;        // Versión mínima de Moodle (5.0)
$plugin->supported = [501, 501];        // Ramas soportadas
$plugin->component = 'theme_mytheme';   // Nombre Frankenstyle
$plugin->maturity  = MATURITY_STABLE;
$plugin->release   = '1.0.0';
$plugin->dependencies = [
    'theme_boost' => 2025041600,
];
```

### config.php

El archivo más importante. Define herencia, layouts, compilación SCSS y comportamiento:

```php
<?php
defined('MOODLE_INTERNAL') || die();
require_once(__DIR__ . '/lib.php');

$THEME->name = 'mytheme';
$THEME->parents = ['boost'];
$THEME->sheets = [];
$THEME->editor_scss = ['editor'];
$THEME->usefallback = true;

// Callbacks SCSS (tres fases del pipeline)
$THEME->scss = function($theme) {
    return theme_mytheme_get_main_scss_content($theme);
};
$THEME->prescsscallback = 'theme_mytheme_get_pre_scss';
$THEME->extrascsscallback = 'theme_mytheme_get_extra_scss';
$THEME->precompiledcsscallback = 'theme_mytheme_get_precompiled_css';

// Comportamiento del theme
$THEME->rendererfactory = 'theme_overridden_renderer_factory';
$THEME->iconsystem = \core\output\icon_system::FONTAWESOME;
$THEME->haseditswitch = true;
$THEME->usescourseindex = true;
$THEME->enable_dock = false;
$THEME->requiredblocks = '';
$THEME->addblockposition = BLOCK_ADDBLOCK_POSITION_FLATNAV;

// Layouts — en 5.1+ heredan automáticamente del padre
// Solo es necesario redefinir los que se quieran personalizar
$THEME->layouts = [
    'base'      => ['file' => 'drawers.php', 'regions' => []],
    'standard'  => ['file' => 'drawers.php', 'regions' => ['side-pre'],
                     'defaultregion' => 'side-pre'],
    'course'    => ['file' => 'drawers.php', 'regions' => ['side-pre'],
                     'defaultregion' => 'side-pre',
                     'options' => ['langmenu' => true]],
    'frontpage' => ['file' => 'drawers.php', 'regions' => ['side-pre'],
                     'defaultregion' => 'side-pre',
                     'options' => ['nonavbar' => true]],
    'login'     => ['file' => 'login.php', 'regions' => [],
                     'options' => ['langmenu' => true]],
    'popup'     => ['file' => 'columns1.php', 'regions' => [],
                     'options' => ['nofooter' => true, 'nonavbar' => true]],
    'maintenance' => ['file' => 'maintenance.php', 'regions' => []],
];
```

**Layouts disponibles en Boost**: `drawers.php` (principal con nav lateral), `columns1.php` (columna única), `login.php`, `embedded.php`, `maintenance.php`, `secure.php`.

**Novedad 5.1 (MDL-79319)**: Los layouts heredan automáticamente del padre. Ya no necesitas redeclararlos todos.

### lang/en/theme_mytheme.php

Cadenas mínimas requeridas:

```php
<?php
$string['pluginname'] = 'My Theme';
$string['choosereadme'] = 'My Theme es un theme hijo de Boost personalizado.';
$string['configtitle'] = 'My Theme';
```

## Herencia de themes

`$THEME->parents = ['boost']` activa la herencia en cinco niveles:

1. **SCSS**: Se inyecta antes y después del SCSS del padre mediante callbacks
2. **Templates Mustache**: Se busca primero en el hijo, luego en la cadena de padres
3. **Layouts PHP**: Heredan automáticamente del padre (5.1+)
4. **Iconos**: Se sobreescriben colocándolos en `pix_core/` y `pix_plugins/`
5. **Renderers**: `theme_overridden_renderer_factory` busca clases en el theme primero

El SCSS **no** se hereda automáticamente — se debe importar explícitamente el SCSS del padre via `theme_boost_get_main_scss_content($theme)` en la función `get_main_scss_content`.

## Scaffolding

Al generar un theme nuevo, crear como mínimo estos archivos:

1. `version.php` — con `$plugin->component = 'theme_<nombre>'`
2. `config.php` — con herencia de Boost y callbacks SCSS
3. `lib.php` — con las tres funciones del pipeline SCSS
4. `lang/en/theme_<nombre>.php` — con las tres cadenas mínimas
5. `scss/pre.scss` — archivo vacío o con imports personalizados
6. `scss/post.scss` — archivo vacío o con reglas personalizadas
