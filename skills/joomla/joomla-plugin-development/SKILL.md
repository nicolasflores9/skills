---
name: joomla-plugin-development
description: Domina el desarrollo de plugins modernos en Joomla 5/6. Aprende a crear extensiones robustas usando SubscriberInterface, Event Classes, inyección de dependencias y PSR-4. Cubre manifest.xml, service providers, eventos del sistema/contenido/usuario, namespaces y mejores prácticas de seguridad. Incluye ejemplos completos desde plugins básicos hasta avanzados. Utiliza este contenido cuando necesites crear plugins Joomla, comprender el patrón de eventos, implementar SubscriberInterface, trabajar con Event Classes, configurar dependencias, resolver problemas de namespace, o seguir arquitectura moderna.
---

# Desarrollo de Plugins Joomla 5/6: Guía Completa

## 1. Introducción

Los plugins en Joomla 5/6 representan la evolución más moderna del framework. Este conjunto de conocimientos te proporciona todo lo necesario para crear extensiones robustas, eficientes y mantenibles siguiendo los patrones actuales.

### 1.1 Qué Aprenderás

- Arquitectura moderna de plugins Joomla 5/6
- Implementación de SubscriberInterface y getSubscribedEvents()
- Manejo de eventos del sistema, contenido y usuario
- Event Classes con type hints
- Inyección de dependencias
- PSR-4 namespaces y autoloading automático
- Mejores prácticas de seguridad y rendimiento
- Ejemplos prácticos y completos

### 1.2 Requisitos Previos

- Instalación funcional de Joomla 5 o 6
- Conocimiento intermedio de PHP
- Familiaridad con namespaces y composición
- Acceso de administrador a Joomla
- Editor de código (VS Code, PhpStorm, etc.)

## 2. Estructura Moderna de Plugins

### 2.1 Árbol de Directorios Estándar

Todo plugin moderno sigue esta estructura:

```
plg_system_myexample/
├── manifest.xml              # Configuración e instalación
├── services/
│   └── provider.php          # Inyección de dependencias
├── src/
│   ├── Extension/
│   │   └── MyExample.php     # Clase principal
│   ├── Event/                # (Opcional) Event classes personalizadas
│   └── Helper/               # (Opcional) Clases auxiliares
└── language/
    └── en-GB/
        ├── plg_system_myexample.ini      # Traducciones frontend
        └── plg_system_myexample.sys.ini  # Traducciones sistema
```

**Nomenclatura crítica:**
- Prefijo: `plg_` (siempre plugin)
- Grupo: `system`, `content`, `user`, etc.
- Nombre: minúsculas sin espacios
- Ejemplo: `plg_content_shortcodes` = plugin de contenido llamado "shortcodes"

### 2.2 Archivo manifest.xml

El manifest es el punto de entrada de instalación:

```xml
<?xml version="1.0" encoding="utf-8"?>
<extension type="plugin" group="system">
    <name>PLG_SYSTEM_MYEXAMPLE</name>
    <author>Tu Nombre</author>
    <creationDate>2025-03-06</creationDate>
    <copyright>Copyright 2025</copyright>
    <license>GNU General Public License version 2 or later</license>
    <version>1.0.0</version>
    <description>PLG_SYSTEM_MYEXAMPLE_DESCRIPTION</description>
    <targetPlatform version="5.0" />

    <!-- CRÍTICO: Define namespace PSR-4 -->
    <namespace path="src">MyCompany\Plugin\System\MyExample</namespace>

    <!-- Declaración de archivos -->
    <files>
        <file>manifest.xml</file>
        <folder plugin="myexample">services</folder>
        <folder>src</folder>
        <folder>language</folder>
    </files>

    <!-- Configuración de parámetros (opcional) -->
    <config>
        <fields name="params">
            <fieldset name="basic">
                <field
                    name="enabled"
                    type="checkbox"
                    label="Habilitar plugin"
                    default="1"
                />
            </fieldset>
        </fields>
    </config>
</extension>
```

**Elementos críticos:**
- `<namespace path="src">`: Define el prefijo namespace. DEBE coincidir exactamente en provider.php y Extension
- `<folder plugin="myexample">`: El atributo debe ser el nombre del plugin (coincide con manifest.xml)
- `type="plugin"` y `group="..."`: Identifica tipo y categoría

### 2.3 Service Provider (services/provider.php)

Este archivo registra tu plugin en el contenedor de inyección de dependencias:

```php
<?php
namespace MyCompany\Plugin\System\MyExample;

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
                $plugin = new Extension(
                    $c->get('dispatcher'),
                    (array) PluginHelper::getPlugin('system', 'myexample')
                );
                return $plugin;
            }
        );
    }
}
```

**Puntos clave:**
- El namespace DEBE coincidir exactamente con manifest.xml
- `PluginHelper::getPlugin()` obtiene la configuración del plugin
- El dispatcher es el gestor de eventos de Joomla
- Se registra la clase Extension como PluginInterface

## 3. Clase Extension: El Corazón del Plugin

La clase principal implementa `SubscriberInterface`:

```php
<?php
namespace MyCompany\Plugin\System\MyExample;

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Event\SubscriberInterface;
use Joomla\CMS\Event\System\AfterInitialiseEvent;

class Extension extends CMSPlugin implements SubscriberInterface
{
    protected $autoloadLanguage = true;
    protected $allowLegacyListeners = false;

    /**
     * Declara eventos suscritos
     * Este método estático es OBLIGATORIO
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'onAfterInitialise' => 'onAfterInitialise',
        ];
    }

    /**
     * Maneja el evento onAfterInitialise
     */
    public function onAfterInitialise(AfterInitialiseEvent $event)
    {
        // Tu lógica aquí
    }
}
```

**Propiedades importantes:**
- `$autoloadLanguage = true`: Carga automáticos los archivos .ini
- `$allowLegacyListeners = false`: Mejora rendimiento, desactiva búsqueda con Reflection
- Extiende `CMSPlugin` que proporciona `$params`, `$app`, etc.

## 4. SubscriberInterface y getSubscribedEvents()

### 4.1 Patrón Moderno vs Antiguo

**Antiguo (Joomla 3-4):**
```php
public function onContentPrepare($context, &$article, &$params, $page = 0)
{
    // Lógica
}
```

**Moderno (Joomla 5+):**
```php
public static function getSubscribedEvents(): array
{
    return ['onContentPrepare' => 'onContentPrepare'];
}

public function onContentPrepare(ContentPrepareEvent $event)
{
    $article = $event->getArgument('0');
    // Lógica con type hints
}
```

### 4.2 Formatos de getSubscribedEvents()

```php
// Formato básico
public static function getSubscribedEvents(): array
{
    return [
        'onContentPrepare' => 'onContentPrepare',
        'onContentAfterTitle' => 'onContentAfterTitle',
    ];
}

// Con prioridades (menor número = ejecuta primero)
public static function getSubscribedEvents(): array
{
    return [
        'onContentPrepare' => ['onContentPrepare', 5],
        'onContentAfterTitle' => 'onContentAfterTitle',
    ];
}

// Múltiples listeners para un evento
public static function getSubscribedEvents(): array
{
    return [
        'onContentPrepare' => [
            ['primaryHandler', 0],
            ['secondaryHandler', 10],
        ],
    ];
}
```

## 5. Tipos de Eventos Principales

### 5.1 Eventos del Sistema

Se disparan en cada carga de página:

| Evento | Cuándo se dispara |
|--------|-------------------|
| `onAfterInitialise` | Después de inicializar Joomla |
| `onAfterRoute` | Después de resolver la ruta |
| `onAfterDispatch` | Después de ejecutar el componente |
| `onBeforeRender` | Antes de renderizar la página |
| `onBeforeCompileHead` | Antes de compilar etiquetas head |
| `onAfterRender` | Después de renderizar |

### 5.2 Eventos de Contenido

Se disparan en ciclo de lectura/escritura de artículos:

| Evento | Descripción |
|--------|-------------|
| `onContentPrepare` | Antes de mostrar artículos |
| `onContentAfterTitle` | Después del título |
| `onContentBeforeSave` | Validación previa |
| `onContentAfterSave` | Post-procesamiento |
| `onContentBeforeDelete` | Limpieza previa |
| `onContentAfterDelete` | Limpieza posterior |

### 5.3 Eventos de Usuario

Gestión de usuarios:

| Evento | Descripción |
|--------|-------------|
| `onUserBeforeSave` | Antes de guardar usuario |
| `onUserAfterSave` | Después de guardar usuario |
| `onUserLogin` | Después de login exitoso |
| `onUserLogout` | Después de logout |

## 6. Event Classes (Joomla 5.2+)

Las Event Classes proporcionan type safety y mejor estructura:

```php
// Antiguo
public function onContentPrepare($context, &$article, &$params, $page = 0)
{
    $text = $article->text;
}

// Moderno con Event Class
use Joomla\CMS\Event\Content\ContentPrepareEvent;

public function onContentPrepare(ContentPrepareEvent $event)
{
    // Acceder por índice
    $article = $event->getArgument('0');

    // O usar métodos específicos (si existen)
    $article = $event->getArticle();

    // Modificar
    $event->setArgument('0', $modifiedArticle);
}
```

**Ubicación de Event Classes:**
- `\Joomla\CMS\Event\Content\*` para eventos de contenido
- `\Joomla\CMS\Event\System\*` para eventos del sistema
- `\Joomla\CMS\Event\User\*` para eventos de usuario

## 7. Inyección de Dependencias

### 7.1 Acceso a Servicios Básicos

```php
<?php
namespace MyCompany\Plugin\Content\Example;

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Event\SubscriberInterface;
use Joomla\DI\Traits\ContainerAwareTrait;
use Joomla\Database\DatabaseInterface;

class Extension extends CMSPlugin implements SubscriberInterface
{
    use ContainerAwareTrait;

    public static function getSubscribedEvents(): array
    {
        return ['onContentPrepare' => 'onContentPrepare'];
    }

    public function onContentPrepare($event)
    {
        // Acceder al contenedor
        $container = $this->getContainer();

        // Obtener la base de datos
        $db = $container->get(DatabaseInterface::class);

        // Ejecutar consulta
        $query = $db->getQuery(true)
            ->select('*')
            ->from($db->quoteName('#__articles'));

        $db->setQuery($query);
        $results = $db->loadObjectList();
    }
}
```

### 7.2 En Service Provider

```php
<?php
namespace MyCompany\Plugin\Content\Example;

use Joomla\CMS\Extension\PluginInterface;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Database\DatabaseInterface;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $container->set(
            PluginInterface::class,
            function (Container $c) {
                $plugin = new Extension(
                    $c->get('dispatcher'),
                    (array) PluginHelper::getPlugin('content', 'example')
                );

                // Inyectar el contenedor
                $plugin->setContainer($c);

                return $plugin;
            }
        );
    }
}
```

## 8. Tu Primer Plugin: "Hello World"

### 8.1 Estructura Mínima

```
plg_system_helloworld/
├── manifest.xml
├── services/
│   └── provider.php
├── src/
│   └── Extension/
│       └── Helloworld.php
└── language/
    └── en-GB/
        ├── plg_system_helloworld.ini
        └── plg_system_helloworld.sys.ini
```

### 8.2 manifest.xml

```xml
<?xml version="1.0" encoding="utf-8"?>
<extension type="plugin" group="system">
    <name>PLG_SYSTEM_HELLOWORLD</name>
    <author>Tu Nombre</author>
    <creationDate>2025-03-06</creationDate>
    <copyright>Copyright 2025</copyright>
    <license>GNU General Public License version 2 or later</license>
    <version>1.0.0</version>
    <description>PLG_SYSTEM_HELLOWORLD_DESCRIPTION</description>
    <targetPlatform version="5.0" />

    <namespace path="src">MyCompany\Plugin\System\Helloworld</namespace>

    <files>
        <file>manifest.xml</file>
        <folder plugin="helloworld">services</folder>
        <folder>src</folder>
        <folder>language</folder>
    </files>
</extension>
```

### 8.3 services/provider.php

```php
<?php
namespace MyCompany\Plugin\System\Helloworld;

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
                $plugin = new Extension(
                    $c->get('dispatcher'),
                    (array) PluginHelper::getPlugin('system', 'helloworld')
                );
                return $plugin;
            }
        );
    }
}
```

### 8.4 src/Extension/Helloworld.php

```php
<?php
namespace MyCompany\Plugin\System\Helloworld;

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
        $app = $this->getApplication();
        $app->getLogger()->info('Hello World! El plugin está funcionando.');
    }
}
```

### 8.5 Archivos de Idioma

**language/en-GB/plg_system_helloworld.ini:**
```ini
PLG_SYSTEM_HELLOWORLD="Hello World Plugin"
```

**language/en-GB/plg_system_helloworld.sys.ini:**
```ini
PLG_SYSTEM_HELLOWORLD="Hello World Plugin"
PLG_SYSTEM_HELLOWORLD_DESCRIPTION="Un plugin de ejemplo que muestra 'Hello World'"
```

### 8.6 Instalación

1. Crear carpeta: `plugins/system/helloworld`
2. Copiar todos los archivos
3. Ir a Panel de Control > Extensiones > Plugins
4. Buscar "Hello World Plugin"
5. Habilitarlo
6. Verificar en logs: `logs/joomla.log`

## 9. Plugin Avanzado: Shortcodes de Contenido

### 9.1 Manifest.xml con Parámetros

```xml
<?xml version="1.0" encoding="utf-8"?>
<extension type="plugin" group="content" method="upgrade">
    <name>PLG_CONTENT_SHORTCODES</name>
    <author>Tu Nombre</author>
    <creationDate>2025-03-06</creationDate>
    <copyright>Copyright 2025</copyright>
    <license>GNU General Public License version 2 or later</license>
    <version>1.0.0</version>
    <description>PLG_CONTENT_SHORTCODES_DESCRIPTION</description>
    <targetPlatform version="5.0" />

    <namespace path="src">MyCompany\Plugin\Content\Shortcodes</namespace>

    <files>
        <file>manifest.xml</file>
        <folder plugin="shortcodes">services</folder>
        <folder>src</folder>
        <folder>language</folder>
    </files>

    <config>
        <fields name="params">
            <fieldset name="basic">
                <field
                    name="process_shortcodes"
                    type="checkbox"
                    label="PLG_CONTENT_SHORTCODES_ENABLED"
                    default="1"
                />
            </fieldset>
        </fields>
    </config>
</extension>
```

### 9.2 Extension Class Completa

```php
<?php
namespace MyCompany\Plugin\Content\Shortcodes;

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Event\SubscriberInterface;
use Joomla\CMS\Event\Content\ContentPrepareEvent;
use Joomla\CMS\Factory;

class Extension extends CMSPlugin implements SubscriberInterface
{
    protected $autoloadLanguage = true;
    protected $allowLegacyListeners = false;

    public static function getSubscribedEvents(): array
    {
        return [
            'onContentPrepare' => 'onContentPrepare',
        ];
    }

    public function onContentPrepare(ContentPrepareEvent $event)
    {
        // Verificar si está habilitado
        if (!$this->params->get('process_shortcodes', true)) {
            return;
        }

        $article = $event->getArgument('0');

        if (!isset($article) || !isset($article->text)) {
            return;
        }

        // Procesar shortcodes
        $article->text = $this->processShortcodes($article->text);
    }

    protected function processShortcodes($text)
    {
        $config = Factory::getConfig();

        // Reemplazar {sitename}
        $text = str_replace(
            '{sitename}',
            $config->get('sitename'),
            $text
        );

        // Reemplazar {siteurl}
        $text = str_replace(
            '{siteurl}',
            $config->get('live_site'),
            $text
        );

        // Reemplazar {year}
        $text = str_replace(
            '{year}',
            date('Y'),
            $text
        );

        return $text;
    }
}
```

### 9.3 Service Provider

```php
<?php
namespace MyCompany\Plugin\Content\Shortcodes;

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
                $plugin = new Extension(
                    $c->get('dispatcher'),
                    (array) PluginHelper::getPlugin('content', 'shortcodes')
                );
                return $plugin;
            }
        );
    }
}
```

### 9.4 Archivos de Idioma

**language/en-GB/plg_content_shortcodes.ini:**
```ini
PLG_CONTENT_SHORTCODES="Content Shortcodes"
PLG_CONTENT_SHORTCODES_ENABLED="Process Shortcodes"
```

**language/en-GB/plg_content_shortcodes.sys.ini:**
```ini
PLG_CONTENT_SHORTCODES="Content Shortcodes"
PLG_CONTENT_SHORTCODES_DESCRIPTION="Replace shortcodes like {sitename} with site configuration values"
```

## 10. Mejores Prácticas

### 10.1 Seguridad

```php
// SIEMPRE validar entrada
use Joomla\CMS\Filter\InputFilter;

$filter = InputFilter::getInstance();
$safe_input = $filter->clean($_GET['data'], 'STRING');

// SIEMPRE escapar salida
use Joomla\CMS\HTML\HTMLHelper;

echo HTMLHelper::_('common.escape', $user_content);

// Verificar permisos
$user = Factory::getUser();
if (!$user->authorise('core.manage', 'com_example')) {
    return;
}
```

### 10.2 Rendimiento

```php
// Usar cache
$cache = Factory::getCache('_system');
$key = 'plugin_example_data_' . $article_id;

if ($data = $cache->get($key)) {
    return $data;
}

// Procesamiento
$data = $this->expensiveOperation();

// Guardar en cache (3600 segundos = 1 hora)
$cache->store($data, $key, '_system', 3600);

// Ser selectivo con eventos - NO suscribirse a todos
public static function getSubscribedEvents(): array
{
    return [
        'onContentPrepare' => 'onContentPrepare',
        // NO: 'onAfterRender', 'onBeforeRender', etc.
    ];
}
```

### 10.3 Namespace y PSR-4

**CORRECTO:**
- manifest.xml: `<namespace path="src">MyCompany\Plugin\Content\Shortcodes</namespace>`
- services/provider.php: `namespace MyCompany\Plugin\Content\Shortcodes;`
- src/Extension/Shortcodes.php: `namespace MyCompany\Plugin\Content\Shortcodes;`

**INCORRECTO:**
- No coincidir en los namespaces
- No incluir `path="src"` en manifest
- Usar paths incorrectos en archivos

## 11. Troubleshooting Común

### 11.1 El plugin no aparece en la lista

**Soluciones:**
1. Verificar que manifest.xml está en la raíz de la carpeta del plugin
2. Revisar que el XML es válido (sin caracteres especiales)
3. Limpiar cache: Panel Control > Sistema > Cache > Vaciar Cache
4. Verificar permisos de carpeta (755)

### 11.2 El evento no se dispara

**Soluciones:**
1. Verificar que `getSubscribedEvents()` declara correctamente el evento
2. Asegurar que el plugin está habilitado
3. Revisar que `$allowLegacyListeners = false`
4. Verificar logs en `logs/joomla.log`

### 11.3 Error de namespace

**Soluciones:**
1. Coincidir EXACTAMENTE namespace en manifest, provider y Extension
2. Usar punto y coma al final de `namespace`
3. Verificar ruta en `<folder plugin="pluginname">services</folder>`
4. Limpiar cache de autoload: `administrator/cache/autoload_psr4.php`

## 12. Verificación de Instalación

Después de instalar un plugin, verifica:

1. Panel Control > Extensiones > Plugins
2. Buscar el plugin por nombre
3. Verificar que aparece en la lista
4. Habilitarlo (estado verde)
5. Consultar logs en `logs/joomla.log` para mensajes
6. Probar funcionalidad según el tipo de plugin

## 13. Recursos Adicionales

- [Documentación Oficial Joomla Manual](https://manual.joomla.org/)
- [Joomla Event Classes Documentation](https://docs.joomla.org/)
- [API Reference](https://api.joomla.org/)
- [Comunidad Joomla - Forum](https://forum.joomla.org/)
