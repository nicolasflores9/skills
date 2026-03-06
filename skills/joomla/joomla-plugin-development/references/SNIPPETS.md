# Snippets Útiles: Copy & Paste para Plugins Joomla 5/6

## 1. Template Mínimo de Plugin

### manifest.xml
```xml
<?xml version="1.0" encoding="utf-8"?>
<extension type="plugin" group="system">
    <name>PLG_SYSTEM_TEMPLATE</name>
    <author>Tu Nombre</author>
    <creationDate>2025-03-06</creationDate>
    <copyright>Copyright 2025</copyright>
    <license>GNU General Public License version 2 or later</license>
    <version>1.0.0</version>
    <description>PLG_SYSTEM_TEMPLATE_DESCRIPTION</description>
    <targetPlatform version="5.0" />

    <namespace path="src">MyCompany\Plugin\System\Template</namespace>

    <files>
        <file>manifest.xml</file>
        <folder plugin="template">services</folder>
        <folder>src</folder>
        <folder>language</folder>
    </files>
</extension>
```

### services/provider.php
```php
<?php
namespace MyCompany\Plugin\System\Template;

use Joomla\CMS\Extension\PluginInterface;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $container->set(
            PluginInterface::class,
            function (Container $c) {
                return new Extension(
                    $c->get('dispatcher'),
                    (array) PluginHelper::getPlugin('system', 'template')
                );
            }
        );
    }
}
```

### src/Extension/Template.php
```php
<?php
namespace MyCompany\Plugin\System\Template;

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Event\SubscriberInterface;
use Joomla\CMS\Event\System\AfterInitialiseEvent;

class Extension extends CMSPlugin implements SubscriberInterface
{
    protected $autoloadLanguage = true;
    protected $allowLegacyListeners = false;

    public static function getSubscribedEvents(): array
    {
        return [
            'onAfterInitialise' => 'onAfterInitialise',
        ];
    }

    public function onAfterInitialise(AfterInitialiseEvent $event)
    {
        // Tu código aquí
    }
}
```

### language/en-GB/plg_system_template.ini
```ini
PLG_SYSTEM_TEMPLATE="Plugin Template"
PLG_SYSTEM_TEMPLATE_DESCRIPTION="A template plugin for Joomla"
```

## 2. Acceder a Servicios Comunes

### Base de Datos
```php
use Joomla\Database\DatabaseInterface;

// Opción 1: Mediante Factory
$db = Factory::getDbo();

// Opción 2: Mediante contenedor (inyección)
$db = $this->getContainer()->get(DatabaseInterface::class);

// Usar
$query = $db->getQuery(true)
    ->select('*')
    ->from($db->quoteName('#__articles'))
    ->where($db->quoteName('id') . ' = :id')
    ->bind(':id', $id);

$db->setQuery($query);
$result = $db->loadObject();
```

### Usuario Actual
```php
use Joomla\CMS\Factory;

$user = Factory::getUser();

// Propiedades
$userId = $user->id;
$username = $user->username;
$email = $user->email;
$name = $user->name;

// Verificar si está logueado
if ($user->id === 0) {
    // Es un invitado (guest)
}

// Verificar permisos
if ($user->authorise('core.edit', 'com_content')) {
    // Tiene permisos de edición
}
```

### Aplicación
```php
use Joomla\CMS\Factory;

$app = Factory::getApplication();

// Mostrar mensaje
$app->enqueueMessage('Mensaje de éxito', 'message');
$app->enqueueMessage('Advertencia', 'warning');
$app->enqueueMessage('Error', 'error');

// Logging
$logger = $app->getLogger();
$logger->info('Información', ['category' => 'plugin']);
$logger->error('Error', ['exception' => $e]);

// Obtener configuración
$config = Factory::getConfig();
$sitename = $config->get('sitename');
$siteurl = $config->get('live_site');
```

### Cache
```php
use Joomla\CMS\Cache\CacheFactory;

$cache = CacheFactory::getCache('_system');

// Leer del cache
if ($data = $cache->get('mi_clave')) {
    return $data;
}

// Procesar
$data = $this->procesamiento();

// Guardar en cache (3600 seg = 1 hora)
$cache->store($data, 'mi_clave', '_system', 3600);

// Limpiar un item
$cache->remove('mi_clave', '_system');

// Limpiar todo
$cache->clean('_system');
```

## 3. Patrones de Eventos Comunes

### Evento de Sistema
```php
use Joomla\CMS\Event\System\AfterInitialiseEvent;

public function onAfterInitialise(AfterInitialiseEvent $event)
{
    // Se ejecuta después de inicializar Joomla
    // Útil para configuración global
}
```

### Evento de Contenido - Preparación
```php
use Joomla\CMS\Event\Content\ContentPrepareEvent;

public function onContentPrepare(ContentPrepareEvent $event)
{
    $article = $event->getArgument('0');
    $params = $event->getArgument('1');

    if (!$article || !isset($article->text)) {
        return;
    }

    // Modificar el contenido
    $article->text = $this->procesarContenido($article->text);

    // Actualizar el evento
    $event->setArgument('0', $article);
}
```

### Evento de Usuario - Login
```php
use Joomla\CMS\Event\User\UserLoginEvent;

public function onUserLogin(UserLoginEvent $event)
{
    $response = $event->getArgument('response');
    $user = $event->getArgument('1');

    // $response contiene: username, password_clear, error
    // $user es el objeto usuario

    $username = $response['username'];
}
```

### Evento de Usuario - Save
```php
use Joomla\CMS\Event\User\UserAfterSaveEvent;

public function onUserAfterSave(UserAfterSaveEvent $event)
{
    $user = $event->getArgument('0');
    $isNew = $event->getArgument('1');

    if ($isNew) {
        // Nuevo usuario creado
        // Enviar email de bienvenida, etc.
    } else {
        // Usuario existente actualizado
    }
}
```

## 4. Validación y Filtrado

### Validar Entrada
```php
use Joomla\CMS\Filter\InputFilter;

$filter = InputFilter::getInstance();

// Tipos comunes
$text = $filter->clean($_GET['text'], 'STRING');
$number = $filter->clean($_GET['id'], 'INT');
$email = $filter->clean($_GET['email'], 'EMAIL');
$bool = $filter->clean($_GET['flag'], 'BOOLEAN');
$array = $filter->clean($_GET['items'], 'ARRAY');
$html = $filter->clean($_POST['content'], 'HTML');

// Validación personalizada
if (strlen($text) > 255) {
    // Rechazar
    return false;
}
```

### Escapar Salida
```php
// Para texto plano
echo htmlspecialchars($userContent, ENT_QUOTES, 'UTF-8');

// Para HTML
echo $userContent; // Si es HTML sanitizado

// Usar HTMLHelper
use Joomla\CMS\HTML\HTMLHelper;
echo HTMLHelper::_('common.escape', $userContent);
```

## 5. Manejo de Errores

### Try-Catch
```php
try {
    $db = Factory::getDbo();
    $query = $db->getQuery(true)
        ->select('*')
        ->from($db->quoteName('#__articles'));

    $db->setQuery($query);
    $results = $db->loadObjectList();

} catch (\Exception $e) {
    $app = Factory::getApplication();
    $app->enqueueMessage(
        'Error: ' . $e->getMessage(),
        'error'
    );

    $logger = $app->getLogger();
    $logger->error('DB Error', ['exception' => $e]);
}
```

### Validación y Retorno
```php
public function onContentBeforeSave(ContentBeforeSaveEvent $event)
{
    $article = $event->getArgument('1');

    // Validar
    if (empty($article->title)) {
        $app = Factory::getApplication();
        $app->enqueueMessage('Título requerido', 'error');
        return false; // Prevenir guardado
    }

    // Pasar validación
    return true;
}
```

## 6. Internacionalización

### En manifest.xml
```xml
<config>
    <fields name="params">
        <fieldset name="basic">
            <field
                name="custom_text"
                type="text"
                label="PLG_MYPLUGIN_CUSTOM_TEXT_LABEL"
                description="PLG_MYPLUGIN_CUSTOM_TEXT_DESCRIPTION"
                default="Default"
            />
        </fieldset>
    </fields>
</config>
```

### En .ini
```ini
PLG_MYPLUGIN="My Plugin"
PLG_MYPLUGIN_DESCRIPTION="Description of the plugin"
PLG_MYPLUGIN_CUSTOM_TEXT_LABEL="Custom Text"
PLG_MYPLUGIN_CUSTOM_TEXT_DESCRIPTION="Enter custom text here"
```

### En PHP
```php
// Acceder a traducciones
$text = Text::_('PLG_MYPLUGIN_CUSTOM_LABEL');

// Con parámetros
$text = Text::_('PLG_MYPLUGIN_HELLO_USER');
// La traducción podría ser: "Hello {USER}"
$text = sprintf($text, $username);
```

## 7. Configuración de Plugin

### Acceder a Parámetros
```php
class Extension extends CMSPlugin
{
    public function onAfterInitialise($event)
    {
        // Acceso simple
        $value = $this->params->get('param_name');

        // Con valor por defecto
        $value = $this->params->get('param_name', 'default_value');

        // Acceso con tipo
        $value = $this->params->get('param_name', true, 'bool');

        // Parámetro dentro de grupo
        $subvalue = $this->params->get('group.sub_param');
    }
}
```

### En manifest.xml
```xml
<config>
    <fields name="params">
        <fieldset name="basic" label="PLG_MYPLUGIN_BASIC">
            <field
                name="enabled"
                type="checkbox"
                label="Enabled"
                default="1"
            />
            <field
                name="max_items"
                type="text"
                label="Max Items"
                default="10"
                filter="integer"
            />
        </fieldset>

        <fieldset name="advanced" label="PLG_MYPLUGIN_ADVANCED">
            <field
                name="debug"
                type="checkbox"
                label="Debug Mode"
                default="0"
            />
        </fieldset>
    </fields>
</config>
```

## 8. Queries Avanzadas

### Select con Joins
```php
$db = Factory::getDbo();
$query = $db->getQuery(true)
    ->select($db->quoteName(['a.id', 'a.title', 'u.name']))
    ->from($db->quoteName('#__articles') . ' AS a')
    ->innerJoin(
        $db->quoteName('#__users') . ' AS u ON a.created_by = u.id'
    )
    ->where($db->quoteName('a.published') . ' = 1')
    ->order($db->quoteName('a.created') . ' DESC');

$db->setQuery($query);
$results = $db->loadObjectList();
```

### Insert
```php
$db = Factory::getDbo();

$object = new stdClass();
$object->title = 'New Article';
$object->text = 'Content';
$object->created = date('Y-m-d H:i:s');

$result = $db->insertObject('#__articles', $object);

if ($result) {
    $newId = $object->id;
}
```

### Update
```php
$db = Factory::getDbo();

$object = new stdClass();
$object->id = 5;
$object->title = 'Updated Title';

$db->updateObject('#__articles', $object, 'id');
```

### Delete
```php
$db = Factory::getDbo();
$query = $db->getQuery(true)
    ->delete($db->quoteName('#__articles'))
    ->where($db->quoteName('id') . ' = :id')
    ->bind(':id', $id);

$db->setQuery($query);
$db->execute();
```

## 9. Inyección de Dependencias Personalizada

### En services/provider.php
```php
class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        // Registrar servicio personalizado
        $container->set(
            'my.custom.service',
            function (Container $c) {
                return new MyCustomService(
                    $c->get(DatabaseInterface::class),
                    $c->get('logger')
                );
            }
        );

        // Registrar el plugin con inyección
        $container->set(
            PluginInterface::class,
            function (Container $c) {
                $plugin = new Extension(
                    $c->get('dispatcher'),
                    (array) PluginHelper::getPlugin('system', 'myservice')
                );

                // Inyectar servicios
                $plugin->setContainer($c);
                $plugin->setCustomService(
                    $c->get('my.custom.service')
                );

                return $plugin;
            }
        );
    }
}
```

### En Extension.php
```php
class Extension extends CMSPlugin
{
    private $customService;

    public function setCustomService(MyCustomService $service): self
    {
        $this->customService = $service;
        return $this;
    }

    public function onAfterInitialise($event)
    {
        // Usar el servicio inyectado
        $result = $this->customService->doSomething();
    }
}
```

## 10. Checklist de Implementación

- [ ] Crear carpeta en `plugins/tipo/nombre/`
- [ ] Crear `manifest.xml` con namespace correcto
- [ ] Crear `services/provider.php` con namespace exacto
- [ ] Crear `src/Extension/Nombre.php` con SubscriberInterface
- [ ] Implementar `getSubscribedEvents()`
- [ ] Crear archivos `.ini` en `language/en-GB/`
- [ ] Establecer `$autoloadLanguage = true`
- [ ] Establecer `$allowLegacyListeners = false`
- [ ] Implementar métodos de eventos
- [ ] Validar entrada en métodos
- [ ] Escapar salida en métodos
- [ ] Agregar logging/debugging
- [ ] Probar en Panel Control
- [ ] Verificar logs en `logs/joomla.log`
- [ ] Crear estructura de referencias (referencias.md)
