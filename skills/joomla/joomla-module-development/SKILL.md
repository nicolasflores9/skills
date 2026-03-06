---
name: joomla-module-development
description: Aprende a crear módulos personalizados en Joomla 5 y 6 con arquitectura moderna, PSR-4, inyección de dependencias y manifest.xml. Incluye ejemplos completos Hello World y acceso a base de datos. Triggers: módulo joomla, crear módulo, module joomla, mod_custom, ModuleDispatcherFactory, HelperFactory, tmpl joomla, manifest.xml
---

# Desarrollo de Módulos Personalizados en Joomla 5/6

## 1. Conceptos Fundamentales

Los módulos en Joomla 5/6 siguen una arquitectura moderna basada en:
- **PSR-4**: Autoloading automático de clases
- **Inyección de Dependencias (DI)**: Gestión centralizada de servicios
- **Namespaces**: Organización clara del código
- **XML Configuration**: Parámetros y configuración declarativa

Un módulo es una extensión que se renderiza en posiciones específicas del sitio (sidebar, header, etc.). Diferente de componentes (secciones principales) y plugins (hooks del sistema).

## 2. Estructura de Archivos Moderna

```
mod_ejemplo/
├── manifest.xml                    (configuración e instalación)
├── mod_ejemplo.php                 (punto de entrada)
├── language/
│   └── en-GB/
│       ├── mod_ejemplo.ini
│       └── mod_ejemplo.sys.ini
├── src/
│   ├── Dispatcher/Dispatcher.php   (manejo del renderizado)
│   └── Helper/ExampleHelper.php    (lógica de negocio)
├── services/
│   └── provider.php                (registro de servicios DI)
└── tmpl/
    ├── default.php                 (template HTML)
    └── default.xml                 (definición de layout)
```

**Convenciones de nombres**:
- Módulos: `mod_[nombre]` (ej: `mod_latest_articles`)
- Clases: `PascalCase` (ej: `ExampleHelper`, `Dispatcher`)
- Métodos: `camelCase` (ej: `getItems()`, `renderContent()`)
- Namespace raíz: `Joomla\Module\[ModuleName]`

## 3. Manifest.xml - Configuración Principal

```xml
<?xml version="1.0" encoding="UTF-8"?>
<extension type="module" client="site" method="upgrade">
    <name>MOD_EJEMPLO</name>
    <author>Tu Nombre</author>
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
                    default="Mi Módulo" />
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

**Tipos de campos disponibles**: text, integer, textarea, list, category, article, user, menu, modulelayout, sql, radio, checkbox, email, url, password, hidden

## 4. Inyección de Dependencias - services/provider.php

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

## 5. Dispatcher - Control del Renderizado

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

## 6. Helper - Lógica de Negocio

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

## 7. Templates - Renderizado HTML

### tmpl/default.php

```php
<?php
defined('_JEXEC') or die;
use Joomla\CMS\HTML\HTMLHelper;
?>
<div class="mod-ejemplo">
    <h3><?php echo $displayData['params']->get('title', 'Ejemplo'); ?></h3>
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
        <p>No hay elementos</p>
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

## 8. Archivo Principal - mod_ejemplo.php

```php
<?php
defined('_JEXEC') or die;

use Joomla\CMS\Helper\ModuleHelper;

$layout = $params->get('layout', 'default');
$path = ModuleHelper::getLayoutPath('mod_ejemplo', $layout);
require $path;
```

## 9. Archivos de Idioma

### language/en-GB/mod_ejemplo.ini

```ini
MOD_EJEMPLO="Ejemplo Module"
MOD_EJEMPLO_DESC="Módulo de ejemplo con características modernas"
MOD_EJEMPLO_TITLE="Título del módulo"
MOD_EJEMPLO_TITLE_DESC="Mostrado en la parte superior"
MOD_EJEMPLO_COUNT="Cantidad de elementos"
MOD_EJEMPLO_COUNT_DESC="Número de artículos a mostrar"
MOD_EJEMPLO_CAT="Categoría"
MOD_EJEMPLO_CAT_DESC="Selecciona la categoría a mostrar"
MOD_EJEMPLO_LAYOUT_DEFAULT="Layout por defecto"
```

### language/en-GB/mod_ejemplo.sys.ini

```ini
MOD_EJEMPLO="Ejemplo Module"
MOD_EJEMPLO_DESC="Módulo de ejemplo con características modernas"
```

## 10. Ejemplo Completo: Hello World

**Estructura mínima** para un módulo funcional:

1. Crear carpeta `mod_hello_world`
2. Crear `manifest.xml` (ver sección 3)
3. Crear `mod_hello_world.php` (ver sección 8)
4. Crear `tmpl/default.php`:

```php
<?php defined('_JEXEC') or die; ?>
<div class="hello-world">
    <h3><?php echo $displayData['params']->get('title', 'Hello'); ?></h3>
    <p>Hello, World!</p>
</div>
```

5. Crear `tmpl/default.xml` (ver sección 7)
6. Crear `language/en-GB/mod_hello_world.ini` y `.sys.ini`
7. Empaquetar en ZIP: `mod_hello_world.zip`
8. Instalar desde Panel Admin → Instalar Extensiones

## 11. Diferencias Joomla 4 → 5 → 6

| Feature | J4 | J5 | J6 |
|---------|----|----|-----|
| PSR-4 | Sí | Sí | Sí |
| DI Container | Nuevo | Mejorado | Mejorado+ |
| src/ directory | Opcional | Estándar | Estándar |
| services/provider | Opcional | Estándar | Estándar |
| namespace en XML | Nuevo | Obligatorio | Obligatorio |

Joomla 5 y 6 comparten arquitectura; cambios son principalmente optimizaciones internas.

## 12. Checklist de Instalación

- ✓ Folder structure PSR-4 completa
- ✓ manifest.xml con namespace valido
- ✓ Dispatcher extiende AbstractModuleDispatcher
- ✓ services/provider.php registra servicios
- ✓ Templates con defined('_JEXEC')
- ✓ Archivos .ini en language/
- ✓ Parámetros en manifest.xml
- ✓ Punto de entrada mod_[nombre].php

## 13. Mejores Prácticas

1. **Seguridad**: Escapar siempre en templates con `HTMLHelper::_()` y `htmlspecialchars()`
2. **Validación**: Validar parámetros en Helper antes de usar
3. **Performance**: Usar caching (field "cache" en manifest)
4. **Testabilidad**: Inyectar todas las dependencias
5. **Documentación**: Incluir README.md explicando instalación y uso

Ver archivo references/cheat-sheet.md para comandos rápidos.

---

**Recursos**:
- https://manual.joomla.org/docs/building-extensions/modules/
- https://docs.joomla.org/Module_development_tutorial_(4.x)
- https://github.com/joomla/joomla-cms (ejemplos en core)
