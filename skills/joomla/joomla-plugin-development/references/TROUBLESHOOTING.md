# Troubleshooting Guide: Joomla 5/6 Plugins

## Installation Problems

### Plugin does not appear in the extensions list

**Symptoms:**
- The plugin is in the correct folder but does not appear in Control Panel > Extensions > Plugins
- The installation does not show errors

**Solutions:**

1. **Verify manifest.xml**
   - Ensure it is in the root of the plugin folder
   - Validate that the XML is valid (no special characters or unencoded accents)
   - Verify it has the XML declaration: `<?xml version="1.0" encoding="utf-8"?>`

2. **Verify permissions**
   ```bash
   chmod 755 plugins/system/myplugin
   chmod 644 plugins/system/myplugin/manifest.xml
   ```

3. **Clear cache**
   - Control Panel > System > Cache > Clear Cache
   - Or delete: `administrator/cache/autoload_psr4.php`

4. **Verify the type element**
   ```xml
   <!-- Correct -->
   <extension type="plugin" group="system">

   <!-- Incorrect -->
   <extension type="plg" group="system">
   <plugin type="system">
   ```

### Error "Fatal error: Class not found"

**Symptoms:**
- Error when enabling the plugin
- Message like "Class 'MyCompany\Plugin\System\Myexample\Extension' not found"

**Solutions:**

1. **Verify namespace in manifest.xml**
   ```xml
   <!-- MUST match exactly with the class -->
   <namespace path="src">MyCompany\Plugin\System\Myexample</namespace>
   ```

2. **Verify namespace in services/provider.php**
   ```php
   // MUST match with manifest
   namespace MyCompany\Plugin\System\Myexample;
   ```

3. **Verify path in manifest.xml**
   ```xml
   <!-- The plugin attribute MUST match the plugin name -->
   <folder plugin="myexample">services</folder>

   <!-- If the plugin is called "plg_system_myexample", this is correct -->
   <!-- If it is called "plg_system_myexample2", it should be plugin="myexample2" -->
   ```

4. **Regenerate PSR-4 cache**
   ```bash
   rm administrator/cache/autoload_psr4.php
   ```
   Then access the page and let Joomla regenerate it.

## Event Problems

### Event does not fire

**Symptoms:**
- The code in onContentPrepare does not execute
- The event is not called at all

**Solutions:**

1. **Verify getSubscribedEvents()**
   ```php
   // Correct
   public static function getSubscribedEvents(): array
   {
       return [
           'onContentPrepare' => 'onContentPrepare',
       ];
   }

   // Incorrect: missing return type
   public static function getSubscribedEvents()
   {
       return ['onContentPrepare' => 'onContentPrepare'];
   }
   ```

2. **Verify the plugin is enabled**
   - Control Panel > Extensions > Plugins
   - Status must be green (enabled)

3. **Verify the method exists**
   ```php
   // This exact method must exist
   public function onContentPrepare(ContentPrepareEvent $event)
   {
       // ...
   }
   ```

4. **Verify allowLegacyListeners**
   ```php
   // If TRUE, it tries to find legacy methods
   protected $allowLegacyListeners = false; // Correct for Joomla 5/6
   ```

5. **Check logs**
   ```bash
   tail -f logs/joomla.log
   ```
   Look for error messages related to the event

### Event fires but with invalid arguments

**Symptoms:**
- The event fires but arguments are null or empty
- Error when accessing event object properties

**Solutions:**

1. **Use correct Event Classes**
   ```php
   // Incorrect: without type hint
   public function onContentPrepare($event)
   {
       $article = $event->getArgument('0'); // May be null
   }

   // Correct: with type hint
   use Joomla\CMS\Event\Content\ContentPrepareEvent;

   public function onContentPrepare(ContentPrepareEvent $event)
   {
       $article = $event->getArgument('0'); // Safe access
   }
   ```

2. **Verify correct indices**
   ```php
   // Indices may vary by event
   // onContentPrepare: [0] = article, [1] = params
   $article = $event->getArgument('0');
   $params = $event->getArgument('1');

   // Use specific methods if available
   $article = $event->getArticle(); // Safer
   ```

3. **Validate arguments before use**
   ```php
   public function onContentPrepare(ContentPrepareEvent $event)
   {
       $article = $event->getArgument('0');

       // ALWAYS validate
       if (!$article || !property_exists($article, 'text')) {
           return;
       }

       // Now safe to use
       $article->text = $this->process($article->text);
   }
   ```

## Configuration Problems

### Parameters are not saving

**Symptoms:**
- Configuration fields do not appear
- Parameters reset when disabling/enabling

**Solutions:**

1. **Verify syntax in manifest.xml**
   ```xml
   <config>
       <fields name="params">
           <fieldset name="basic">
               <field
                   name="param_name"
                   type="text"
                   label="PLG_MYPLUGIN_PARAM_LABEL"
                   default="default_value"
               />
           </fieldset>
       </fields>
   </config>
   ```

2. **Verify valid field types**
   - text, textarea, checkbox, radio, select, list, etc.
   - Use `type="checkbox"` not `type="check"`

3. **Access parameters correctly**
   ```php
   // Correct
   $value = $this->params->get('param_name', 'default_value');

   // Incorrect
   $value = $this->params['param_name'];
   $value = $this->params->param_name;
   ```

### Translations are not loading

**Symptoms:**
- Labels show "PLG_MYPLUGIN_LABEL" instead of the text
- Languages are not applied correctly

**Solutions:**

1. **Verify directory structure**
   ```
   language/
   └── en-GB/
       ├── plg_system_myplugin.ini
       └── plg_system_myplugin.sys.ini
   ```

2. **Verify exact file name**
   ```
   Correct: plg_system_myplugin.ini
   Incorrect: plg_system_myplugin.php
   Incorrect: plg_system_my_plugin.ini (with underscore in name)
   ```

3. **Enable language autoloading**
   ```php
   class Extension extends CMSPlugin
   {
       protected $autoloadLanguage = true; // MUST be true
   }
   ```

4. **Verify the string prefix is correct**
   ```ini
   <!-- In manifest.xml and .ini files, use PLG_TYPE_NAME -->
   PLG_SYSTEM_MYPLUGIN="My Plugin"
   PLG_SYSTEM_MYPLUGIN_LABEL="Label"

   <!-- If the plugin is called "plg_content_example", use -->
   PLG_CONTENT_EXAMPLE="Example Plugin"
   PLG_CONTENT_EXAMPLE_LABEL="Label"
   ```

## Performance Problems

### The site becomes slow

**Symptoms:**
- Pages load more slowly after enabling the plugin
- High CPU/Memory usage

**Solutions:**

1. **Limit subscribed events**
   ```php
   // Bad: subscribing to many events
   public static function getSubscribedEvents(): array
   {
       return [
           'onAfterInitialise' => 'process',
           'onAfterRoute' => 'process',
           'onAfterDispatch' => 'process',
           'onBeforeRender' => 'process',
           'onAfterRender' => 'process',
       ];
   }

   // Better: only necessary events
   public static function getSubscribedEvents(): array
   {
       return [
           'onContentPrepare' => 'onContentPrepare',
       ];
   }
   ```

2. **Use cache**
   ```php
   use Joomla\CMS\Cache\CacheFactory;

   $cache = CacheFactory::getCache('_system');
   $key = 'plugin_result_' . $id;

   if ($result = $cache->get($key)) {
       return $result;
   }

   // Expensive processing
   $result = $this->expensiveOperation();

   // Store for 1 hour (3600 seconds)
   $cache->store($result, $key, '_system', 3600);
   ```

3. **Avoid unnecessary queries**
   ```php
   // Bad: query on every event
   public function onContentPrepare($event)
   {
       $db = Factory::getDbo();
       $query = $db->getQuery(true)->select('*')->from('#__articles');
       $db->setQuery($query);
       // This executes for EVERY article
   }

   // Better: cache or limit
   public function onContentPrepare($event)
   {
       static $articles = null;

       if ($articles === null) {
           $db = Factory::getDbo();
           $query = $db->getQuery(true)->select('*')->from('#__articles');
           $db->setQuery($query);
           $articles = $db->loadObjectList();
       }
   }
   ```

## Security Problems

### Access denied or permission errors

**Symptoms:**
- "Access Denied" even though the user has permissions
- The plugin tries to do things it cannot

**Solutions:**

1. **Verify user permissions**
   ```php
   use Joomla\CMS\Factory;

   $user = Factory::getUser();

   if (!$user->authorise('core.edit', 'com_content')) {
       // User does not have permissions
       return;
   }
   ```

2. **Always validate input**
   ```php
   use Joomla\CMS\Filter\InputFilter;

   $filter = InputFilter::getInstance();

   // Validate by type
   $text = $filter->clean($_GET['text'], 'STRING');
   $number = $filter->clean($_GET['number'], 'INT');
   $html = $filter->clean($_GET['html'], 'HTML');
   ```

3. **Escape output**
   ```php
   use Joomla\CMS\HTML\HTMLHelper;

   // For plain text
   echo HTMLHelper::_('common.escape', $userContent);

   // For HTML
   echo htmlspecialchars($userContent, ENT_QUOTES, 'UTF-8');
   ```

### Insecure SQL queries

**Symptoms:**
- Unexpected SQL errors
- Strange database behavior
- Possible SQL injection

**Solutions:**

1. **Use query binding**
   ```php
   // Correct
   $db = Factory::getDbo();
   $query = $db->getQuery(true)
       ->select('*')
       ->from($db->quoteName('#__articles'))
       ->where($db->quoteName('id') . ' = :id')
       ->bind(':id', $articleId);

   $db->setQuery($query);
   $result = $db->loadObject();

   // Incorrect
   $query = $db->getQuery(true)
       ->select('*')
       ->from($db->quoteName('#__articles'))
       ->where('id = ' . $articleId); // SQL INJECTION!
   ```

2. **Use quoteName for identifiers**
   ```php
   // Correct
   $query = $db->getQuery(true)
       ->select($db->quoteName('title'))
       ->from($db->quoteName('#__articles'));

   // Incorrect
   $query = $db->getQuery(true)
       ->select('title')
       ->from('#__articles');
   ```

## Compatibility Problems

### Error "Undefined class 'SubscriberInterface'"

**Symptoms:**
- Error during installation
- Joomla version too old

**Solutions:**

1. **Verify minimum version**
   ```xml
   <!-- In manifest.xml -->
   <targetPlatform version="5.0" />

   <!-- Means it requires Joomla 5.0 or higher -->
   ```

2. **For Joomla 4.4 or higher:**
   ```php
   use Joomla\Event\SubscriberInterface;
   ```

3. **For earlier versions, use the legacy pattern:**
   ```php
   class Extension extends CMSPlugin
   {
       public function onContentPrepare($context, &$article, &$params, $page = 0)
       {
           // Legacy pattern for Joomla 3-4
       }
   }
   ```

### Event Classes not available

**Symptoms:**
- "Undefined class 'ContentPrepareEvent'"
- Event Classes only available in Joomla 5.2+

**Solutions:**

1. **Verify Joomla version**
   - Event Classes available starting from Joomla 5.2
   - For earlier versions, use arguments by index

2. **Fallback for earlier versions**
   ```php
   public function onContentPrepare($event)
   {
       // Supports Event Classes (Joomla 5.2+)
       if ($event instanceof \Joomla\CMS\Event\EventInterface) {
           $article = $event->getArgument('0');
       } else {
           // Fallback for older versions
           $article = func_get_arg(0);
       }
   }
   ```

## Effective Debugging

### Enable logging

```php
use Joomla\CMS\Factory;

$app = Factory::getApplication();
$logger = $app->getLogger();

// Log info
$logger->info('Message', ['category' => 'plugin']);

// Log error
$logger->error('Error message', ['exception' => $e]);

// Check logs
tail -f logs/joomla.log
```

### Using Xdebug

1. Install Xdebug on the server
2. Configure IDE (VS Code, PhpStorm)
3. Add breakpoints in the plugin code
4. Browse the site to trigger breakpoints

### Verify plugin status

```bash
# In the Joomla folder
php bin/joomla list:plugins

# Or in Control Panel
Extensions > Plugins > Search plugin > Verify status
```
