---
name: sppagebuilder-custom-addon
description: Create custom addons for SP Page Builder. Triggers - addon sp page builder, addon sppb, SP Page Builder custom, custom addon page builder, plg_sppagebuilder, admin.php site.php addon
---

# SP Page Builder - Custom Addon Development

Master the creation of custom addons for SP Page Builder (v5/6). Learn architecture, file structure, field types, dynamic rendering, and database access.

## Table of Contents

1. Fundamental Architecture
2. Plugin Structure
3. Main Files
4. Field Types
5. Frontend Implementation
6. Database Access
7. Complete Example
8. Installation and Testing

---

## 1. FUNDAMENTAL ARCHITECTURE

Addons are installed as **Joomla plugins** following the convention:

```
plg_sppagebuilder_{name}
```

Valid examples: `plg_sppagebuilder_demo`, `plg_sppagebuilder_testimonials`, `plg_sppagebuilder_gallery`

Directory structure:
```
plg_sppagebuilder_demo/
├── demo.php                    (Main plugin file)
├── demo.xml                    (XML Manifest)
├── language/
│   └── en-GB/
│       └── en-GB.plg_sppagebuilder_demo.ini
└── addons/
    └── demo/
        ├── admin.php          (Field configuration)
        ├── site.php           (Frontend rendering)
        └── assets/
            └── images/icon.png (76x76 px)
```

Loading cycle:
1. Main plugin (demo.php) registers addon
2. Manifest (demo.xml) defines metadata
3. admin.php defines editable fields
4. site.php renders HTML on the frontend

---

## 2. PLUGIN STRUCTURE

**demo.php** - Plugin entry point:

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

**demo.xml** - Plugin manifest:

```xml
<?xml version="1.0" encoding="utf-8"?>
<extension type="plugin" group="sppagebuilder" method="upgrade">
    <name>plg_sppagebuilder_demo</name>
    <author>Your Name</author>
    <version>1.0.0</version>
    <description>Custom addon demo for SP Page Builder</description>
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
                <field name="enabled" type="radio" label="Enabled" default="1">
                    <option value="0">No</option>
                    <option value="1">Yes</option>
                </field>
            </fieldset>
        </fields>
    </config>
</extension>
```

---

## 3. MAIN FILES

**admin.php** - Defines configuration fields:

```php
<?php
defined('_JEXEC') or die('restricted access');

SpAddonsConfig::addonConfig(array(
    'type'       => 'content',
    'addon_name' => 'sp_demo',
    'title'      => 'Demo Card',
    'desc'       => 'Custom card addon',
    'category'   => 'Custom',
    'icon'       => '',
    'attr'       => array(
        'general' => array(
            'title' => array(
                'type'  => 'text',
                'title' => 'Title',
                'std'   => 'Card Title'
            ),
            'description' => array(
                'type'  => 'textarea',
                'title' => 'Description',
                'std'   => ''
            ),
            'button_text' => array(
                'type'  => 'text',
                'title' => 'Button Text',
                'std'   => 'Click Me'
            ),
            'button_url' => array(
                'type'  => 'link',
                'title' => 'Button URL',
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
                'title' => 'Alignment',
                'std'   => 'left'
            )
        )
    )
));
```

---

## 4. AVAILABLE FIELD TYPES

| Type | Usage | Example |
|------|-------|---------|
| `text` | Simple text | Titles, names |
| `textarea` | Multiline text | Descriptions |
| `number` | Numbers | Sizes, limits |
| `color` | Color picker | Color palettes |
| `media` | Image upload | Multimedia |
| `link` | URL with options | Links |
| `select` | Dropdown | Selections |
| `checkbox`/`radio` | Boolean/option | Switches |
| `repeatable` | Collections | Dynamic lists |
| `typography` | Text styles | Fonts |
| `padding`/`margin` | Spacing | Margins |
| `icon` | Font Awesome | Iconography |
| `slider` | Slider control | Ranges |
| `animation` | CSS effects | Animations |

Repeatable fields for collections:

```php
'items' => array(
    'type'   => 'repeatable',
    'title'  => 'Items',
    'fields' => array(
        'item_title' => array(
            'type'  => 'text',
            'title' => 'Item Title'
        ),
        'item_image' => array(
            'type'  => 'media',
            'title' => 'Item Image'
        ),
        'item_url' => array(
            'type'  => 'link',
            'title' => 'Item URL'
        )
    )
)
```

---

## 5. FRONTEND IMPLEMENTATION

**site.php** - Renders the HTML:

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

Main methods:
- `render()` - Generates HTML for the frontend
- `css()` - Generates dynamic CSS based on settings
- `getTemplate()` - Lodash template for real-time editing
- `stylesheets()` - Registers external CSS files
- `scripts()` - Registers external JS files

Always escape output:
- `esc_html()` - For text
- `esc_attr()` - For HTML attributes
- `esc_url()` - For URLs

---

## 6. DATABASE ACCESS

Fetch articles dynamically:

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

Database best practices:
- Use `JFactory::getDbo()` to get the instance
- Implement access control
- Use limits in queries
- Always escape output
- Cache results when possible

---

## 7. COMPLETE EXAMPLE - TESTIMONIALS ADDON

**admin.php:**

```php
<?php
SpAddonsConfig::addonConfig(array(
    'addon_name' => 'sp_testimonios',
    'title'      => 'Testimonials',
    'category'   => 'Custom',
    'attr'       => array(
        'general' => array(
            'testimonios' => array(
                'type'   => 'repeatable',
                'fields' => array(
                    'nombre' => array('type' => 'text', 'title' => 'Name'),
                    'cargo' => array('type' => 'text', 'title' => 'Position'),
                    'texto' => array('type' => 'textarea', 'title' => 'Testimonial'),
                    'foto' => array('type' => 'media', 'title' => 'Photo')
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

## 8. INSTALLATION AND TESTING

Create a ZIP with the correct structure:

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

Install in Joomla:
1. System -> Install -> Extensions
2. Upload the ZIP file
3. System -> Manage -> Plugins
4. Enable the `plg_sppagebuilder_demo` plugin
5. Edit a page with SP Page Builder
6. The addon will appear in the "Custom" category

Testing checklist:
- [ ] Plugin appears in the plugins list
- [ ] Addon is visible in SP Page Builder
- [ ] Fields load correctly
- [ ] Frontend rendering is correct
- [ ] No errors in error_log
- [ ] CSS/JS load properly
- [ ] Responsive on mobile
- [ ] Browser compatibility
- [ ] XSS protection (escaping)
- [ ] Access for unauthorized users

---

## REFERENCES

Official documentation:
- [JoomShaper Custom Addon Creation](https://www.joomshaper.com/blog/how-to-create-custom-addon-in-sp-page-builder-4)
- [SP Page Builder Admin File](https://www.joomshaper.com/documentation/sp-page-builder/the-admin-php-file)
- [SP Page Builder Site File](https://www.joomshaper.com/documentation/sp-page-builder/the-site-php-file)
- [Joomla Plugin Development](https://docs.joomla.org/Plugins)

See `references/` for complete examples, snippets, and reusable templates.
