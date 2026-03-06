---
name: joomla-module-development
description: Build custom Joomla 5/6 modules with modern PSR-4 architecture, dependency injection, and manifest.xml. Includes Hello World and database access examples.
---

# Custom Module Development in Joomla 5/6

## 1. Fundamental Concepts

Modules in Joomla 5/6 follow a modern architecture based on:
- **PSR-4**: Automatic class autoloading
- **Dependency Injection (DI)**: Centralized service management
- **Namespaces**: Clear code organization
- **XML Configuration**: Declarative parameters and configuration

A module is an extension that renders in specific site positions (sidebar, header, etc.). Different from components (main sections) and plugins (system hooks).

## 2. Modern File Structure

```
mod_ejemplo/
├── manifest.xml                    (configuration and installation)
├── mod_ejemplo.php                 (entry point)
├── language/
│   └── en-GB/
│       ├── mod_ejemplo.ini
│       └── mod_ejemplo.sys.ini
├── src/
│   ├── Dispatcher/Dispatcher.php   (rendering handler)
│   └── Helper/ExampleHelper.php    (business logic)
├── services/
│   └── provider.php                (DI service registration)
└── tmpl/
    ├── default.php                 (HTML template)
    └── default.xml                 (layout definition)
```

**Naming conventions**:
- Modules: `mod_[name]` (e.g.: `mod_latest_articles`)
- Classes: `PascalCase` (e.g.: `ExampleHelper`, `Dispatcher`)
- Methods: `camelCase` (e.g.: `getItems()`, `renderContent()`)
- Root namespace: `Joomla\Module\[ModuleName]`

## 3. Manifest.xml - Main Configuration

```xml
<?xml version="1.0" encoding="UTF-8"?>
<extension type="module" client="site" method="upgrade">
    <name>MOD_EJEMPLO</name>
    <author>Your Name</author>
    <version>1.0.0</version>
    <description>MOD_EJEMPLO_DESC</description>
    <license>GNU General Public License v2.0</license>
    <namespace path="src">Joomla\Module\Ejemplo</namespace>

    <files>
        <filename module="mod_ejemplo">mod_ejemplo.php</filename>
        <folder>language</folder>
        <folder>src</folder>
        <folder>services</folder>
        <folder>tmpl</folder>
    </files>

    <languages>
        <language tag="en-GB">language/en-GB/mod_ejemplo.ini</language>
        <language tag="en-GB">language/en-GB/mod_ejemplo.sys.ini</language>
    </languages>

    <config>
        <fields name="params">
            <fieldset name="basic">
                <field name="title" type="text" label="MOD_EJEMPLO_TITLE"
                    default="My Module" />
                <field name="count" type="integer" label="MOD_EJEMPLO_COUNT"
                    default="5" min="1" max="100" />
                <field name="categoria" type="category" label="MOD_EJEMPLO_CAT"
                    extension="com_content" />
            </fieldset>
            <fieldset name="advanced">
                <field name="layout" type="modulelayout" label="JFIELD_ALT_LAYOUT_LABEL" />
                <field name="cache" type="list" label="JFIELD_CACHING_LABEL" default="1">
                    <option value="0">JNO</option>
                    <option value="1">JYES</option>
                </field>
            </fieldset>
        </fields>
    </config>
</extension>
```

**Available field types**: text, integer, textarea, list, category, article, user, menu, modulelayout, sql, radio, checkbox, email, url, password, hidden

## 4. Dependency Injection - services/provider.php

```php
<?php
namespace Joomla\Module\Ejemplo\Service;

use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Module\Ejemplo\Dispatcher\Dispatcher;
use Joomla\Module\Ejemplo\Helper\ExampleHelper;

class Provider implements ServiceProviderInterface
{
    public function register(Container $container): void
    {
        $container->set(
            Dispatcher::class,
            function (Container $c) {
                return new Dispatcher(
                    $c->get(ExampleHelper::class)
                );
            }
        );

        $container->set(
            ExampleHelper::class,
            function (Container $c) {
                return new ExampleHelper(
                    $c->get('db'),
                    $c->get('app')
                );
            }
        );
    }
}
```

## 5. Dispatcher - Rendering Control

### src/Dispatcher/Dispatcher.php

```php
<?php
namespace Joomla\Module\Ejemplo\Dispatcher;

use Joomla\CMS\Dispatcher\AbstractModuleDispatcher;
use Joomla\Module\Ejemplo\Helper\ExampleHelper;

class Dispatcher extends AbstractModuleDispatcher
{
    private $helper;

    public function __construct(ExampleHelper $helper)
    {
        $this->helper = $helper;
    }

    protected function getLayoutData(): array
    {
        $data = parent::getLayoutData();
        $count = $this->module->params->get('count', 5);
        $data['items'] = $this->helper->getItems($count);
        return $data;
    }
}
```

## 6. Helper - Business Logic

### src/Helper/ExampleHelper.php

```php
<?php
namespace Joomla\Module\Ejemplo\Helper;

use Joomla\Database\DatabaseInterface;
use Joomla\CMS\Application\CMSApplicationInterface;

class ExampleHelper
{
    private $db;
    private $app;

    public function __construct(
        DatabaseInterface $db,
        CMSApplicationInterface $app
    ) {
        $this->db = $db;
        $this->app = $app;
    }

    public function getItems($count = 5)
    {
        $query = $this->db->getQuery(true);
        $query->select('*')
            ->from($this->db->quoteName('#__articles'))
            ->where($this->db->quoteName('state') . ' = 1')
            ->order($this->db->quoteName('publish_up') . ' DESC')
            ->setLimit($count);

        $this->db->setQuery($query);
        return $this->db->loadObjectList();
    }
}
```

## 7. Templates - HTML Rendering

### tmpl/default.php

```php
<?php
defined('_JEXEC') or die;
use Joomla\CMS\HTML\HTMLHelper;
?>
<div class="mod-ejemplo">
    <h3><?php echo $displayData['params']->get('title', 'Example'); ?></h3>
    <?php if (!empty($displayData['items'])): ?>
        <ul>
            <?php foreach ($displayData['items'] as $item): ?>
                <li>
                    <a href="<?php echo $item->catslug ? '/blog/' . $item->catslug . '/' . $item->id : '#'; ?>">
                        <?php echo HTMLHelper::_('string.truncate', $item->title, 50); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No items available</p>
    <?php endif; ?>
</div>
```

### tmpl/default.xml

```xml
<?xml version="1.0" encoding="UTF-8"?>
<layout title="MOD_EJEMPLO_LAYOUT_DEFAULT">
    <state>
        <name>MOD_EJEMPLO_LAYOUT_DEFAULT</name>
        <description>MOD_EJEMPLO_LAYOUT_DEFAULT_DESC</description>
    </state>
</layout>
```

## 8. Main File - mod_ejemplo.php

```php
<?php
defined('_JEXEC') or die;

use Joomla\CMS\Helper\ModuleHelper;

$layout = $params->get('layout', 'default');
$path = ModuleHelper::getLayoutPath('mod_ejemplo', $layout);
require $path;
```

## 9. Language Files

### language/en-GB/mod_ejemplo.ini

```ini
MOD_EJEMPLO="Example Module"
MOD_EJEMPLO_DESC="Example module with modern features"
MOD_EJEMPLO_TITLE="Module title"
MOD_EJEMPLO_TITLE_DESC="Displayed at the top"
MOD_EJEMPLO_COUNT="Number of items"
MOD_EJEMPLO_COUNT_DESC="Number of articles to display"
MOD_EJEMPLO_CAT="Category"
MOD_EJEMPLO_CAT_DESC="Select the category to display"
MOD_EJEMPLO_LAYOUT_DEFAULT="Default layout"
```

### language/en-GB/mod_ejemplo.sys.ini

```ini
MOD_EJEMPLO="Example Module"
MOD_EJEMPLO_DESC="Example module with modern features"
```

## 10. Complete Example: Hello World

**Minimal structure** for a functional module:

1. Create folder `mod_hello_world`
2. Create `manifest.xml` (see section 3)
3. Create `mod_hello_world.php` (see section 8)
4. Create `tmpl/default.php`:

```php
<?php defined('_JEXEC') or die; ?>
<div class="hello-world">
    <h3><?php echo $displayData['params']->get('title', 'Hello'); ?></h3>
    <p>Hello, World!</p>
</div>
```

5. Create `tmpl/default.xml` (see section 7)
6. Create `language/en-GB/mod_hello_world.ini` and `.sys.ini`
7. Package as ZIP: `mod_hello_world.zip`
8. Install from Admin Panel → Install Extensions

## 11. Differences Joomla 4 → 5 → 6

| Feature | J4 | J5 | J6 |
|---------|----|----|-----|
| PSR-4 | Yes | Yes | Yes |
| DI Container | New | Improved | Improved+ |
| src/ directory | Optional | Standard | Standard |
| services/provider | Optional | Standard | Standard |
| namespace in XML | New | Required | Required |

Joomla 5 and 6 share architecture; changes are primarily internal optimizations.

## 12. Installation Checklist

- ✓ Complete PSR-4 folder structure
- ✓ manifest.xml with valid namespace
- ✓ Dispatcher extends AbstractModuleDispatcher
- ✓ services/provider.php registers services
- ✓ Templates with defined('_JEXEC')
- ✓ .ini files in language/
- ✓ Parameters in manifest.xml
- ✓ Entry point mod_[name].php

## 13. Best Practices

1. **Security**: Always escape in templates with `HTMLHelper::_()` and `htmlspecialchars()`
2. **Validation**: Validate parameters in Helper before use
3. **Performance**: Use caching (cache field in manifest)
4. **Testability**: Inject all dependencies
5. **Documentation**: Include README.md explaining installation and usage

See references/cheat-sheet.md for quick commands.

---

**Resources**:
- https://manual.joomla.org/docs/building-extensions/modules/
- https://docs.joomla.org/Module_development_tutorial_(4.x)
- https://github.com/joomla/joomla-cms (core examples)
