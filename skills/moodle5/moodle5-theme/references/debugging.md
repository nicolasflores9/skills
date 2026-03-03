# Depuración y caché

## Tabla de contenidos
1. [Sistema de caché](#caché)
2. [Theme Designer Mode](#designer-mode)
3. [Comandos CLI](#cli)
4. [Configuración de desarrollo](#config-desarrollo)
5. [Depuración de SCSS](#debug-scss)
6. [Depuración de templates](#debug-templates)
7. [Monitorización de rendimiento](#rendimiento)
8. [Errores frecuentes y soluciones](#errores)

## Caché

| Tipo | Ubicación | Impacto |
|---|---|---|
| CSS/SCSS compilado | `<moodledata>/localcache/theme/<themerev>/<theme>/css/` | Cambios SCSS invisibles sin purga |
| Templates Mustache | `<moodledata>/localcache/mustache/<revision>/<theme>/` | Overrides no se aplican |
| Cadenas de idioma | MUC (Moodle Universal Cache) | Nuevas cadenas no aparecen |
| JavaScript | `theme/javascript.php` minificado | Cambios JS no reflejados |
| Imágenes/Iconos | Caché navegador + `theme/image.php` | Iconos nuevos no visibles |

La invalidación funciona con números de revisión en las URLs de assets. `theme_reset_all_caches()` incrementa `themerev`, forzando al navegador a descargar archivos nuevos.

## Designer Mode

Previene cacheado de CSS, templates e imágenes. Recompila SCSS en cada carga de página:

```php
// En config.php de Moodle — NUNCA en producción
$CFG->themedesignermode = true;
```

El binario `sassc` reduce el tiempo de compilación >50%:
```bash
# Instalar en Debian/Ubuntu
apt install sassc
# Configurar en: Administración → Experimental → Path to SassC
```

## CLI

```bash
# Purgar todo
php admin/cli/purge_caches.php

# Solo caché de themes
php admin/cli/purge_caches.php --theme

# Solo JavaScript
php admin/cli/purge_caches.php --js

# Solo cadenas de idioma
php admin/cli/purge_caches.php --lang

# Combinar
php admin/cli/purge_caches.php --theme --js --lang

# Compilar CSS de un theme específico (sin purgar todo)
php admin/cli/build_theme_css.php --themes=mytheme --verbose
```

`build_theme_css.php` es especialmente útil: compila CSS e incrementa solo el sub-revision del theme específico.

## Config desarrollo

```php
// config.php de Moodle — SOLO para desarrollo
@error_reporting(E_ALL | E_STRICT);
@ini_set('display_errors', '1');
$CFG->debug = (E_ALL | E_STRICT);          // DEBUG_DEVELOPER
$CFG->debugdisplay = 1;
$CFG->themedesignermode = true;             // Recompila SCSS en cada carga
$CFG->cachejs = false;                      // No cachear JavaScript
$CFG->cachetemplates = false;               // No cachear templates Mustache
$CFG->langstringcache = false;              // No cachear cadenas de idioma
$CFG->debugtemplateinfo = true;             // Nombres de templates en comentarios HTML
$CFG->debugstringids = 1;                   // Mostrar identificadores de cadenas
$CFG->noemailever = true;                   // No enviar emails reales
```

## Debug SCSS

Los errores de compilación SCSS no se muestran en el navegador por defecto (MDL-62542). Para diagnosticar:

1. Activar debug DEVELOPER en `config.php`
2. Purgar cachés (el error aparece como output PHP)
3. Revisar logs: `tail -f /var/log/apache2/error.log`
4. Usar CLI: `php admin/cli/build_theme_css.php --themes=mytheme --verbose`

Directivas de debug en SCSS:
```scss
$primary: #0073aa;
@debug "Valor actual de primary: #{$primary}";  // Escribe en log del servidor
@warn "Variable podría cambiar";                 // Warning en log
@error "Valor inválido para primary";            // Detiene compilación
```

## Debug templates

`$CFG->debugtemplateinfo = true` inyecta comentarios HTML:
```html
<!-- template(PHP): core/pix_icon_fontawesome -->
<i class="icon fa fa-window-close fa-fw" aria-hidden="true"></i>
<!-- /template(PHP): core/pix_icon_fontawesome -->
```

El Template Library (Administración → Desarrollo → Template Library) previsualiza templates con anotaciones `@template` y contextos JSON de ejemplo.

## Rendimiento

```php
define('MDL_PERF', true);
define('MDL_PERFDB', true);
define('MDL_PERFTOFOOT', true);
```

Muestra en el pie: tiempo de carga, uso de memoria, queries a BD, tiempos de compilación SCSS.

## Errores

| Problema | Causa | Solución |
|---|---|---|
| Cambios no aparecen | Caché no purgada | `php admin/cli/purge_caches.php --theme` |
| CSS funciona en designer mode pero no sin él | SCSS inválido que pasa compilador pero rompe minificador | Verificar SCSS con CLI verbose |
| Override de template sin efecto | Ruta incorrecta o caché | Verificar ruta: `theme/<nombre>/templates/<componente>/<template>.mustache`, usar `$CFG->cachetemplates = false` |
| Cadenas de idioma no se actualizan | Caché de idioma | `$CFG->langstringcache = false` |
| Theme hijo no hereda estilos | SCSS no se hereda automáticamente | Importar explícitamente: `theme_boost_get_main_scss_content($theme)` en `get_main_scss_content` |
| Página sin estilos tras editar SCSS | Error de compilación silencioso | Usar `build_theme_css.php --verbose` |
