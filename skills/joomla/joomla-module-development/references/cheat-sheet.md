# Cheat Sheet: Joomla 5/6 Module Development

## Quick Structure

```bash
# Create basic structure
mkdir -p mod_mimodulo/{src/{Dispatcher,Helper},services,tmpl,language/en-GB}

# Create base files
touch mod_mimodulo/{manifest.xml,mod_mimodulo.php}
touch mod_mimodulo/src/Dispatcher/Dispatcher.php
touch mod_mimodulo/src/Helper/MiHelper.php
touch mod_mimodulo/services/provider.php
touch mod_mimodulo/tmpl/{default.php,default.xml}
touch mod_mimodulo/language/en-GB/{mod_mimodulo.ini,mod_mimodulo.sys.ini}
```

## Quick PHP Templates

### Minimal manifest.xml
```xml
<?xml version="1.0" encoding="UTF-8"?>
<extension type="module" client="site" method="upgrade">
    <name>MOD_MIMODULO</name>
    <version>1.0.0</version>
    <description>MOD_MIMODULO_DESC</description>
    <namespace path="src">Joomla\Module\Mimodulo</namespace>
    <files>
        <filename module="mod_mimodulo">mod_mimodulo.php</filename>
        <folder>src</folder>
        <folder>tmpl</folder>
        <folder>language</folder>
        <folder>services</folder>
    </files>
    <languages>
        <language tag="en-GB">language/en-GB/mod_mimodulo.ini</language>
    </languages>
</extension>
```

### Basic Dispatcher
```php
<?php
namespace Joomla\Module\Mimodulo\Dispatcher;

use Joomla\CMS\Dispatcher\AbstractModuleDispatcher;

class Dispatcher extends AbstractModuleDispatcher
{
    protected function getLayoutData(): array
    {
        $data = parent::getLayoutData();
        // Add data here
        return $data;
    }
}
```

### Helper with DB
```php
<?php
namespace Joomla\Module\Mimodulo\Helper;

use Joomla\Database\DatabaseInterface;

class MiHelper
{
    private $db;

    public function __construct(DatabaseInterface $db)
    {
        $this->db = $db;
    }

    public function getItems($limit = 10)
    {
        $query = $this->db->getQuery(true)
            ->select('*')
            ->from($this->db->quoteName('#__articles'))
            ->setLimit($limit);

        return $this->db->setQuery($query)->loadObjectList();
    }
}
```

### Provider.php
```php
<?php
namespace Joomla\Module\Mimodulo\Service;

use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Module\Mimodulo\Dispatcher\Dispatcher;
use Joomla\Module\Mimodulo\Helper\MiHelper;

class Provider implements ServiceProviderInterface
{
    public function register(Container $container): void
    {
        $container->set(Dispatcher::class, function (Container $c) {
            return new Dispatcher($c->get(MiHelper::class));
        });

        $container->set(MiHelper::class, function (Container $c) {
            return new MiHelper($c->get('db'));
        });
    }
}
```

## Common Fields in manifest.xml

```xml
<!-- Text -->
<field name="texto" type="text" label="Label" default="value" />

<!-- Integer -->
<field name="numero" type="integer" label="Number" min="1" max="100" />

<!-- Textarea -->
<field name="descripcion" type="textarea" label="Description" rows="5" />

<!-- List/Dropdown -->
<field name="opcion" type="list" label="Option">
    <option value="1">Option 1</option>
    <option value="2">Option 2</option>
</field>

<!-- Category -->
<field name="categoria" type="category" label="Category"
    extension="com_content" />

<!-- Article -->
<field name="articulo" type="article" label="Article" />

<!-- User -->
<field name="usuario" type="user" label="User" />

<!-- Menu -->
<field name="menu" type="menu" label="Menu" />

<!-- Module Layout -->
<field name="layout" type="modulelayout" label="Layout" />

<!-- Cache -->
<field name="cache" type="list" label="Cache" default="1">
    <option value="0">No</option>
    <option value="1">Yes</option>
</field>
```

## Get Data in Template

```php
<?php
// From displayData
$params = $displayData['params'];
$module = $displayData['module'];
$items = $displayData['items'];

// Access parameters
$titulo = $params->get('title', 'Default');
$count = (int) $params->get('count', 5);

// Iterate items
foreach ($items as $item) {
    echo $item->title;
}
?>
```

## Safe HTML Escaping

```php
<?php
// Plain text
<?php echo htmlspecialchars($variable, ENT_QUOTES, 'UTF-8'); ?>

// With HTMLHelper
<?php echo HTMLHelper::_('string.truncate', $item->title, 50); ?>

// HTML attributes
<img alt="<?php echo htmlspecialchars($alt); ?>" />

// URLs
<?php echo Route::_('index.php?option=com_content&view=article&id=' . $item->id); ?>
?>
```

## Useful Commands

```bash
# View Joomla logs
tail -f /var/www/html/joomla/administrator/logs/joomla.log

# Clear cache from CLI
php /path/to/joomla/cli/joomla.php cache:clean

# View module information
grep -r "mod_mimodulo" /var/www/html/joomla/modules/

# Package for distribution
cd modules && zip -r mod_mimodulo.zip mod_mimodulo/
```

## Common Errors

| Error | Cause | Solution |
|-------|-------|----------|
| Class not found | Incorrect namespace | Verify PSR-4 path in manifest |
| No output | Dispatcher does not prepare data | Add getLayoutData() |
| Empty parameters | manifest.xml without config | Add <config><fields> |
| Template not found | Incorrect path | Verify tmpl/ folder |
| DB with no data | Incorrect query | Verify quoteName() and SQL syntax |

## Quick Testing

```php
<?php
// Test in template temporarily
echo '<pre>';
var_dump($displayData);
echo '</pre>';

// With Joomla Factory
use Joomla\CMS\Factory;
$db = Factory::getDbo();
var_dump($db->loadObjectList());
?>
```

## Debugging in Joomla

```php
<?php
// Enable debug mode in configuration.php
public $debug = true;
public $debug_lang = true;

// Custom log
use Joomla\CMS\Log\Log;
Log::add('My message', Log::INFO, 'com_content');

// View logs
// /administrator/logs/joomla.log
?>
```
