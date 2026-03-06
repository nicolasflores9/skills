# Referencia Rápida: Plugins Joomla 5/6

## Checklist Rápido

```
Crear plugin:
1. mkdir plugins/[group]/[name]/
2. Crear manifest.xml
3. Crear services/provider.php
4. Crear src/Extension/Name.php
5. Crear language/en-GB/files.ini
6. Panel Control > Extensiones > Plugins > Habilitar
```

## Estructura Mínima

```php
// manifest.xml
<?xml version="1.0" encoding="utf-8"?>
<extension type="plugin" group="system">
    <name>PLG_SYSTEM_EXAMPLE</name>
    <namespace path="src">MyCompany\Plugin\System\Example</namespace>
    <files>
        <folder plugin="example">services</folder>
        <folder>src</folder>
        <folder>language</folder>
    </files>
</extension>

// services/provider.php
namespace MyCompany\Plugin\System\Example;
use Joomla\CMS\Extension\PluginInterface;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface {
    public function register(Container $container) {
        $container->set(PluginInterface::class, function (Container $c) {
            return new Extension(
                $c->get('dispatcher'),
                (array) PluginHelper::getPlugin('system', 'example')
            );
        });
    }
}

// src/Extension/Example.php
namespace MyCompany\Plugin\System\Example;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Event\SubscriberInterface;
use Joomla\CMS\Event\System\AfterInitialiseEvent;

class Extension extends CMSPlugin implements SubscriberInterface {
    protected $autoloadLanguage = true;
    protected $allowLegacyListeners = false;

    public static function getSubscribedEvents(): array {
        return ['onAfterInitialise' => 'onAfterInitialise'];
    }

    public function onAfterInitialise(AfterInitialiseEvent $event) {
        // Tu código
    }
}
```

## Eventos Principales

### Sistema
| Evento | Cuándo |
|--------|--------|
| onAfterInitialise | Después inicializar |
| onAfterRoute | Después enrutar |
| onAfterDispatch | Después despachar |
| onBeforeRender | Antes renderizar |
| onAfterRender | Después renderizar |

### Contenido
| Evento | Cuándo |
|--------|--------|
| onContentPrepare | Antes mostrar |
| onContentBeforeSave | Validación previa |
| onContentAfterSave | Post-procesamiento |
| onContentBeforeDelete | Antes eliminar |
| onContentAfterDelete | Después eliminar |

### Usuario
| Evento | Cuándo |
|--------|--------|
| onUserLogin | Después login |
| onUserLogout | Después logout |
| onUserBeforeSave | Antes guardar |
| onUserAfterSave | Después guardar |

## Event Classes

```php
// Importar
use Joomla\CMS\Event\Content\ContentPrepareEvent;
use Joomla\CMS\Event\System\AfterInitialiseEvent;
use Joomla\CMS\Event\User\UserLoginEvent;

// Usar
public function onContentPrepare(ContentPrepareEvent $event) {
    $article = $event->getArgument('0');
    $article->text = str_replace('foo', 'bar', $article->text);
    $event->setArgument('0', $article);
}
```

## Servicios Comunes

```php
// Importar
use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;

// Base de datos
$db = Factory::getDbo();
$db = $this->getContainer()->get(DatabaseInterface::class);

// Usuario
$user = Factory::getUser();
$user->id, $user->email, $user->name

// Aplicación
$app = Factory::getApplication();
$app->enqueueMessage('Texto', 'message'); // error, warning
$app->getLogger()->info('Log');

// Configuración
$config = Factory::getConfig();
$config->get('sitename');
$config->get('live_site');

// Cache
use Joomla\CMS\Cache\CacheFactory;
$cache = CacheFactory::getCache('_system');
$cache->get('key'); // leer
$cache->store($data, 'key', '_system', 3600); // guardar

// Traducción
use Joomla\CMS\Language\Text;
$text = Text::_('PLG_PLUGIN_LABEL');
```

## Parámetros

```php
// En manifest.xml
<config>
    <fields name="params">
        <fieldset name="basic">
            <field name="enabled" type="checkbox" default="1" />
            <field name="text_option" type="text" default="value" />
            <field name="select_option" type="list">
                <option value="1">Option 1</option>
                <option value="2">Option 2</option>
            </field>
        </fieldset>
    </fields>
</config>

// En PHP
$enabled = $this->params->get('enabled', true);
$text = $this->params->get('text_option', 'default');
$selected = $this->params->get('select_option', '1');
```

## Validación y Filtrado

```php
use Joomla\CMS\Filter\InputFilter;
use Joomla\CMS\HTML\HTMLHelper;

// Validar entrada
$filter = InputFilter::getInstance();
$text = $filter->clean($_GET['text'], 'STRING');
$id = $filter->clean($_GET['id'], 'INT');
$email = $filter->clean($_GET['email'], 'EMAIL');

// Escapar salida
echo htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
echo HTMLHelper::_('common.escape', $text);

// Verificar permisos
$user = Factory::getUser();
if (!$user->authorise('core.edit', 'com_content')) {
    return; // No hacer nada
}
```

## Queries

```php
$db = Factory::getDbo();

// Select
$query = $db->getQuery(true)
    ->select('*')
    ->from($db->quoteName('#__articles'))
    ->where($db->quoteName('id') . ' = :id')
    ->bind(':id', $id);
$db->setQuery($query);
$result = $db->loadObject();

// Insert
$obj = new stdClass();
$obj->title = 'Title';
$db->insertObject('#__articles', $obj);

// Update
$obj->id = 1;
$obj->title = 'New Title';
$db->updateObject('#__articles', $obj, 'id');

// Delete
$query = $db->getQuery(true)
    ->delete('#__articles')
    ->where($db->quoteName('id') . ' = :id')
    ->bind(':id', $id);
$db->setQuery($query);
$db->execute();
```

## Errores Comunes

| Error | Solución |
|-------|----------|
| "Class not found" | Verificar namespace en manifest, provider y Extension |
| Plugin no aparece | Limpiar cache, verificar manifest.xml |
| Evento no dispara | getSubscribedEvents() correcto, plugin habilitado |
| allowLegacyListeners | Debe ser false para Joomla 5/6 |
| Parámetros no se guardan | Verificar sintaxis en manifest.xml |
| Sin traducciones | $autoloadLanguage = true, archivo .ini correcto |

## Debugging

```php
// Log
$app = Factory::getApplication();
$logger = $app->getLogger();
$logger->info('Mensaje');
$logger->error('Error: ' . $e->getMessage());

// Revisar
tail -f logs/joomla.log

// Mensaje al usuario
$app->enqueueMessage('Texto', 'message');

// Exception
try {
    // código
} catch (\Exception $e) {
    $app->enqueueMessage('Error: ' . $e->getMessage(), 'error');
    $logger->error('Detalle', ['exception' => $e]);
}
```

## Inyección de Dependencias

```php
// En services/provider.php
$plugin->setContainer($c);

// En Extension.php
use Joomla\DI\Traits\ContainerAwareTrait;

class Extension extends CMSPlugin {
    use ContainerAwareTrait;

    public function myMethod() {
        $db = $this->getContainer()->get(DatabaseInterface::class);
    }
}
```

## Archivos Necesarios

```
plg_system_example/
├── manifest.xml
├── services/provider.php
├── src/Extension/Example.php
├── language/en-GB/
│   ├── plg_system_example.ini
│   └── plg_system_example.sys.ini
└── (opcional) src/Helper/Helper.php
```

## Instalación

1. Crear carpeta en `plugins/[group]/[name]/`
2. Copiar todos los archivos
3. Panel Control > Extensiones > Plugins
4. Buscar plugin por nombre
5. Hacer clic en estado para habilitar
6. Verificar en logs: `logs/joomla.log`

## Prioridades de Eventos

```php
public static function getSubscribedEvents(): array
{
    return [
        'onAfterInitialise' => ['handler', 0], // 0 = ejecuta primero
        'onAfterRoute' => ['handler', 5],      // 5 = normal
        'onAfterDispatch' => ['handler', 10],  // 10 = ejecuta último
    ];
}
```

## Múltiples Handlers

```php
public static function getSubscribedEvents(): array
{
    return [
        'onContentPrepare' => [
            ['primaryHandler', 0],
            ['secondaryHandler', 10],
        ],
    ];
}

public function primaryHandler(ContentPrepareEvent $event) {
    // Se ejecuta primero
}

public function secondaryHandler(ContentPrepareEvent $event) {
    // Se ejecuta segundo
}
```

## Métodos de Event Classes

```php
// Acceder a argumentos
$article = $event->getArgument('0');
$article = $event->getArgument('article'); // si está nombrado

// Modificar argumentos
$event->setArgument('0', $newValue);

// Obtener todos los argumentos
$all = $event->getArguments();

// Métodos específicos (si existen)
$article = $event->getArticle();
$event->setArticle($article);
```

## Recursos

- [Joomla Manual](https://manual.joomla.org/)
- [Joomla Docs](https://docs.joomla.org/)
- [Joomla API](https://api.joomla.org/)
- [Forum Joomla](https://forum.joomla.org/)
