# Database Reference: Custom Fields in Joomla 5/6

## Main Tables

### #__fields - Field Definitions

Stores the configuration of each custom field.

```sql
CREATE TABLE `#__fields` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `context` VARCHAR(255) NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `label` VARCHAR(255) NOT NULL,
    `description` TEXT,
    `type` VARCHAR(255) NOT NULL,
    `default_value` MEDIUMTEXT,
    `params` MEDIUMTEXT,
    `fieldparams` MEDIUMTEXT,
    `access` INT UNSIGNED NOT NULL DEFAULT 1,
    `state` INT NOT NULL DEFAULT 1,
    `group_id` INT,
    UNIQUE KEY `idx_context_name` (`context`, `name`),
    INDEX `idx_context` (`context`),
    INDEX `idx_state` (`state`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**Fields:**
- `id` - Unique field ID
- `context` - Context (com_content.article, com_users.user, etc.)
- `name` - Technical name (snake_case, unique per context)
- `label` - Visible label in forms
- `description` - Description / instructions
- `type` - Field type (text, textarea, list, media, etc.)
- `default_value` - Default value for new elements
- `params` - General configuration in JSON
- `fieldparams` - Type-specific parameters in JSON
- `access` - Joomla access level (1=Public, 2=Registered, 3=Special, etc.)
- `state` - Published (1) or Unpublished (0)
- `group_id` - Field group ID (Field Group)

### #__fields_values - Field Values

Stores the actual field values for each element.

```sql
CREATE TABLE `#__fields_values` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `field_id` INT UNSIGNED NOT NULL,
    `item_id` INT UNSIGNED NOT NULL,
    `value` LONGTEXT,
    INDEX `idx_field_item` (`field_id`, `item_id`),
    INDEX `idx_item` (`item_id`),
    CONSTRAINT `fk_field_id` FOREIGN KEY (`field_id`)
        REFERENCES `#__fields`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**Fields:**
- `id` - Unique value ID
- `field_id` - Reference to #__fields.id
- `item_id` - Element ID (article, user, etc.)
- `value` - Stored value (JSON for multiple values)

### #__fields_groups - Field Groups

Organizes fields into tabs in the editing form.

```sql
CREATE TABLE `#__fields_groups` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT,
    `context` VARCHAR(255) NOT NULL,
    `access` INT UNSIGNED NOT NULL DEFAULT 1,
    `state` INT NOT NULL DEFAULT 1,
    `params` MEDIUMTEXT,
    INDEX `idx_context` (`context`),
    INDEX `idx_state` (`state`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**Fields:**
- `id` - Unique group ID
- `title` - Visible name in tab
- `description` - Group description
- `context` - Context (com_content.article, etc.)
- `access` - Access level
- `state` - Published (1) or Not (0)
- `params` - Additional configuration in JSON

## Common Query Examples

### 1. Get all fields for an article

```sql
SELECT fv.*, f.name, f.label, f.type
FROM `#__fields_values` fv
INNER JOIN `#__fields` f ON fv.field_id = f.id
WHERE fv.item_id = 123
  AND f.context = 'com_content.article'
  AND f.state = 1;
```

### 2. Get all field definitions for a context

```sql
SELECT *
FROM `#__fields`
WHERE context = 'com_content.article'
  AND state = 1
ORDER BY label ASC;
```

### 3. Get grouped fields with group information

```sql
SELECT f.*, fg.title as group_title
FROM `#__fields` f
LEFT JOIN `#__fields_groups` fg ON f.group_id = fg.id
WHERE f.context = 'com_content.article'
  AND f.state = 1
ORDER BY f.group_id, f.label;
```

### 4. Fields with visible permissions for a user

```sql
-- Assuming the user has access levels [1, 2, 4]
SELECT *
FROM `#__fields`
WHERE context = 'com_content.article'
  AND state = 1
  AND access IN (1, 2, 4)
ORDER BY label ASC;
```

### 5. Get latest modified values of a field

```sql
SELECT fv.*, a.title
FROM `#__fields_values` fv
INNER JOIN `#__content` a ON fv.item_id = a.id
WHERE fv.field_id = 5
ORDER BY a.modified DESC
LIMIT 10;
```

### 6. Count how many elements have a completed field

```sql
SELECT COUNT(DISTINCT item_id) as elements_with_value
FROM `#__fields_values`
WHERE field_id = 5 AND value IS NOT NULL AND value != '';
```

### 7. Search articles by field value

```sql
-- Search articles where "color_destacado" = "rojo"
SELECT DISTINCT a.*
FROM `#__content` a
INNER JOIN `#__fields_values` fv ON a.id = fv.item_id
INNER JOIN `#__fields` f ON fv.field_id = f.id
WHERE f.name = 'color_destacado'
  AND fv.value = 'rojo'
  AND f.context = 'com_content.article';
```

## JSON Structure in Fields

### `params` Parameter

Contains general field configuration in JSON:

```json
{
    "class": "campo-personalizado",
    "hint": "Enter a value",
    "required": false,
    "filter": "RAW",
    "automatic_display": "false"
}
```

### `fieldparams` Parameter

Type-specific field parameters:

**For List type:**
```json
{
    "options": "Option 1\nOption 2\nOption 3",
    "multiple": false
}
```

**For Text type:**
```json
{
    "maxlength": 255,
    "rows": 1,
    "cols": 50
}
```

**For Media type:**
```json
{
    "directory": "images",
    "preview": "true"
}
```

### `value` Field in fields_values

For simple fields:
```
"simple value"
```

For multiple fields (checkbox, multiple list):
```json
["option1", "option2", "option3"]
```

For repeatable fields:
```json
[
    {"name": "Item 1", "description": "Description 1"},
    {"name": "Item 2", "description": "Description 2"}
]
```

## JDatabase Queries (Joomla)

### Complete DB Repository Class

```php
<?php
namespace MyApp\Repository;

use Joomla\Database\DatabaseInterface;
use Joomla\CMS\Factory;

class FieldsRepository {

    private $db;

    public function __construct(DatabaseInterface $db) {
        $this->db = $db;
    }

    /**
     * Get all fields for an article with their values
     */
    public function getArticleFields($articleId) {
        $query = $this->db->getQuery(true)
            ->select(['f.*', 'fv.value'])
            ->from($this->db->quoteName('#__fields', 'f'))
            ->leftJoin(
                $this->db->quoteName('#__fields_values', 'fv') . ' ON ' .
                $this->db->quoteName('f.id') . ' = ' . $this->db->quoteName('fv.field_id') .
                ' AND ' . $this->db->quoteName('fv.item_id') . ' = ' . (int)$articleId
            )
            ->where($this->db->quoteName('f.context') . ' = ' . $this->db->quote('com_content.article'))
            ->where($this->db->quoteName('f.state') . ' = 1')
            ->order($this->db->quoteName('f.label') . ' ASC');

        $this->db->setQuery($query);
        return $this->db->loadObjectList('id');
    }

    /**
     * Save or update a field value
     */
    public function saveValue($fieldId, $itemId, $value) {
        // Check if it already exists
        $query = $this->db->getQuery(true)
            ->select('id')
            ->from($this->db->quoteName('#__fields_values'))
            ->where($this->db->quoteName('field_id') . ' = ' . (int)$fieldId)
            ->where($this->db->quoteName('item_id') . ' = ' . (int)$itemId);

        $this->db->setQuery($query);
        $existing = $this->db->loadResult();

        if ($existing) {
            // Update
            $query = $this->db->getQuery(true)
                ->update($this->db->quoteName('#__fields_values'))
                ->set($this->db->quoteName('value') . ' = ' . $this->db->quote(json_encode($value)))
                ->where($this->db->quoteName('id') . ' = ' . (int)$existing);

            $this->db->setQuery($query);
        } else {
            // Insert
            $obj = new \stdClass();
            $obj->field_id = (int)$fieldId;
            $obj->item_id = (int)$itemId;
            $obj->value = json_encode($value);

            $this->db->insertObject('#__fields_values', $obj);
        }

        return true;
    }

    /**
     * Get field by name
     */
    public function getFieldByName($name, $context) {
        $query = $this->db->getQuery(true)
            ->select('*')
            ->from($this->db->quoteName('#__fields'))
            ->where($this->db->quoteName('name') . ' = ' . $this->db->quote($name))
            ->where($this->db->quoteName('context') . ' = ' . $this->db->quote($context))
            ->where($this->db->quoteName('state') . ' = 1');

        $this->db->setQuery($query);
        return $this->db->loadObject();
    }

    /**
     * Delete all values for an element
     */
    public function deleteItemValues($itemId) {
        $query = $this->db->getQuery(true)
            ->delete($this->db->quoteName('#__fields_values'))
            ->where($this->db->quoteName('item_id') . ' = ' . (int)$itemId);

        $this->db->setQuery($query);
        return $this->db->execute();
    }
}
?>
```

## Performance Considerations

1. **Indexes:** The tables come optimized with indexes on `context`, `state`, and the unique key on `context+name`.

2. **Joins:** If you need many fields, consider caching results with JCache:
   ```php
   $cache = Factory::getContainer()->get('cache');
   $key = 'article_fields_' . $articleId;
   $fields = $cache->get($key);

   if (!$fields) {
       $fields = FieldsHelper::getFields('com_content.article', $article, true);
       $cache->store($fields, $key, 3600); // 1 hour
   }
   ```

3. **Batch Operations:** For multiple updates, use transactions:
   ```php
   $this->db->transactionStart();

   foreach ($values as $fieldId => $value) {
       $this->saveValue($fieldId, $itemId, $value);
   }

   $this->db->transactionCommit();
   ```

## Versioning and Migration

For changes in field structure, create a SQL script in your component:

```sql
-- Add new field
INSERT INTO `#__fields` (context, name, label, type, state)
VALUES ('com_content.article', 'nuevo_campo', 'New Field', 'text', 1);

-- Delete field (cascade delete works automatically)
DELETE FROM `#__fields` WHERE id = X;

-- Modify field type
UPDATE `#__fields` SET type = 'textarea' WHERE id = X;
```

Always perform backups before modifying field structure in production.
