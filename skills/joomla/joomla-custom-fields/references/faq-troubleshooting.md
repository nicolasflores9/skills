# FAQ and Troubleshooting: Custom Fields in Joomla 5/6

## Frequently Asked Questions

### Q: How do I load custom fields in a non-core component?

A: Implement a system plugin that responds to `onContentPrepareForm` and calls `FieldsHelper::getFields()`. The context must be unique to your component (example: `com_mycomponent.myelement`).

```php
$context = 'com_mycomponent.myelement';
JLoader::register('FieldsHelper', JPATH_ADMINISTRATOR . '/components/com_fields/helpers/fields.php');
$fields = FieldsHelper::getFields($context, $data, false);
```

### Q: Can I create fields shared between multiple contexts?

A: Not directly. Each context has its own field definitions. However, you can create a plugin that replicates fields manually or use the same name across multiple contexts.

### Q: How do I access raw values without rendering?

A: Use the `$field->rawvalue` property instead of `$field->value`:

```php
// Rendered HTML
echo $field->value;

// Unprocessed value
echo $field->rawvalue;
```

### Q: Can I validate custom fields server-side?

A: Yes, implement `onContentValidateForm` in a plugin:

```php
public function onContentValidateForm($form, $data) {
    // Validate custom fields
    if (empty($data->mi_campo)) {
        $form->setError('The field is required');
        return false;
    }
    return true;
}
```

### Q: How do I store multiple values in a field?

A: Fields like Checkboxes and List (multiple) store values as JSON:

```php
// Field with multiple values
$value = ['option1', 'option2', 'option3'];
$db->setQuery(
    "INSERT INTO #__fields_values (field_id, item_id, value)
     VALUES ($fieldId, $itemId, " . $db->quote(json_encode($value)) . ")"
);
```

### Q: What is the difference between "Automatic Display" and manual rendering?

A: **Automatic Display:** Joomla automatically renders the field on the frontend (if configured). **Manual:** You control when and how it is displayed using `FieldsHelper::render()` or your own HTML.

### Q: Can I limit a field to specific users?

A: Use the **Access** parameter in the field configuration. Select the access level (Public, Registered, Special, or a custom level).

### Q: How do I migrate fields from one Joomla instance to another?

A: Export the #__fields and #__fields_groups tables:

```sql
-- Export
mysqldump db_source #__fields #__fields_groups #__fields_values > fields.sql

-- Import
mysql db_destination < fields.sql

-- Verify that contexts are valid in the destination
```

### Q: Can I create a field with dynamic options from the database?

A: Use a **SQL** field type and provide a query that returns the values:

```sql
SELECT id, name FROM #__categories WHERE state = 1
```

Or create a custom field type (more advanced).

### Q: How do I delete a field without losing data?

A: Simply deactivate the field (state = 0) instead of deleting it. The data is preserved in #__fields_values. You can reactivate it later.

```sql
UPDATE #__fields SET state = 0 WHERE id = X;
```

To permanently delete:

```sql
DELETE FROM #__fields WHERE id = X;  -- Values are automatically deleted (ON DELETE CASCADE)
```

### Q: Can I reorder fields in the form?

A: Use Field Groups. Fields are displayed in order within each group. Without an assigned group, they appear in the "Fields" tab in creation order.

### Q: How do I cache field results to improve performance?

A: Use JCache:

```php
$cache = Factory::getContainer()->get('cache');
$key = 'article_fields_' . $articleId;
$fields = $cache->get($key);

if (!$fields) {
    $fields = FieldsHelper::getFields('com_content.article', $article, true);
    $cache->store($fields, $key, 3600);  // 1 hour
}
```

### Q: What is the maximum number of fields per element?

A: Technically there is no hard limit, but the UI becomes slow with >100 fields. Use Field Groups for better organization.

---

## Troubleshooting

### Problem: Fields do not appear in the form

**Possible causes:**

1. **Field not published:** Verify that `state = 1` in #__fields
2. **Category limited:** If the field is limited to categories, verify it matches
3. **Access level:** The user does not have permission to see the field (check `access`)
4. **Incorrect context:** The form context does not match the field context

**Solution:**

```sql
SELECT * FROM #__fields
WHERE context = 'com_content.article'
  AND state = 1
  AND access <= [user_level];
```

### Problem: Field values are not saving

**Possible causes:**

1. **onContentPrepareData not injecting values:** The plugin is not preparing data correctly
2. **fields_values not updating:** The user does not have permission to edit fields
3. **Incorrect value format:** The value is not a valid string or JSON

**Solution:**

In the plugin, make sure `onContentPrepareData` injects the values:

```php
public function onContentPrepareData($context, $data) {
    if (strpos($context, 'com_content.article') === 0) {
        // Ensure fields are available
        if (!isset($data->jcfields)) {
            $data->jcfields = [];
        }
    }
}
```

### Problem: Template override is not working

**Checks:**

1. **Correct location:** `templates/[template]/html/layouts/com_content/fields/render.php`
2. **Valid syntax:** Check that the PHP is valid (no parse errors)
3. **Cache cleared:** Clear the Joomla cache (System -> Cache)
4. **Override created correctly:** Use the template manager to create/edit

**Debug:**

```php
<?php
// Add this to the override to verify
error_log('Override executed for field: ' . $displayData['field']->name);
?>
```

Check `/logs/everything.php` to see if the override is being executed.

### Problem: Field appears but without value

**Causes:**

1. **Value is NULL or empty:** Verify #__fields_values
2. **rawvalue vs value:** You are using `$field->value` instead of `$field->rawvalue`
3. **Filter applied incorrectly:** The filter removes the content

**Solution:**

```php
// Verify directly in the database
SELECT * FROM #__fields_values
WHERE field_id = X AND item_id = Y;

// In code, use rawvalue
echo $field->rawvalue;  // Without filters
```

### Problem: Fields are slow on large tables

**Causes:**

- Many elements with many fields
- Queries without appropriate indexes
- Loading everything into memory

**Solutions:**

1. **Cache the results:**
   ```php
   $fields = Cache::get('article_fields_' . $id)
              ?: FieldsHelper::getFields(...);
   ```

2. **Limit the loaded fields:**
   ```php
   // Instead of loading all, load only the ones needed
   $query = $db->getQuery(true)
       ->select(['fv.*', 'f.name'])
       ->from('#__fields_values fv')
       ->innerJoin('#__fields f...')
       ->where('f.name IN (' . $db->quote(['campo1', 'campo2']) . ')');
   ```

3. **Use pagination** if displaying many elements.

### Problem: Repeatable field does not work correctly

**Check:**

- The JSON is valid: `json_decode($field->value)` works
- You access the array correctly:
  ```php
  $items = json_decode($field->value, true);
  foreach ($items as $item) {
      echo $item['nombre'];
  }
  ```

### Problem: Access permissions are not working

**Check:**

```php
// Check the user's access levels
$user = Factory::getUser();
$userLevels = $user->getAuthorisedViewLevels();

// Verify if field->access is in those levels
if (in_array($field->access, $userLevels)) {
    // User can see the field
}
```

### Problem: Media field does not show image

**Causes:**

1. **Incorrect path:** The value contains a relative path but needs an absolute one
2. **File deleted:** The image was removed but the field still references it
3. **Permissions:** User does not have permission to view the directory

**Solution:**

```php
// Build full path if needed
$imagePath = $field->value;
if (strpos($imagePath, 'http') === false) {
    $imagePath = JURI::base() . $imagePath;
}

echo '<img src="' . htmlspecialchars($imagePath) . '" />';
```

### Problem: Custom validation is not executing

**Checks:**

1. **Validation file exists:** `/components/com_mycomponent/models/rules/myrule.php`
2. **Correct name:** Must be `JFormRuleMyrule`
3. **In the field:** `validate="myrule"`

```php
// Correct structure
class JFormRuleMyrule extends JFormRule {
    public function test(SimpleXMLElement $element, $value, $group = null, Registry $input = null, $form = null) {
        // Return true if valid, false if invalid
        return preg_match('/^[A-Z0-9]+$/', $value) === 1;
    }
}
```

### Problem: "Field not found" in REST API

**Check:**

- The field is published
- The context is correct
- The user has access to the context

**Solution:**

Expose fields manually in the JSON response:

```php
$fields = FieldsHelper::getFields('com_content.article', $article, false);
$response['custom_fields'] = [];

foreach ($fields as $field) {
    $response['custom_fields'][$field->name] = $field->rawvalue;
}
```

### Problem: Joomla upgrade breaks fields

**Prevention:**

1. Back up before updating
2. Verify that contexts are still valid post-upgrade
3. Clear cache after updating

**Recovery:**

```sql
-- Verify integrity
SELECT f.*, COUNT(fv.id) as values_count
FROM #__fields f
LEFT JOIN #__fields_values fv ON f.id = fv.field_id
GROUP BY f.id;

-- Remove orphaned values (field_id with no match in #__fields)
DELETE FROM #__fields_values
WHERE field_id NOT IN (SELECT id FROM #__fields);
```

---

## Debug Tools

### 1. Inspect Loaded Fields

```php
<?php
$item = Factory::getUser();
echo '<pre>';
var_dump($item->jcfields);
echo '</pre>';
?>
```

### 2. Verify Database Directly

```sql
-- All available fields
SELECT * FROM #__fields ORDER BY context, label;

-- Values for an element
SELECT f.name, f.label, fv.value
FROM #__fields_values fv
JOIN #__fields f ON fv.field_id = f.id
WHERE fv.item_id = [ID];
```

### 3. System Logs

Check `/administrator/logs/everything.php` for field loading errors.

### 4. Third-Party Tools

- **System Information:** System menu -> System Information -> Debug
- **Joomla Debug Bar:** Extension that shows queries and variables
- **Database Debugger:** View executed SQL queries

---

## Deployment Checklist

Before going to production with custom fields:

- [ ] Fields are published (state = 1)
- [ ] Access permissions configured correctly
- [ ] Default values defined
- [ ] Server-side validation implemented
- [ ] Template override tested in browser
- [ ] Performance tested with real data
- [ ] Database backup performed
- [ ] Tests in different browsers
- [ ] Documentation updated
- [ ] Rollback plan in case of error
