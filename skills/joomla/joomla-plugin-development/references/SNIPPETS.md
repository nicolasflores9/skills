# Useful Snippets: Copy & Paste for Joomla 5/6 Plugins

## 1. Minimal Plugin Template

### manifest.xml
```xml
<?xml version="1.0" encoding="utf-8"?>
<extension type="plugin" group="system">
    <name>PLG_SYSTEM_TEMPLATE</name>
    <author>Your Name</author>
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
        // Your code here
    }
}
```

### language/en-GB/plg_system_template.ini
```ini
PLG_SYSTEM_TEMPLATE="Plugin Template"
PLG_SYSTEM_TEMPLATE_DESCRIPTION="A template plugin for Joomla"
```

## 2. Accessing Common Services

### Database
```php
use Joomla\Database\DatabaseInterface;

// Option 1: Via Factory
$db = Factory::getDbo();

// Option 2: Via container (injection)
$db = $this->getContainer()->get(DatabaseInterface::class);

// Usage
$query = $db->getQuery(true)
    ->select('*')
    ->from($db->quoteName('#__articles'))
    ->where($db->quoteName('id') . ' = :id')
    ->bind(':id', $id);

$db->setQuery($query);
$result = $db->loadObject();
```

### Current User
```php
use Joomla\CMS\Factory;

$user = Factory::getUser();

// Properties
$userId = $user->id;
$username = $user->username;
$email = $user->email;
$name = $user->name;

// Check if logged in
if ($user->id === 0) {
    // Is a guest
}

// Check permissions
if ($user->authorise('core.edit', 'com_content')) {
    // Has edit permissions
}
```

### Application
```php
use Joomla\CMS\Factory;

$app = Factory::getApplication();

// Display message
$app->enqueueMessage('Success message', 'message');
$app->enqueueMessage('Warning', 'warning');
$app->enqueueMessage('Error', 'error');

// Logging
$logger = $app->getLogger();
$logger->info('Information', ['category' => 'plugin']);
$logger->error('Error', ['exception' => $e]);

// Get configuration
$config = Factory::getConfig();
$sitename = $config->get('sitename');
$siteurl = $config->get('live_site');
```

### Cache
```php
use Joomla\CMS\Cache\CacheFactory;

$cache = CacheFactory::getCache('_system');

// Read from cache
if ($data = $cache->get('my_key')) {
    return $data;
}

// Process
$data = $this->processing();

// Store in cache (3600 sec = 1 hour)
$cache->store($data, 'my_key', '_system', 3600);

// Remove an item
$cache->remove('my_key', '_system');

// Clear all
$cache->clean('_system');
```

## 3. Common Event Patterns

### System Event
```php
use Joomla\CMS\Event\System\AfterInitialiseEvent;

public function onAfterInitialise(AfterInitialiseEvent $event)
{
    // Executes after Joomla initialization
    // Useful for global configuration
}
```

### Content Event - Preparation
```php
use Joomla\CMS\Event\Content\ContentPrepareEvent;

public function onContentPrepare(ContentPrepareEvent $event)
{
    $article = $event->getArgument('0');
    $params = $event->getArgument('1');

    if (!$article || !isset($article->text)) {
        return;
    }

    // Modify the content
    $article->text = $this->processContent($article->text);

    // Update the event
    $event->setArgument('0', $article);
}
```

### User Event - Login
```php
use Joomla\CMS\Event\User\UserLoginEvent;

public function onUserLogin(UserLoginEvent $event)
{
    $response = $event->getArgument('response');
    $user = $event->getArgument('1');

    // $response contains: username, password_clear, error
    // $user is the user object

    $username = $response['username'];
}
```

### User Event - Save
```php
use Joomla\CMS\Event\User\UserAfterSaveEvent;

public function onUserAfterSave(UserAfterSaveEvent $event)
{
    $user = $event->getArgument('0');
    $isNew = $event->getArgument('1');

    if ($isNew) {
        // New user created
        // Send welcome email, etc.
    } else {
        // Existing user updated
    }
}
```

## 4. Validation and Filtering

### Validate Input
```php
use Joomla\CMS\Filter\InputFilter;

$filter = InputFilter::getInstance();

// Common types
$text = $filter->clean($_GET['text'], 'STRING');
$number = $filter->clean($_GET['id'], 'INT');
$email = $filter->clean($_GET['email'], 'EMAIL');
$bool = $filter->clean($_GET['flag'], 'BOOLEAN');
$array = $filter->clean($_GET['items'], 'ARRAY');
$html = $filter->clean($_POST['content'], 'HTML');

// Custom validation
if (strlen($text) > 255) {
    // Reject
    return false;
}
```

### Escape Output
```php
// For plain text
echo htmlspecialchars($userContent, ENT_QUOTES, 'UTF-8');

// For HTML
echo $userContent; // If HTML is sanitized

// Use HTMLHelper
use Joomla\CMS\HTML\HTMLHelper;
echo HTMLHelper::_('common.escape', $userContent);
```

## 5. Error Handling

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

### Validation and Return
```php
public function onContentBeforeSave(ContentBeforeSaveEvent $event)
{
    $article = $event->getArgument('1');

    // Validate
    if (empty($article->title)) {
        $app = Factory::getApplication();
        $app->enqueueMessage('Title required', 'error');
        return false; // Prevent saving
    }

    // Pass validation
    return true;
}
```

## 6. Internationalization

### In manifest.xml
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

### In .ini
```ini
PLG_MYPLUGIN="My Plugin"
PLG_MYPLUGIN_DESCRIPTION="Description of the plugin"
PLG_MYPLUGIN_CUSTOM_TEXT_LABEL="Custom Text"
PLG_MYPLUGIN_CUSTOM_TEXT_DESCRIPTION="Enter custom text here"
```

### In PHP
```php
// Access translations
$text = Text::_('PLG_MYPLUGIN_CUSTOM_LABEL');

// With parameters
$text = Text::_('PLG_MYPLUGIN_HELLO_USER');
// The translation could be: "Hello {USER}"
$text = sprintf($text, $username);
```

## 7. Plugin Configuration

### Accessing Parameters
```php
class Extension extends CMSPlugin
{
    public function onAfterInitialise($event)
    {
        // Simple access
        $value = $this->params->get('param_name');

        // With default value
        $value = $this->params->get('param_name', 'default_value');

        // Access with type
        $value = $this->params->get('param_name', true, 'bool');

        // Parameter within a group
        $subvalue = $this->params->get('group.sub_param');
    }
}
```

### In manifest.xml
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

## 8. Advanced Queries

### Select with Joins
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

## 9. Custom Dependency Injection

### In services/provider.php
```php
class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        // Register custom service
        $container->set(
            'my.custom.service',
            function (Container $c) {
                return new MyCustomService(
                    $c->get(DatabaseInterface::class),
                    $c->get('logger')
                );
            }
        );

        // Register the plugin with injection
        $container->set(
            PluginInterface::class,
            function (Container $c) {
                $plugin = new Extension(
                    $c->get('dispatcher'),
                    (array) PluginHelper::getPlugin('system', 'myservice')
                );

                // Inject services
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

### In Extension.php
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
        // Use the injected service
        $result = $this->customService->doSomething();
    }
}
```

## 10. Implementation Checklist

- [ ] Create folder at `plugins/type/name/`
- [ ] Create `manifest.xml` with correct namespace
- [ ] Create `services/provider.php` with exact namespace
- [ ] Create `src/Extension/Name.php` with SubscriberInterface
- [ ] Implement `getSubscribedEvents()`
- [ ] Create `.ini` files in `language/en-GB/`
- [ ] Set `$autoloadLanguage = true`
- [ ] Set `$allowLegacyListeners = false`
- [ ] Implement event methods
- [ ] Validate input in methods
- [ ] Escape output in methods
- [ ] Add logging/debugging
- [ ] Test in Control Panel
- [ ] Verify logs at `logs/joomla.log`
- [ ] Create reference structure (references.md)
