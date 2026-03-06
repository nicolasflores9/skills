# Troubleshooting and Common Cases

## Installation Problems

### Error: "Class not found" during installation

**Cause**: The namespace in manifest.xml does not match the folder structure.

**Solution**:
- Verify that `namespace path="src"` is in manifest.xml
- Confirm that `Joomla\Module\[ModuleName]` matches the classes in src/
- The path should be: `src/Dispatcher/Dispatcher.php` → `Joomla\Module\[ModuleName]\Dispatcher\Dispatcher`

```xml
<!-- Correct -->
<namespace path="src">Joomla\Module\MiModulo</namespace>

<!-- Incorrect - missing path -->
<namespace>Joomla\Module\MiModulo</namespace>
```

### Error: "Module file not found"

**Cause**: The main file `mod_[name].php` does not exist or is not registered.

**Solution**:
```xml
<!-- In manifest.xml it must be: -->
<files>
    <filename module="mod_mimodulo">mod_mimodulo.php</filename>
</files>

<!-- The file must exist in the module root -->
mod_mimodulo/
├── mod_mimodulo.php    ← HERE
├── manifest.xml
└── ...
```

### Error: "Invalid manifest" during installation

**Cause**: XML syntax errors.

**Solution**:
- Validate XML with an online validator (xmlvalidation.com)
- Check special characters: `&` must be `&amp;`
- Close all tags
- UTF-8 encoding declared: `<?xml version="1.0" encoding="UTF-8"?>`

## Rendering Problems

### The module does not appear on the frontend

**Checklist**:
1. Is the module published? (Extensions → Modules)
2. Is it assigned to a position? (edit module)
3. Is the position in the template? (System Templates)
4. Is it enabled for the current menu?

**Debug**:
```php
<?php
// In tmpl/default.php
echo '<!-- Module loaded -->';
var_dump($displayData);
?>
```

### The template does not render

**Cause**: Dispatcher does not prepare data for the template.

**Solution**:
```php
<?php
namespace Joomla\Module\Mimodulo\Dispatcher;

class Dispatcher extends AbstractModuleDispatcher
{
    protected function getLayoutData(): array
    {
        $data = parent::getLayoutData();
        // IMPORTANT: add data here
        $data['items'] = $this->helper->getItems();
        return $data;
    }
}
```

### Error: "Undefined variable" in template

**Cause**: Variable does not come from getLayoutData().

**Solution**:
- All variables must be in `$displayData`
- Access as: `$displayData['key']`
- Do NOT use `$variable` directly

```php
<?php
// INCORRECT
echo $items;  // undefined

// CORRECT
echo $displayData['items'];
?>
```

## Database Problems

### Query returns no results

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

    // Log the query
    \Joomla\CMS\Log\Log::add(
        'SQL: ' . $query->__toString(),
        \Joomla\CMS\Log\Log::DEBUG,
        'mod_mimodulo'
    );

    return $this->db->setQuery($query)->loadObjectList();
}
?>
```

### SQL error with special characters

**Cause**: Not using `quoteName()` on identifiers.

**Solution**:
```php
<?php
// INCORRECT - fails with special characters
->where('state = 1')

// CORRECT
->where($this->db->quoteName('state') . ' = 1')

// With variables
->where($this->db->quoteName('title') . ' = ' . $this->db->quote($value))
?>
```

### Custom table does not exist

**Cause**: Missing the Joomla `#__` prefix.

**Solution**:
```php
<?php
// INCORRECT
->from('mi_tabla')

// CORRECT - uses dynamic prefix
->from($this->db->quoteName('#__mi_tabla'))
?>
```

## Parameter Problems

### Parameters are not saving

**Cause**: Fields in manifest.xml do not have the correct fieldset.

**Solution**:
```xml
<!-- INCORRECT - empty fieldset -->
<config>
    <fields>
        <field name="titulo" type="text" />
    </fields>
</config>

<!-- CORRECT -->
<config>
    <fields name="params">
        <fieldset name="basic">
            <field name="titulo" type="text" label="MOD_TITULO" />
        </fieldset>
    </fields>
</config>
```

### Parameter always returns the default

**Cause**: The name in manifest.xml does not match how it is accessed.

**Solution**:
```xml
<!-- manifest.xml -->
<field name="miparametro" type="text" default="valor" />
```

```php
<?php
// In Dispatcher or Helper
$params = $this->module->params;
$valor = $params->get('miparametro', 'default');  // Name must match
?>
```

## Security Problems

### XSS in the template

**Symptom**: JavaScript executes from the module data.

**Cause**: Not escaping variables in HTML.

**Solution**:
```php
<?php
// INCORRECT - XSS vulnerable
echo $item->title;

// CORRECT
echo htmlspecialchars($item->title, ENT_QUOTES, 'UTF-8');

// Or with HTMLHelper
use Joomla\CMS\HTML\HTMLHelper;
echo HTMLHelper::_('string.truncate', $item->title, 50);
?>
```

### Access to private data from other users

**Cause**: Not validating article state or permissions.

**Solution**:
```php
<?php
public function getItems($category = null)
{
    $query = $this->db->getQuery(true)
        ->select('*')
        ->from($this->db->quoteName('#__articles'))
        // IMPORTANT: filter by state
        ->where($this->db->quoteName('state') . ' = 1')
        // And by publication date
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

## Compatibility Problems

### Module works in Joomla 5 but not in 6

**Cause**: Changes in deprecated APIs.

**Solution**:
- Avoid `JFactory::*` - use dependency injection
- Avoid deprecated methods - check debug logs
- Update namespace paths

```php
<?php
// DEPRECATED in J6
use Joomla\CMS\Factory;
$db = Factory::getDbo();

// CORRECT - inject
use Joomla\Database\DatabaseInterface;
public function __construct(DatabaseInterface $db) {
    $this->db = $db;
}
?>
```

### The module does not appear in the installation listing

**Cause**: Invalid manifest.xml or incorrectly named folder.

**Solution**:
- Folder must be `mod_[name]`
- Validate manifest.xml with XML validator
- The type attribute must be "module"

```xml
<!-- CORRECT -->
<extension type="module" client="site" method="upgrade">
```

## Performance

### The module is slow

**Solutions**:
1. Enable cache in manifest.xml:
```xml
<field name="cache" type="list" default="1">
    <option value="0">No</option>
    <option value="1">Yes</option>
</field>
```

2. Limit queries:
```php
<?php
->setLimit(10)  // Do not load 1000 records
->select('id, title')  // Only necessary fields
?>
```

3. Use DB indexes:
```sql
CREATE INDEX idx_state ON jos_articles (state);
CREATE INDEX idx_catid ON jos_articles (catid);
```

## Testing

### Test without installing

```php
<?php
// Create a test.php file in the module root
use Joomla\CMS\Factory;
use Joomla\Module\Mimodulo\Helper\MiHelper;

$db = Factory::getDbo();
$helper = new MiHelper($db);
$items = $helper->getItems(5);

var_dump($items);
?>
```

### Detailed logs

```php
<?php
// Add to config.php
public $log_path = '/var/www/html/joomla/logs';
public $log_everything = true;

// View in /logs/joomla.log
?>
```

---

**More help**:
- Forum: https://forum.joomla.org/
- Stack Exchange: https://joomla.stackexchange.com/
- Docs: https://docs.joomla.org/
