# Cheat Sheet: Desarrollo de Módulos Joomla 5/6

## Estructura Rápida

```bash
# Crear estructura básica
mkdir -p mod_mimodulo/{src/{Dispatcher,Helper},services,tmpl,language/en-GB}

# Crear archivos base
touch mod_mimodulo/{manifest.xml,mod_mimodulo.php}
touch mod_mimodulo/src/Dispatcher/Dispatcher.php
touch mod_mimodulo/src/Helper/MiHelper.php
touch mod_mimodulo/services/provider.php
touch mod_mimodulo/tmpl/{default.php,default.xml}
touch mod_mimodulo/language/en-GB/{mod_mimodulo.ini,mod_mimodulo.sys.ini}
```

## Templates PHP Rápidas

### manifest.xml minimal
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

### Dispatcher básico
```php
<?php
namespace Joomla\Module\Mimodulo\Dispatcher;

use Joomla\CMS\Dispatcher\AbstractModuleDispatcher;

class Dispatcher extends AbstractModuleDispatcher
{
    protected function getLayoutData(): array
    {
        $data = parent::getLayoutData();
        // Agregar datos aquí
        return $data;
    }
}
```

### Helper con BD
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

## Campos Comunes en manifest.xml

```xml
<!-- Text -->
<field name="texto" type="text" label="Etiqueta" default="valor" />

<!-- Integer -->
<field name="numero" type="integer" label="Número" min="1" max="100" />

<!-- Textarea -->
<field name="descripcion" type="textarea" label="Descripción" rows="5" />

<!-- List/Dropdown -->
<field name="opcion" type="list" label="Opción">
    <option value="1">Opción 1</option>
    <option value="2">Opción 2</option>
</field>

<!-- Category -->
<field name="categoria" type="category" label="Categoría"
    extension="com_content" />

<!-- Article -->
<field name="articulo" type="article" label="Artículo" />

<!-- User -->
<field name="usuario" type="user" label="Usuario" />

<!-- Menu -->
<field name="menu" type="menu" label="Menú" />

<!-- Module Layout -->
<field name="layout" type="modulelayout" label="Layout" />

<!-- Cache -->
<field name="cache" type="list" label="Cache" default="1">
    <option value="0">No</option>
    <option value="1">Sí</option>
</field>
```

## Obtener Datos en Template

```php
<?php
// Desde displayData
$params = $displayData['params'];
$module = $displayData['module'];
$items = $displayData['items'];

// Acceder a parámetros
$titulo = $params->get('title', 'Por defecto');
$count = (int) $params->get('count', 5);

// Iterar items
foreach ($items as $item) {
    echo $item->title;
}
?>
```

## Escapado Seguro en HTML

```php
<?php
// Texto simple
<?php echo htmlspecialchars($variable, ENT_QUOTES, 'UTF-8'); ?>

// Con HTMLHelper
<?php echo HTMLHelper::_('string.truncate', $item->title, 50); ?>

// Atributos HTML
<img alt="<?php echo htmlspecialchars($alt); ?>" />

// URLs
<?php echo Route::_('index.php?option=com_content&view=article&id=' . $item->id); ?>
?>
```

## Comandos Útiles

```bash
# Ver logs en Joomla
tail -f /var/www/html/joomla/administrator/logs/joomla.log

# Limpiar cache desde CLI
php /path/to/joomla/cli/joomla.php cache:clean

# Ver información del módulo
grep -r "mod_mimodulo" /var/www/html/joomla/modules/

# Empaquetar para distribución
cd modules && zip -r mod_mimodulo.zip mod_mimodulo/
```

## Errores Comunes

| Error | Causa | Solución |
|-------|-------|----------|
| Class not found | Namespace incorrecto | Verificar PSR-4 path en manifest |
| No output | Dispatcher no prepara datos | Agregar getLayoutData() |
| Parámetros vacíos | manifest.xml sin config | Agregar <config><fields> |
| Template no encontrado | Ruta incorrecta | Verificar tmpl/ folder |
| BD sin datos | Query incorrecto | Verificar quoteName() y sintaxis SQL |

## Testing Rápido

```php
<?php
// Probar en template temporalmente
echo '<pre>';
var_dump($displayData);
echo '</pre>';

// Con Joomla Factory
use Joomla\CMS\Factory;
$db = Factory::getDbo();
var_dump($db->loadObjectList());
?>
```

## Debugging en Joomla

```php
<?php
// Activar debug mode en configuration.php
public $debug = true;
public $debug_lang = true;

// Log personalizado
use Joomla\CMS\Log\Log;
Log::add('Mi mensaje', Log::INFO, 'com_content');

// Ver logs
// /administrator/logs/joomla.log
?>
```
