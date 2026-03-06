---
name: sppagebuilder-custom-addon
description: Crea addons personalizados para SP Page Builder. Triggers - addon sp page builder, addon sppb, SP Page Builder custom, addon personalizado page builder, plg_sppagebuilder, admin.php site.php addon
---

# SP Page Builder - Custom Addon Development

Domina la creación de addons personalizados para SP Page Builder (v5/6). Aprenderás arquitectura, estructura de archivos, tipos de campos, renderizado dinámico y acceso a base de datos.

## Tabla de Contenidos

1. Arquitectura Fundamental
2. Estructura de Plugin
3. Archivos Principales
4. Tipos de Campos
5. Implementación Frontend
6. Acceso a Base de Datos
7. Ejemplo Completo
8. Instalación y Testing

---

## 1. ARQUITECTURA FUNDAMENTAL

Los addons se instalan como **plugins Joomla** con convención:

```
plg_sppagebuilder_{nombre}
```

Ejemplos válidos: `plg_sppagebuilder_demo`, `plg_sppagebuilder_testimonios`, `plg_sppagebuilder_gallery`

Estructura de directorios:
```
plg_sppagebuilder_demo/
├── demo.php                    (Archivo principal plugin)
├── demo.xml                    (Manifest XML)
├── language/
│   └── en-GB/
│       └── en-GB.plg_sppagebuilder_demo.ini
└── addons/
    └── demo/
        ├── admin.php          (Configuración campos)
        ├── site.php           (Renderizado frontend)
        └── assets/
            └── images/icon.png (76x76 px)
```

Ciclo de carga:
1. Plugin principal (demo.php) registra addon
2. Manifest (demo.xml) define metadatos
3. admin.php define campos editables
4. site.php renderiza HTML en frontend

---

## 2. ESTRUCTURA DE PLUGIN

**demo.php** - Punto de entrada del plugin:

```php
<?php
defined('_JEXEC') or die('restricted access');

class PlgSppagebuilderDemo extends CMSPlugin {
    protected $autoloadLanguage = true;

    public function onSppagebuilderGetAddons() {
        $addon_path = dirname(__FILE__) . '/addons/demo';
        if (file_exists($addon_path . '/admin.php')) {
            require $addon_path . '/admin.php';
        }
    }
}
```

**demo.xml** - Manifest del plugin:

```xml
<?xml version="1.0" encoding="utf-8"?>
<extension type="plugin" group="sppagebuilder" method="upgrade">
    <name>plg_sppagebuilder_demo</name>
    <author>Tu Nombre</author>
    <version>1.0.0</version>
    <description>Custom addon demo para SP Page Builder</description>
    <license>GNU/GPLv2 or later</license>

    <files>
        <filename plugin="demo">demo.php</filename>
        <folder>language</folder>
        <folder>addons</folder>
    </files>

    <languages>
        <language tag="es-ES">language/es-ES/es-ES.plg_sppagebuilder_demo.ini</language>
    </languages>

    <config>
        <fields name="params">
            <fieldset name="basic">
                <field name="enabled" type="radio" label="Habilitado" default="1">
                    <option value="0">No</option>
                    <option value="1">Sí</option>
                </field>
            </fieldset>
        </fields>
    </config>
</extension>
```

---

## 3. ARCHIVOS PRINCIPALES

**admin.php** - Define campos de configuración:

```php
<?php
defined('_JEXEC') or die('restricted access');

SpAddonsConfig::addonConfig(array(
    'type'       => 'content',
    'addon_name' => 'sp_demo',
    'title'      => 'Demo Card',
    'desc'       => 'Addon card personalizado',
    'category'   => 'Custom',
    'icon'       => '',
    'attr'       => array(
        'general' => array(
            'title' => array(
                'type'  => 'text',
                'title' => 'Título',
                'std'   => 'Card Title'
            ),
            'description' => array(
                'type'  => 'textarea',
                'title' => 'Descripción',
                'std'   => ''
            ),
            'button_text' => array(
                'type'  => 'text',
                'title' => 'Texto Botón',
                'std'   => 'Click Me'
            ),
            'button_url' => array(
                'type'  => 'link',
                'title' => 'URL Botón',
                'std'   => ''
            ),
            'addon_color' => array(
                'type'  => 'color',
                'title' => 'Color',
                'std'   => '#333333'
            )
        ),
        'styling' => array(
            'alignment' => array(
                'type'  => 'alignment',
                'title' => 'Alineación',
                'std'   => 'left'
            )
        )
    )
));
```

---

## 4. TIPOS DE CAMPOS DISPONIBLES

| Tipo | Uso | Ejemplo |
|------|-----|---------|
| `text` | Texto simple | Títulos, nombres |
| `textarea` | Texto multilínea | Descripciones |
| `number` | Números | Tamaños, límites |
| `color` | Selector color | Paletas cromáticas |
| `media` | Upload imagen | Multimedia |
| `link` | URL con opciones | Enlaces |
| `select` | Dropdown | Selecciones |
| `checkbox`/`radio` | Booleano/opción | Switches |
| `repeatable` | Colecciones | Listas dinámicas |
| `typography` | Estilos texto | Fuentes |
| `padding`/`margin` | Espaciado | Márgenes |
| `icon` | Font Awesome | Iconografía |
| `slider` | Control deslizante | Rangos |
| `animation` | Efectos CSS | Animaciones |

Campos repetibles para colecciones:

```php
'items' => array(
    'type'   => 'repeatable',
    'title'  => 'Items',
    'fields' => array(
        'item_title' => array(
            'type'  => 'text',
            'title' => 'Título Item'
        ),
        'item_image' => array(
            'type'  => 'media',
            'title' => 'Imagen Item'
        ),
        'item_url' => array(
            'type'  => 'link',
            'title' => 'URL Item'
        )
    )
)
```

---

## 5. IMPLEMENTACIÓN FRONTEND

**site.php** - Renderiza el HTML:

```php
<?php
defined('_JEXEC') or die('restricted access');

class SppagebuilderAddonSp_demo extends SppagebuilderAddons {

    public function render() {
        $settings = $this->addon->settings;
        $title = isset($settings->title) ? $settings->title : '';
        $description = isset($settings->description) ? $settings->description : '';
        $btn_text = isset($settings->button_text) ? $settings->button_text : '';
        $btn_url = isset($settings->button_url) ? $settings->button_url : '';
        $color = isset($settings->addon_color) ? $settings->addon_color : '#333';

        $output = '<div class="sp-addon-demo" style="color: ' . esc_attr($color) . ';">';
        $output .= '<h3>' . esc_html($title) . '</h3>';
        $output .= '<p>' . nl2br(esc_html($description)) . '</p>';

        if ($btn_url && $btn_text) {
            $output .= '<a href="' . esc_attr($btn_url) . '" class="sp-demo-btn">';
            $output .= esc_html($btn_text) . '</a>';
        }

        $output .= '</div>';
        return $output;
    }

    public function css() {
        $addon_id = '#sppb-addon-' . $this->addon->id;
        $css = '';
        $settings = $this->addon->settings;

        if (isset($settings->alignment)) {
            $css .= $addon_id . ' { text-align: ' . $settings->alignment . '; }';
        }

        return $css;
    }

    public function getTemplate() {
        return '<div class="sp-addon-demo">
            <h3><%= title %></h3>
            <p><%= description %></p>
            <% if (button_url && button_text) { %>
                <a href="<%= button_url %>" class="sp-demo-btn"><%= button_text %></a>
            <% } %>
        </div>';
    }

    public function stylesheets() {
        return array(
            JUri::base(true) . '/plugins/sppagebuilder/demo/addons/demo/assets/css/demo.css'
        );
    }

    public function scripts() {
        return array(
            JUri::base(true) . '/plugins/sppagebuilder/demo/addons/demo/assets/js/demo.js'
        );
    }
}
```

Métodos principales:
- `render()` - Genera HTML para frontend
- `css()` - Genera CSS dinámico según settings
- `getTemplate()` - Template lodash para edición en tiempo real
- `stylesheets()` - Registra archivos CSS externos
- `scripts()` - Registra archivos JS externos

Siempre escapar output:
- `esc_html()` - Para texto
- `esc_attr()` - Para atributos HTML
- `esc_url()` - Para URLs

---

## 6. ACCESO A BASE DE DATOS

Obtener artículos dinámicamente:

```php
public function render() {
    $settings = $this->addon->settings;
    $output = '';

    $db = JFactory::getDbo();
    $query = $db->getQuery(true);

    $query->select('a.id, a.title, a.introtext')
        ->from($db->quoteName('#__content', 'a'))
        ->where('a.state = 1')
        ->order('a.created DESC')
        ->setLimit(isset($settings->limit) ? (int)$settings->limit : 5);

    $db->setQuery($query);
    $articles = $db->loadObjectList();

    if (count($articles)) {
        $output .= '<ul class="sp-articles">';
        foreach ($articles as $article) {
            $output .= '<li>';
            $output .= '<h4>' . esc_html($article->title) . '</h4>';
            $output .= '<p>' . substr(strip_tags($article->introtext), 0, 100) . '...</p>';
            $output .= '</li>';
        }
        $output .= '</ul>';
    }

    return $output;
}
```

Mejores prácticas DB:
- Usar `JFactory::getDbo()` para instancia
- Implementar control de acceso
- Usar límites en queries
- Escapar output siempre
- Cachear resultados si es posible

---

## 7. EJEMPLO COMPLETO - ADDON TESTIMONIOS

**admin.php:**

```php
<?php
SpAddonsConfig::addonConfig(array(
    'addon_name' => 'sp_testimonios',
    'title'      => 'Testimonios',
    'category'   => 'Custom',
    'attr'       => array(
        'general' => array(
            'testimonios' => array(
                'type'   => 'repeatable',
                'fields' => array(
                    'nombre' => array('type' => 'text', 'title' => 'Nombre'),
                    'cargo' => array('type' => 'text', 'title' => 'Cargo'),
                    'texto' => array('type' => 'textarea', 'title' => 'Testimonio'),
                    'foto' => array('type' => 'media', 'title' => 'Foto')
                )
            )
        )
    )
));
```

**site.php:**

```php
<?php
class SppagebuilderAddonSp_testimonios extends SppagebuilderAddons {
    public function render() {
        $settings = $this->addon->settings;
        $items = isset($settings->testimonios) ? $settings->testimonios : array();
        $output = '<div class="sp-testimonios">';

        foreach ($items as $item) {
            $output .= '<div class="testimonio">';
            if (isset($item->foto)) {
                $output .= '<img src="' . esc_attr($item->foto) . '" alt="">';
            }
            $output .= '<p>' . esc_html($item->texto) . '</p>';
            $output .= '<strong>' . esc_html($item->nombre) . '</strong>';
            $output .= '<small>' . esc_html($item->cargo) . '</small>';
            $output .= '</div>';
        }

        $output .= '</div>';
        return $output;
    }

    public function css() {
        return '#sppb-addon-' . $this->addon->id . ' { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; }';
    }
}
```

---

## 8. INSTALACIÓN Y TESTING

Crear ZIP con estructura correcta:

```
plg_sppagebuilder_demo.zip
└── plg_sppagebuilder_demo/
    ├── demo.php
    ├── demo.xml
    ├── language/
    │   └── es-ES/
    │       └── es-ES.plg_sppagebuilder_demo.ini
    └── addons/
        └── demo/
            ├── admin.php
            ├── site.php
            └── assets/
                └── images/icon.png
```

Instalar en Joomla:
1. Sistema → Instalar → Extensiones
2. Cargar archivo ZIP
3. Sistema → Administrar → Plugins
4. Habilitar plugin `plg_sppagebuilder_demo`
5. Editar página con SP Page Builder
6. Addon aparecerá en categoría "Custom"

Testing checklist:
- [ ] Plugin aparece en lista de plugins
- [ ] Addon visible en SP Page Builder
- [ ] Campos se cargan correctamente
- [ ] Renderizado en frontend es correcto
- [ ] No hay errores en error_log
- [ ] CSS/JS cargan apropiadamente
- [ ] Responsive en móvil
- [ ] Compatibilidad navegadores
- [ ] XSS protection (escapado)
- [ ] Acceso a usuarios sin permisos

---

## REFERENCIAS

Documentación oficial:
- [JoomShaper Custom Addon Creation](https://www.joomshaper.com/blog/how-to-create-custom-addon-in-sp-page-builder-4)
- [SP Page Builder Admin File](https://www.joomshaper.com/documentation/sp-page-builder/the-admin-php-file)
- [SP Page Builder Site File](https://www.joomshaper.com/documentation/sp-page-builder/the-site-php-file)
- [Joomla Plugin Development](https://docs.joomla.org/Plugins)

Ver `references/` para ejemplos completos, snippets y plantillas reutilizables.
