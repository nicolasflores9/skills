# Gestión de ajustes del theme (settings.php)

## Tabla de contenidos
1. [Estructura de settings.php](#estructura)
2. [Tipos de setting disponibles](#tipos)
3. [Ejemplo completo con pestañas](#ejemplo)
4. [Acceso a valores desde PHP y templates](#acceso)
5. [Servir archivos subidos (pluginfile)](#pluginfile)

## Estructura

Los ajustes se definen en `settings.php` y se almacenan en `mdl_config_plugins`. Boost proporciona `theme_boost_admin_settingspage_tabs` para organizarlos en pestañas.

Regla crítica: cada setting que afecte CSS/SCSS **debe** incluir:
```php
$setting->set_updatedcallback('theme_reset_all_caches');
```
Sin esto, los cambios de estilo no se reflejarán hasta la siguiente purga manual de caché.

## Tipos

| Tipo | Uso | Ejemplo |
|---|---|---|
| `admin_setting_configtext` | Texto libre corto | Pie de página, URL externa |
| `admin_setting_configtextarea` | Texto multilínea | Código HTML adicional |
| `admin_setting_confightmleditor` | Editor HTML | Contenido rich text |
| `admin_setting_configcolourpicker` | Selector de color | Color de marca |
| `admin_setting_configcheckbox` | Toggle on/off | Mostrar/ocultar sección |
| `admin_setting_configselect` | Dropdown | Selección de preset |
| `admin_setting_configstoredfile` | Subida de archivo | Logo, imagen de fondo |
| `admin_setting_scsscode` | Editor SCSS | CSS personalizado |
| `admin_setting_heading` | Encabezado visual | Separador de secciones |

## Ejemplo

```php
<?php
defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    $settings = new theme_boost_admin_settingspage_tabs(
        'themesettingmytheme',
        get_string('configtitle', 'theme_mytheme')
    );

    // === PESTAÑA: Ajustes generales ===
    $page = new admin_settingpage('theme_mytheme_general',
        get_string('generalsettings', 'theme_mytheme'));

    // Encabezado visual (sin valor almacenado)
    $page->add(new admin_setting_heading('theme_mytheme/brandingheading',
        get_string('branding', 'theme_mytheme'),
        get_string('branding_desc', 'theme_mytheme')));

    // Selector de color
    $setting = new admin_setting_configcolourpicker('theme_mytheme/brandcolor',
        get_string('brandcolor', 'theme_mytheme'),
        get_string('brandcolor_desc', 'theme_mytheme'), '#0f6cbf');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Subida de logo
    $setting = new admin_setting_configstoredfile('theme_mytheme/logo',
        get_string('logo', 'theme_mytheme'),
        get_string('logodesc', 'theme_mytheme'), 'logo', 0,
        ['accepted_types' => ['.png', '.jpg', '.svg', '.webp'], 'maxfiles' => 1]);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Checkbox toggle
    $setting = new admin_setting_configcheckbox('theme_mytheme/showfooter',
        get_string('showfooter', 'theme_mytheme'),
        get_string('showfooter_desc', 'theme_mytheme'), 1);
    $page->add($setting);

    // Selector dropdown
    $setting = new admin_setting_configselect('theme_mytheme/preset',
        get_string('preset', 'theme_mytheme'),
        get_string('preset_desc', 'theme_mytheme'), 'default.scss',
        ['default.scss' => 'Default', 'plain.scss' => 'Plain']);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Textarea para SCSS personalizado
    $setting = new admin_setting_scsscode('theme_mytheme/scss',
        get_string('rawscss', 'theme_mytheme'),
        get_string('rawscss_desc', 'theme_mytheme'), '', PARAM_RAW);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Texto libre
    $setting = new admin_setting_configtext('theme_mytheme/footnote',
        get_string('footnote', 'theme_mytheme'),
        get_string('footnotedesc', 'theme_mytheme'), '', PARAM_NOTAGS, 50);
    $page->add($setting);

    $settings->add($page);

    // === PESTAÑA: Imágenes ===
    $page = new admin_settingpage('theme_mytheme_images',
        get_string('imagessettings', 'theme_mytheme'));

    $setting = new admin_setting_configstoredfile('theme_mytheme/loginbackgroundimage',
        get_string('loginbackgroundimage', 'theme_mytheme'),
        get_string('loginbackgroundimage_desc', 'theme_mytheme'),
        'loginbackgroundimage', 0,
        ['accepted_types' => ['.png', '.jpg', '.webp']]);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $settings->add($page);
}
```

## Acceso

### Desde PHP (lib.php y layouts)

```php
// Vía get_config
$brandcolor = get_config('theme_mytheme', 'brandcolor');

// Vía el objeto $theme (dentro de callbacks de lib.php)
$value = $theme->settings->brandcolor;

// URL de archivos subidos
$logourl = $theme->setting_file_url('logo', 'logo');
```

### Desde templates Mustache

Los templates no tienen acceso directo a settings. Los valores deben pasarse como contexto desde el archivo de layout PHP:

```php
// En layout/drawers.php
$templatecontext = [
    'showfooter' => get_config('theme_mytheme', 'showfooter'),
    'footnote'   => format_text(get_config('theme_mytheme', 'footnote'), FORMAT_HTML),
    'logourl'    => $OUTPUT->get_logo_url(),
    'output'     => $OUTPUT,
    'bodyattributes' => $OUTPUT->body_attributes([]),
];
echo $OUTPUT->render_from_template('theme_mytheme/drawers', $templatecontext);
```

## Pluginfile

Para que los archivos subidos con `configstoredfile` funcionen, `lib.php` debe implementar:

```php
function theme_mytheme_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = []) {
    if ($context->contextlevel == CONTEXT_SYSTEM) {
        $theme = theme_config::load('mytheme');
        if (!array_key_exists($filearea, [
            'logo' => 1,
            'loginbackgroundimage' => 1,
            'headerbackgroundimage' => 1,
        ])) {
            send_file_not_found();
        }
        return $theme->setting_file_serve($filearea, $args, $forcedownload, $options);
    }
    send_file_not_found();
}
```
