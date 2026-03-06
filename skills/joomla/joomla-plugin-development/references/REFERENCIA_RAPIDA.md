# Quick Reference: Joomla 5/6 Plugins

## Quick Checklist

```
Create plugin:
1. mkdir plugins/[group]/[name]/
2. Create manifest.xml
3. Create services/provider.php
4. Create src/Extension/Name.php
5. Create language/en-GB/files.ini
6. Control Panel > Extensions > Plugins > Enable
```

## Minimal Structure

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
        // Your code
    }
}
```

## Main Events

### System
| Event | When |
|-------|------|
| onAfterInitialise | After initialization |
| onAfterRoute | After routing |
| onAfterDispatch | After dispatching |
| onBeforeRender | Before rendering |
| onAfterRender | After rendering |

### Content
| Event | When |
|-------|------|
| onContentPrepare | Before display |
| onContentBeforeSave | Pre-save validation |
| onContentAfterSave | Post-processing |
| onContentBeforeDelete | Before deletion |
| onContentAfterDelete | After deletion |

### User
| Event | When |
|-------|------|
| onUserLogin | After login |
| onUserLogout | After logout |
| onUserBeforeSave | Before saving |
| onUserAfterSave | After saving |

## Event Classes

```php
// Import
use Joomla\CMS\Event\Content\ContentPrepareEvent;
use Joomla\CMS\Event\System\AfterInitialiseEvent;
use Joomla\CMS\Event\User\UserLoginEvent;

// Usage
public function onContentPrepare(ContentPrepareEvent $event) {
    $article = $event->getArgument('0');
    $article->text = str_replace('foo', 'bar', $article->text);
    $event->setArgument('0', $article);
}
```

## Common Services

```php
// Import
use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;

// Database
$db = Factory::getDbo();
$db = $this->getContainer()->get(DatabaseInterface::class);

// User
$user = Factory::getUser();
$user->id, $user->email, $user->name

// Application
$app = Factory::getApplication();
$app->enqueueMessage('Text', 'message'); // error, warning
$app->getLogger()->info('Log');

// Configuration
$config = Factory::getConfig();
$config->get('sitename');
$config->get('live_site');

// Cache
use Joomla\CMS\Cache\CacheFactory;
$cache = CacheFactory::getCache('_system');
$cache->get('key'); // read
$cache->store($data, 'key', '_system', 3600); // store

// Translation
use Joomla\CMS\Language\Text;
$text = Text::_('PLG_PLUGIN_LABEL');
```

## Parameters

```php
// In manifest.xml
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

// In PHP
$enabled = $this->params->get('enabled', true);
$text = $this->params->get('text_option', 'default');
$selected = $this->params->get('select_option', '1');
```

## Validation and Filtering

```php
use Joomla\CMS\Filter\InputFilter;
use Joomla\CMS\HTML\HTMLHelper;

// Validate input
$filter = InputFilter::getInstance();
$text = $filter->clean($_GET['text'], 'STRING');
$id = $filter->clean($_GET['id'], 'INT');
$email = $filter->clean($_GET['email'], 'EMAIL');

// Escape output
echo htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
echo HTMLHelper::_('common.escape', $text);

// Check permissions
$user = Factory::getUser();
if (!$user->authorise('core.edit', 'com_content')) {
    return; // Do nothing
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

## Common Errors

| Error | Solution |
|-------|----------|
| "Class not found" | Verify namespace in manifest, provider, and Extension |
| Plugin does not appear | Clear cache, verify manifest.xml |
| Event does not fire | Correct getSubscribedEvents(), plugin enabled |
| allowLegacyListeners | Must be false for Joomla 5/6 |
| Parameters not saving | Verify syntax in manifest.xml |
| No translations | $autoloadLanguage = true, correct .ini file |

## Debugging

```php
// Log
$app = Factory::getApplication();
$logger = $app->getLogger();
$logger->info('Message');
$logger->error('Error: ' . $e->getMessage());

// Review
tail -f logs/joomla.log

// Message to user
$app->enqueueMessage('Text', 'message');

// Exception
try {
    // code
} catch (\Exception $e) {
    $app->enqueueMessage('Error: ' . $e->getMessage(), 'error');
    $logger->error('Detail', ['exception' => $e]);
}
```

## Dependency Injection

```php
// In services/provider.php
$plugin->setContainer($c);

// In Extension.php
use Joomla\DI\Traits\ContainerAwareTrait;

class Extension extends CMSPlugin {
    use ContainerAwareTrait;

    public function myMethod() {
        $db = $this->getContainer()->get(DatabaseInterface::class);
    }
}
```

## Required Files

```
plg_system_example/
├── manifest.xml
├── services/provider.php
├── src/Extension/Example.php
├── language/en-GB/
│   ├── plg_system_example.ini
│   └── plg_system_example.sys.ini
└── (optional) src/Helper/Helper.php
```

## Installation

1. Create folder at `plugins/[group]/[name]/`
2. Copy all files
3. Control Panel > Extensions > Plugins
4. Search plugin by name
5. Click on status to enable
6. Verify in logs: `logs/joomla.log`

## Event Priorities

```php
public static function getSubscribedEvents(): array
{
    return [
        'onAfterInitialise' => ['handler', 0], // 0 = executes first
        'onAfterRoute' => ['handler', 5],      // 5 = normal
        'onAfterDispatch' => ['handler', 10],  // 10 = executes last
    ];
}
```

## Multiple Handlers

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
    // Executes first
}

public function secondaryHandler(ContentPrepareEvent $event) {
    // Executes second
}
```

## Event Class Methods

```php
// Access arguments
$article = $event->getArgument('0');
$article = $event->getArgument('article'); // if named

// Modify arguments
$event->setArgument('0', $newValue);

// Get all arguments
$all = $event->getArguments();

// Specific methods (if available)
$article = $event->getArticle();
$event->setArticle($article);
```

## Resources

- [Joomla Manual](https://manual.joomla.org/)
- [Joomla Docs](https://docs.joomla.org/)
- [Joomla API](https://api.joomla.org/)
- [Joomla Forum](https://forum.joomla.org/)
