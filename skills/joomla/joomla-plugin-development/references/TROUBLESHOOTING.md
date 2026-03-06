# Guía de Troubleshooting: Plugins Joomla 5/6

## Problemas de Instalación

### El plugin no aparece en la lista de extensiones

**Síntomas:**
- El plugin está en la carpeta correcta pero no aparece en Panel Control > Extensiones > Plugins
- La instalación no muestra errores

**Soluciones:**

1. **Verificar manifest.xml**
   - Asegurar que está en la raíz de la carpeta del plugin
   - Validar que el XML es válido (sin caracteres especiales o acentos sin encoding)
   - Verificar que tiene la declaración XML: `<?xml version="1.0" encoding="utf-8"?>`

2. **Verificar permisos**
   ```bash
   chmod 755 plugins/system/myplugin
   chmod 644 plugins/system/myplugin/manifest.xml
   ```

3. **Limpiar cache**
   - Panel Control > Sistema > Cache > Vaciar Cache
   - O eliminar: `administrator/cache/autoload_psr4.php`

4. **Verificar elemento type**
   ```xml
   <!-- Correcto -->
   <extension type="plugin" group="system">

   <!-- Incorrecto -->
   <extension type="plg" group="system">
   <plugin type="system">
   ```

### Error "Fatal error: Class not found"

**Síntomas:**
- Error al habilitar el plugin
- Mensaje como "Class 'MyCompany\Plugin\System\Myexample\Extension' not found"

**Soluciones:**

1. **Verificar namespace en manifest.xml**
   ```xml
   <!-- DEBE coincidir exactamente con la clase -->
   <namespace path="src">MyCompany\Plugin\System\Myexample</namespace>
   ```

2. **Verificar namespace en services/provider.php**
   ```php
   // DEBE coincidir con manifest
   namespace MyCompany\Plugin\System\Myexample;
   ```

3. **Verificar ruta en manifest.xml**
   ```xml
   <!-- El atributo plugin DEBE coincidir con el nombre del plugin -->
   <folder plugin="myexample">services</folder>

   <!-- Si el plugin se llama "plg_system_myexample", esto es correcto -->
   <!-- Si se llama "plg_system_myexample2", debe ser plugin="myexample2" -->
   ```

4. **Regenerar cache PSR-4**
   ```bash
   rm administrator/cache/autoload_psr4.php
   ```
   Luego acceder a la página y dejar que Joomla lo regenere.

## Problemas de Eventos

### El evento no se dispara

**Síntomas:**
- El código en onContentPrepare no se ejecuta
- El evento no se llama en absoluto

**Soluciones:**

1. **Verificar getSubscribedEvents()**
   ```php
   // Correcto
   public static function getSubscribedEvents(): array
   {
       return [
           'onContentPrepare' => 'onContentPrepare',
       ];
   }

   // Incorrecto: falta return type
   public static function getSubscribedEvents()
   {
       return ['onContentPrepare' => 'onContentPrepare'];
   }
   ```

2. **Verificar que el plugin está habilitado**
   - Panel Control > Extensiones > Plugins
   - El estado debe ser verde (habilitado)

3. **Verificar que el método existe**
   ```php
   // Debe existir este método exacto
   public function onContentPrepare(ContentPrepareEvent $event)
   {
       // ...
   }
   ```

4. **Verificar allowLegacyListeners**
   ```php
   // Si está TRUE, intenta buscar métodos antiguos
   protected $allowLegacyListeners = false; // Correcto para Joomla 5/6
   ```

5. **Revisar logs**
   ```bash
   tail -f logs/joomla.log
   ```
   Buscar mensajes de error relacionados con el evento

### El evento se dispara pero con argumentos inválidos

**Síntomas:**
- El evento se dispara pero los argumentos son null o vacíos
- Error al acceder a propiedades del objeto del evento

**Soluciones:**

1. **Usar Event Classes correctas**
   ```php
   // Incorrecto: sin type hint
   public function onContentPrepare($event)
   {
       $article = $event->getArgument('0'); // Puede ser null
   }

   // Correcto: con type hint
   use Joomla\CMS\Event\Content\ContentPrepareEvent;

   public function onContentPrepare(ContentPrepareEvent $event)
   {
       $article = $event->getArgument('0'); // Acceso seguro
   }
   ```

2. **Verificar índices correctos**
   ```php
   // Los índices pueden variar por evento
   // onContentPrepare: [0] = article, [1] = params
   $article = $event->getArgument('0');
   $params = $event->getArgument('1');

   // Usar métodos específicos si existen
   $article = $event->getArticle(); // Más seguro
   ```

3. **Validar argumentos antes de usar**
   ```php
   public function onContentPrepare(ContentPrepareEvent $event)
   {
       $article = $event->getArgument('0');

       // Validar SIEMPRE
       if (!$article || !property_exists($article, 'text')) {
           return;
       }

       // Ahora es seguro usar
       $article->text = $this->process($article->text);
   }
   ```

## Problemas de Configuración

### Los parámetros no se guardan

**Síntomas:**
- Los campos de configuración no aparecen
- Los parámetros se resetean al deshabilitar/habilitar

**Soluciones:**

1. **Verificar sintaxis en manifest.xml**
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

2. **Verificar tipos de campos válidos**
   - text, textarea, checkbox, radio, select, list, etc.
   - Usar `type="checkbox"` no `type="check"`

3. **Acceder a parámetros correctamente**
   ```php
   // Correcto
   $value = $this->params->get('param_name', 'default_value');

   // Incorrecto
   $value = $this->params['param_name'];
   $value = $this->params->param_name;
   ```

### Las traducciones no se cargan

**Síntomas:**
- Los labels muestran "PLG_MYPLUGIN_LABEL" en lugar del texto
- Los idiomas no se aplican correctamente

**Soluciones:**

1. **Verificar estructura de directorios**
   ```
   language/
   └── en-GB/
       ├── plg_system_myplugin.ini
       └── plg_system_myplugin.sys.ini
   ```

2. **Verificar nombre exacto del archivo**
   ```
   Correcto: plg_system_myplugin.ini
   Incorrecto: plg_system_myplugin.php
   Incorrecto: plg_system_my_plugin.ini (con guion bajo en nombre)
   ```

3. **Habilitar autoload de idiomas**
   ```php
   class Extension extends CMSPlugin
   {
       protected $autoloadLanguage = true; // DEBE ser true
   }
   ```

4. **Verificar que el prefix de strings es correcto**
   ```ini
   <!-- En manifest.xml y archivos .ini, usar PLG_TIPO_NOMBRE -->
   PLG_SYSTEM_MYPLUGIN="My Plugin"
   PLG_SYSTEM_MYPLUGIN_LABEL="Label"

   <!-- Si el plugin se llama "plg_content_example", usar -->
   PLG_CONTENT_EXAMPLE="Example Plugin"
   PLG_CONTENT_EXAMPLE_LABEL="Label"
   ```

## Problemas de Rendimiento

### El sitio se vuelve lento

**Síntomas:**
- Las páginas cargan más lentamente después de habilitar el plugin
- Alto uso de CPU/Memoria

**Soluciones:**

1. **Limitar eventos suscritos**
   ```php
   // Malo: suscribirse a muchos eventos
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

   // Mejor: solo eventos necesarios
   public static function getSubscribedEvents(): array
   {
       return [
           'onContentPrepare' => 'onContentPrepare',
       ];
   }
   ```

2. **Usar cache**
   ```php
   use Joomla\CMS\Cache\CacheFactory;

   $cache = CacheFactory::getCache('_system');
   $key = 'plugin_result_' . $id;

   if ($result = $cache->get($key)) {
       return $result;
   }

   // Procesamiento costoso
   $result = $this->expensiveOperation();

   // Guardar por 1 hora (3600 segundos)
   $cache->store($result, $key, '_system', 3600);
   ```

3. **Evitar consultas innecesarias**
   ```php
   // Malo: consulta en cada evento
   public function onContentPrepare($event)
   {
       $db = Factory::getDbo();
       $query = $db->getQuery(true)->select('*')->from('#__articles');
       $db->setQuery($query);
       // Esto se ejecuta para CADA artículo
   }

   // Mejor: cachear o limitar
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

## Problemas de Seguridad

### Acceso denegado o errores de permisos

**Síntomas:**
- "Access Denied" aunque el usuario tiene permisos
- El plugin intenta hacer cosas que no puede

**Soluciones:**

1. **Verificar permisos de usuario**
   ```php
   use Joomla\CMS\Factory;

   $user = Factory::getUser();

   if (!$user->authorise('core.edit', 'com_content')) {
       // Usuario no tiene permisos
       return;
   }
   ```

2. **Validar entrada siempre**
   ```php
   use Joomla\CMS\Filter\InputFilter;

   $filter = InputFilter::getInstance();

   // Validar por tipo
   $text = $filter->clean($_GET['text'], 'STRING');
   $number = $filter->clean($_GET['number'], 'INT');
   $html = $filter->clean($_GET['html'], 'HTML');
   ```

3. **Escapar salida**
   ```php
   use Joomla\CMS\HTML\HTMLHelper;

   // Para texto plano
   echo HTMLHelper::_('common.escape', $userContent);

   // Para HTML
   echo htmlspecialchars($userContent, ENT_QUOTES, 'UTF-8');
   ```

### Consultas SQL inseguras

**Síntomas:**
- Errors SQL inesperados
- Comportamiento extraño en la base de datos
- Posible SQL injection

**Soluciones:**

1. **Usar query binding**
   ```php
   // Correcto
   $db = Factory::getDbo();
   $query = $db->getQuery(true)
       ->select('*')
       ->from($db->quoteName('#__articles'))
       ->where($db->quoteName('id') . ' = :id')
       ->bind(':id', $articleId);

   $db->setQuery($query);
   $result = $db->loadObject();

   // Incorrecto
   $query = $db->getQuery(true)
       ->select('*')
       ->from($db->quoteName('#__articles'))
       ->where('id = ' . $articleId); // SQL INJECTION!
   ```

2. **Usar quoteName para identificadores**
   ```php
   // Correcto
   $query = $db->getQuery(true)
       ->select($db->quoteName('title'))
       ->from($db->quoteName('#__articles'));

   // Incorrecto
   $query = $db->getQuery(true)
       ->select('title')
       ->from('#__articles');
   ```

## Problemas de Compatibilidad

### Error "Undefined class 'SubscriberInterface'"

**Síntomas:**
- Error durante instalación
- Versión de Joomla demasiado antigua

**Soluciones:**

1. **Verificar versión mínima**
   ```xml
   <!-- En manifest.xml -->
   <targetPlatform version="5.0" />

   <!-- Significa que requiere Joomla 5.0 o superior -->
   ```

2. **Para Joomla 4.4 o superior:**
   ```php
   use Joomla\Event\SubscriberInterface;
   ```

3. **Para versiones anteriores, usar patrón antiguo:**
   ```php
   class Extension extends CMSPlugin
   {
       public function onContentPrepare($context, &$article, &$params, $page = 0)
       {
           // Patrón antiguo para Joomla 3-4
       }
   }
   ```

### Event Classes no disponibles

**Síntomas:**
- "Undefined class 'ContentPrepareEvent'"
- Event Classes solo disponibles en Joomla 5.2+

**Soluciones:**

1. **Verificar versión de Joomla**
   - Event Classes disponibles a partir de Joomla 5.2
   - Para versiones anteriores, usar argumentos por índice

2. **Fallback para versiones anteriores**
   ```php
   public function onContentPrepare($event)
   {
       // Soporta Event Classes (Joomla 5.2+)
       if ($event instanceof \Joomla\CMS\Event\EventInterface) {
           $article = $event->getArgument('0');
       } else {
           // Fallback para versiones antiguas
           $article = func_get_arg(0);
       }
   }
   ```

## Debugging Efectivo

### Habilitar logging

```php
use Joomla\CMS\Factory;

$app = Factory::getApplication();
$logger = $app->getLogger();

// Log info
$logger->info('Message', ['category' => 'plugin']);

// Log error
$logger->error('Error message', ['exception' => $e]);

// Revisar logs
tail -f logs/joomla.log
```

### Usar Xdebug

1. Instalar Xdebug en el servidor
2. Configurar IDE (VS Code, PhpStorm)
3. Añadir breakpoints en el código del plugin
4. Navegar por el sitio para activar puntos de ruptura

### Verificar estado del plugin

```bash
# En la carpeta de Joomla
php bin/joomla list:plugins

# O en Panel Control
Extensions > Plugins > Buscar plugin > Verificar estado
```
