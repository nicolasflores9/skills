# Troubleshooting y Casos Comunes

## Problemas de Instalación

### Error: "Class not found" al instalar

**Causa**: El namespace en manifest.xml no coincide con la estructura de carpetas.

**Solución**:
- Verificar que `namespace path="src"` esté en manifest.xml
- Confirmar que `Joomla\Module\[ModuleName]` coincida con las clases en src/
- La ruta debe ser: `src/Dispatcher/Dispatcher.php` → `Joomla\Module\[ModuleName]\Dispatcher\Dispatcher`

```xml
<!-- Correcto -->
<namespace path="src">Joomla\Module\MiModulo</namespace>

<!-- Incorrecto - falta path -->
<namespace>Joomla\Module\MiModulo</namespace>
```

### Error: "Module file not found"

**Causa**: El archivo principal `mod_[nombre].php` no existe o no está registrado.

**Solución**:
```xml
<!-- En manifest.xml debe estar: -->
<files>
    <filename module="mod_mimodulo">mod_mimodulo.php</filename>
</files>

<!-- El archivo debe existir en raíz del módulo -->
mod_mimodulo/
├── mod_mimodulo.php    ← AQUÍ
├── manifest.xml
└── ...
```

### Error: "Invalid manifest" durante instalación

**Causa**: Errores de sintaxis en XML.

**Solución**:
- Validar XML con validador online (xmlvalidation.com)
- Verificar caracteres especiales: `&` debe ser `&amp;`
- Cerrar todas las etiquetas
- UTF-8 encoding declarado: `<?xml version="1.0" encoding="UTF-8"?>`

## Problemas de Renderizado

### El módulo no aparece en el frontend

**Checklist**:
1. ¿Está el módulo publicado? (Extensiones → Módulos)
2. ¿Está asignado a una posición? (editar módulo)
3. ¿Está la posición en el template? (System Templates)
4. ¿Está habilitado para el menú actual?

**Debug**:
```php
<?php
// En tmpl/default.php
echo '<!-- Módulo cargado -->';
var_dump($displayData);
?>
```

### El template no se renderiza

**Causa**: Dispatcher no prepara datos para el template.

**Solución**:
```php
<?php
namespace Joomla\Module\Mimodulo\Dispatcher;

class Dispatcher extends AbstractModuleDispatcher
{
    protected function getLayoutData(): array
    {
        $data = parent::getLayoutData();
        // IMPORTANTE: agregar datos aquí
        $data['items'] = $this->helper->getItems();
        return $data;
    }
}
```

### Error: "Undefined variable" en template

**Causa**: Variable no viene desde getLayoutData().

**Solución**:
- Todas las variables deben estar en `$displayData`
- Acceder como: `$displayData['key']`
- NO usar `$variable` directamente

```php
<?php
// INCORRECTO
echo $items;  // undefined

// CORRECTO
echo $displayData['items'];
?>
```

## Problemas de Base de Datos

### Query no devuelve resultados

**Debug**:
```php
<?php
public function getItems($limit = 10)
{
    $query = $this->db->getQuery(true)
        ->select('*')
        ->from($this->db->quoteName('#__articles'))
        ->where($this->db->quoteName('state') . ' = 1')
        ->setLimit($limit);

    // Log de la query
    \Joomla\CMS\Log\Log::add(
        'SQL: ' . $query->__toString(),
        \Joomla\CMS\Log\Log::DEBUG,
        'mod_mimodulo'
    );

    return $this->db->setQuery($query)->loadObjectList();
}
?>
```

### Error SQL con caracteres especiales

**Causa**: No usar `quoteName()` en identificadores.

**Solución**:
```php
<?php
// INCORRECTO - falla con caracteres especiales
->where('state = 1')

// CORRECTO
->where($this->db->quoteName('state') . ' = 1')

// Con variables
->where($this->db->quoteName('title') . ' = ' . $this->db->quote($value))
?>
```

### La tabla personalizada no existe

**Causa**: Falta el prefijo `#__` de Joomla.

**Solución**:
```php
<?php
// INCORRECTO
->from('mi_tabla')

// CORRECTO - usa prefijo dinámico
->from($this->db->quoteName('#__mi_tabla'))
?>
```

## Problemas de Parámetros

### Los parámetros no se guardan

**Causa**: Los campos en manifest.xml no tienen el fieldset correcto.

**Solución**:
```xml
<!-- INCORRECTO - fieldset vacío -->
<config>
    <fields>
        <field name="titulo" type="text" />
    </fields>
</config>

<!-- CORRECTO -->
<config>
    <fields name="params">
        <fieldset name="basic">
            <field name="titulo" type="text" label="MOD_TITULO" />
        </fieldset>
    </fields>
</config>
```

### El parámetro siempre devuelve el default

**Causa**: El nombre en manifest.xml no coincide con como se accede.

**Solución**:
```xml
<!-- manifest.xml -->
<field name="miparametro" type="text" default="valor" />
```

```php
<?php
// En Dispatcher o Helper
$params = $this->module->params;
$valor = $params->get('miparametro', 'default');  // Nombre debe coincidir
?>
```

## Problemas de Seguridad

### XSS en el template

**Síntoma**: JavaScript se ejecuta desde los datos del módulo.

**Causa**: No escapar variables en HTML.

**Solución**:
```php
<?php
// INCORRECTO - XSS vulnerable
echo $item->title;

// CORRECTO
echo htmlspecialchars($item->title, ENT_QUOTES, 'UTF-8');

// O con HTMLHelper
use Joomla\CMS\HTML\HTMLHelper;
echo HTMLHelper::_('string.truncate', $item->title, 50);
?>
```

### Acceso a datos privados de otros usuarios

**Causa**: No validar estado del artículo o permissions.

**Solución**:
```php
<?php
public function getItems($category = null)
{
    $query = $this->db->getQuery(true)
        ->select('*')
        ->from($this->db->quoteName('#__articles'))
        // IMPORTANTE: filtrar por estado
        ->where($this->db->quoteName('state') . ' = 1')
        // Y por fecha de publicación
        ->where('NOW() >= ' . $this->db->quoteName('publish_up'))
        ->where('(NOW() <= ' . $this->db->quoteName('publish_down') .
                ' OR ' . $this->db->quoteName('publish_down') . ' = ' .
                $this->db->quote('0000-00-00 00:00:00') . ')');

    if ($category) {
        $query->where($this->db->quoteName('catid') . ' = ' . (int)$category);
    }

    return $this->db->setQuery($query)->loadObjectList();
}
?>
```

## Problemas de Compatibilidad

### Módulo funciona en Joomla 5 pero no en 6

**Causa**: Cambios en APIs deprecadas.

**Solución**:
- Evitar `JFactory::*` - usar inyección de dependencias
- Evitar métodos deprecated - revisar logs de debug
- Actualizar namespace paths

```php
<?php
// DEPRECATED en J6
use Joomla\CMS\Factory;
$db = Factory::getDbo();

// CORRECTO - inyectar
use Joomla\Database\DatabaseInterface;
public function __construct(DatabaseInterface $db) {
    $this->db = $db;
}
?>
```

### El módulo no aparece en listado de instalación

**Causa**: Manifest.xml inválido o carpeta con nombre incorrecto.

**Solución**:
- Carpeta debe ser `mod_[nombre]`
- Verificar manifest.xml con validador XML
- El atributo type debe ser "module"

```xml
<!-- CORRECTO -->
<extension type="module" client="site" method="upgrade">
```

## Performance

### El módulo es lento

**Soluciones**:
1. Habilitar cache en manifest.xml:
```xml
<field name="cache" type="list" default="1">
    <option value="0">No</option>
    <option value="1">Sí</option>
</field>
```

2. Limitar queries:
```php
<?php
->setLimit(10)  // No cargar 1000 registros
->select('id, title')  // Solo campos necesarios
?>
```

3. Usar indexes en BD:
```sql
CREATE INDEX idx_state ON jos_articles (state);
CREATE INDEX idx_catid ON jos_articles (catid);
```

## Testing

### Testear sin instalar

```php
<?php
// Crear archivo test.php en raíz del módulo
use Joomla\CMS\Factory;
use Joomla\Module\Mimodulo\Helper\MiHelper;

$db = Factory::getDbo();
$helper = new MiHelper($db);
$items = $helper->getItems(5);

var_dump($items);
?>
```

### Logs detallados

```php
<?php
// Agregar a config.php
public $log_path = '/var/www/html/joomla/logs';
public $log_everything = true;

// Ver en /logs/joomla.log
?>
```

---

**Más ayuda**:
- Forum: https://forum.joomla.org/
- Stack Exchange: https://joomla.stackexchange.com/
- Docs: https://docs.joomla.org/
