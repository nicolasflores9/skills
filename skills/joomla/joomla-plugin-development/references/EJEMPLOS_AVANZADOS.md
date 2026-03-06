# Advanced Examples of Joomla 5/6 Plugins

## 1. System Plugin with Logger and Persistence

### Case: Event Logging Plugin

This plugin logs all user events that occur on the site.

**Structure:**
```
plg_system_eventlogger/
├── manifest.xml
├── services/
│   └── provider.php
├── src/
│   ├── Extension/
│   │   └── Eventlogger.php
│   └── Helper/
│       └── LoggerHelper.php
└── language/
    └── en-GB/
        ├── plg_system_eventlogger.ini
        └── plg_system_eventlogger.sys.ini
```

**manifest.xml:**
```xml
<?xml version="1.0" encoding="utf-8"?>
<extension type="plugin" group="system">
    <name>PLG_SYSTEM_EVENTLOGGER</name>
    <author>Your Name</author>
    <creationDate>2025-03-06</creationDate>
    <copyright>Copyright 2025</copyright>
    <license>GNU General Public License version 2 or later</license>
    <version>1.0.0</version>
    <description>PLG_SYSTEM_EVENTLOGGER_DESCRIPTION</description>
    <targetPlatform version="5.0" />

    <namespace path="src">MyCompany\Plugin\System\Eventlogger</namespace>

    <files>
        <file>manifest.xml</file>
        <folder plugin="eventlogger">services</folder>
        <folder>src</folder>
        <folder>language</folder>
    </files>

    <config>
        <fields name="params">
            <fieldset name="basic">
                <field
                    name="log_logins"
                    type="checkbox"
                    label="Log User Logins"
                    default="1"
                />
                <field
                    name="log_logouts"
                    type="checkbox"
                    label="Log User Logouts"
                    default="1"
                />
                <field
                    name="log_save_events"
                    type="checkbox"
                    label="Log Content Save Events"
                    default="0"
                />
            </fieldset>
        </fields>
    </config>
</extension>
```

**src/Extension/Eventlogger.php:**
```php
<?php
namespace MyCompany\Plugin\System\Eventlogger;

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Event\SubscriberInterface;
use Joomla\CMS\Event\User\UserLoginEvent;
use Joomla\CMS\Event\User\UserLogoutEvent;
use Joomla\CMS\Event\Content\ContentAfterSaveEvent;
use MyCompany\Plugin\System\Eventlogger\Helper\LoggerHelper;

class Extension extends CMSPlugin implements SubscriberInterface
{
    protected $autoloadLanguage = true;
    protected $allowLegacyListeners = false;

    public static function getSubscribedEvents(): array
    {
        return [
            'onUserLogin' => 'onUserLogin',
            'onUserLogout' => 'onUserLogout',
            'onContentAfterSave' => 'onContentAfterSave',
        ];
    }

    public function onUserLogin(UserLoginEvent $event)
    {
        if (!$this->params->get('log_logins')) {
            return;
        }

        $response = $event->getArgument('response');
        $logger = new LoggerHelper();

        $logger->write(
            'USER_LOGIN',
            'User ' . $response['username'] . ' logged in'
        );
    }

    public function onUserLogout(UserLogoutEvent $event)
    {
        if (!$this->params->get('log_logouts')) {
            return;
        }

        $logger = new LoggerHelper();
        $logger->write('USER_LOGOUT', 'A user logged out');
    }

    public function onContentAfterSave(ContentAfterSaveEvent $event)
    {
        if (!$this->params->get('log_save_events')) {
            return;
        }

        $context = $event->getArgument('0');
        $article = $event->getArgument('1');
        $isNew = $event->getArgument('2');

        $logger = new LoggerHelper();
        $action = $isNew ? 'CREATED' : 'MODIFIED';

        $logger->write(
            'CONTENT_SAVE',
            'A user ' . $action . ' article: ' . $article->title
        );
    }
}
```

**src/Helper/LoggerHelper.php:**
```php
<?php
namespace MyCompany\Plugin\System\Eventlogger\Helper;

use Joomla\CMS\Factory;

class LoggerHelper
{
    /**
     * Write to the event log file
     */
    public function write($eventType, $message)
    {
        $app = Factory::getApplication();
        $logFile = JPATH_ADMINISTRATOR . '/logs/eventlogger.log';

        $timestamp = date('Y-m-d H:i:s');
        $user = Factory::getUser();
        $userId = $user->id ?: 'GUEST';

        $logEntry = sprintf(
            "[%s] [USER: %d] [EVENT: %s] %s\n",
            $timestamp,
            $userId,
            $eventType,
            $message
        );

        file_put_contents($logFile, $logEntry, FILE_APPEND);
    }
}
```

## 2. User Plugin with Email Sending

**src/Extension/Useremail.php:**
```php
<?php
namespace MyCompany\Plugin\User\Useremail;

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Event\SubscriberInterface;
use Joomla\CMS\Event\User\UserAfterSaveEvent;
use Joomla\CMS\Mail\Mail;
use Joomla\CMS\Factory;

class Extension extends CMSPlugin implements SubscriberInterface
{
    protected $autoloadLanguage = true;
    protected $allowLegacyListeners = false;

    public static function getSubscribedEvents(): array
    {
        return [
            'onUserAfterSave' => 'onUserAfterSave',
        ];
    }

    public function onUserAfterSave(UserAfterSaveEvent $event)
    {
        $user = $event->getArgument('0');
        $isNew = $event->getArgument('1');

        if (!$isNew) {
            return;
        }

        // Send email to the new user
        $this->sendWelcomeEmail($user);
    }

    private function sendWelcomeEmail($user)
    {
        try {
            $app = Factory::getApplication();
            $config = Factory::getConfig();

            $mail = new Mail();
            $mail->setSubject($this->params->get('welcome_subject', 'Welcome'));
            $mail->addRecipient($user->email);
            $mail->setFrom([
                $config->get('mailfrom') => $config->get('fromname')
            ]);

            $body = $this->params->get('welcome_message', 'Hello {NAME}');
            $body = str_replace('{NAME}', $user->name, $body);

            $mail->setBody($body);
            $mail->isHtml(true);
            $mail->Send();

        } catch (\Exception $e) {
            $app->enqueueMessage(
                'Error sending welcome email: ' . $e->getMessage(),
                'error'
            );
        }
    }
}
```

## 3. Plugin with Advanced Dependency Injection

**services/provider.php:**
```php
<?php
namespace MyCompany\Plugin\Content\Advanced;

use Joomla\CMS\Extension\PluginInterface;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Database\DatabaseInterface;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        // Register custom service
        $container->set(
            'plugin.content.advanced.cache',
            function (Container $c) {
                return new \Joomla\CMS\Cache\CacheFactory::getCache(
                    '_system'
                );
            }
        );

        // Register the plugin with injected services
        $container->set(
            PluginInterface::class,
            function (Container $c) {
                $plugin = new Extension(
                    $c->get('dispatcher'),
                    (array) PluginHelper::getPlugin('content', 'advanced')
                );

                // Inject the full container
                $plugin->setContainer($c);

                return $plugin;
            }
        );
    }
}
```

**src/Extension/Advanced.php:**
```php
<?php
namespace MyCompany\Plugin\Content\Advanced;

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Event\SubscriberInterface;
use Joomla\CMS\Event\Content\ContentPrepareEvent;
use Joomla\DI\Traits\ContainerAwareTrait;
use Joomla\Database\DatabaseInterface;

class Extension extends CMSPlugin implements SubscriberInterface
{
    use ContainerAwareTrait;

    protected $autoloadLanguage = true;
    protected $allowLegacyListeners = false;

    public static function getSubscribedEvents(): array
    {
        return [
            'onContentPrepare' => ['onContentPrepare', 5],
        ];
    }

    public function onContentPrepare(ContentPrepareEvent $event)
    {
        $article = $event->getArgument('0');

        // Access services from the container
        $db = $this->getContainer()->get(DatabaseInterface::class);
        $cache = $this->getContainer()->get('plugin.content.advanced.cache');

        // Use services
        $query = $db->getQuery(true)
            ->select('COUNT(*)')
            ->from($db->quoteName('#__articles'));

        $db->setQuery($query);
        $count = $db->loadResult();

        // Process
        $article->text = '<!-- Articles: ' . $count . ' -->' . $article->text;
    }
}
```

## 4. Content Plugin with Typed Event Classes

**src/Extension/ContentprocessorPlus.php:**
```php
<?php
namespace MyCompany\Plugin\Content\ContentprocessorPlus;

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Event\SubscriberInterface;
use Joomla\CMS\Event\Content\ContentPrepareEvent;
use Joomla\CMS\Event\Content\ContentAfterTitleEvent;

class Extension extends CMSPlugin implements SubscriberInterface
{
    protected $autoloadLanguage = true;
    protected $allowLegacyListeners = false;

    public static function getSubscribedEvents(): array
    {
        return [
            'onContentPrepare' => ['onContentPrepare', 0],
            'onContentAfterTitle' => ['onContentAfterTitle', 10],
        ];
    }

    /**
     * Process main content
     */
    public function onContentPrepare(ContentPrepareEvent $event): void
    {
        $article = $event->getArgument('0');

        if (!$article || !property_exists($article, 'text')) {
            return;
        }

        // Apply security filters
        $article->text = $this->sanitizeContent($article->text);

        // Update the event with processed content
        $event->setArgument('0', $article);
    }

    /**
     * Add content after the title
     */
    public function onContentAfterTitle(ContentAfterTitleEvent $event): void
    {
        $context = $event->getArgument('0');
        $article = $event->getArgument('1');

        // Only for published articles
        if ($article->state != 1) {
            return;
        }

        $output = '<div class="article-meta">';
        $output .= 'By: <span class="author">' . $article->author . '</span>';
        $output .= '</div>';

        $event->setArgument('2', $output);
    }

    private function sanitizeContent($content)
    {
        // Apply custom sanitization
        $content = strip_tags($content, '<p><br><strong><em><a><ul><li>');
        return $content;
    }
}
```

## 5. Plugin with Validation and Error Handling

**src/Extension/Validating.php:**
```php
<?php
namespace MyCompany\Plugin\Content\Validating;

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Event\SubscriberInterface;
use Joomla\CMS\Event\Content\ContentBeforeSaveEvent;
use Joomla\CMS\Factory;

class Extension extends CMSPlugin implements SubscriberInterface
{
    protected $autoloadLanguage = true;
    protected $allowLegacyListeners = false;

    public static function getSubscribedEvents(): array
    {
        return [
            'onContentBeforeSave' => 'onContentBeforeSave',
        ];
    }

    public function onContentBeforeSave(ContentBeforeSaveEvent $event)
    {
        $context = $event->getArgument('0');
        $article = $event->getArgument('1');
        $isNew = $event->getArgument('2');

        // Validate that the title is not empty
        if (empty($article->title)) {
            $app = Factory::getApplication();
            $app->enqueueMessage(
                'The article title is required',
                'error'
            );
            return false;
        }

        // Validate minimum content length
        if (strlen($article->text) < 50) {
            $app = Factory::getApplication();
            $app->enqueueMessage(
                'The content must be at least 50 characters long',
                'error'
            );
            return false;
        }

        // Add automatic metadata
        if ($isNew) {
            $user = Factory::getUser();
            $article->created_by = $user->id;
            $article->created_by_alias = $user->username;
        }

        return true;
    }
}
```

## 6. System Plugin with Multiple Events and Priorities

**src/Extension/Multihandler.php:**
```php
<?php
namespace MyCompany\Plugin\System\Multihandler;

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Event\SubscriberInterface;
use Joomla\CMS\Event\System\AfterInitialiseEvent;
use Joomla\CMS\Event\System\AfterRouteEvent;
use Joomla\CMS\Event\System\AfterDispatchEvent;
use Joomla\CMS\Event\System\BeforeRenderEvent;
use Joomla\CMS\Event\System\AfterRenderEvent;

class Extension extends CMSPlugin implements SubscriberInterface
{
    protected $autoloadLanguage = true;
    protected $allowLegacyListeners = false;

    /**
     * Events with different priorities
     * Low priority (0) = executes first
     * High priority (10) = executes last
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'onAfterInitialise' => ['onAfterInitialise', 0],
            'onAfterRoute' => ['onAfterRoute', 5],
            'onAfterDispatch' => ['onAfterDispatch', 10],
            'onBeforeRender' => 'onBeforeRender',
            'onAfterRender' => 'onAfterRender',
        ];
    }

    public function onAfterInitialise(AfterInitialiseEvent $event): void
    {
        // Priority 0: executes first
        // Initialize global configuration
    }

    public function onAfterRoute(AfterRouteEvent $event): void
    {
        // Priority 5: executes in the middle
        // Process resolved route
    }

    public function onAfterDispatch(AfterDispatchEvent $event): void
    {
        // Priority 10: executes last
        // Post-process dispatch
    }

    public function onBeforeRender(BeforeRenderEvent $event): void
    {
        // No priority specified = normal
    }

    public function onAfterRender(AfterRenderEvent $event): void
    {
        // No priority specified = normal
    }
}
```
