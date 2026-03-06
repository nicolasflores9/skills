# Extended Reference: Joomla 5/6 - Database Queries

## Complete APIs

### DatabaseInterface - Main Methods

```php
// Getting the instance
$db = Factory::getContainer()->get(DatabaseInterface::class);

// Query Methods
$query = $db->getQuery(true);           // New query
$db->setQuery($query);                  // Sets current query
$db->execute();                         // Executes query

// Result Methods
$results = $db->loadObjectList();       // Array of stdClass
$result = $db->loadObject();            // One object
$assoc = $db->loadAssocList();          // Associative arrays
$row = $db->loadAssoc();                // One associative array
$column = $db->loadColumn();            // One column
$value = $db->loadResult();             // A single value

// Convenience Methods
$db->insertObject($table, $object);     // Insert object
$db->updateObject($table, $object);     // Update object

// Security Methods
$db->quoteName($identifier);            // Escapes identifiers
$db->quote($value);                     // Escapes values (legacy)

// Information
$db->getPrefix();                       // Gets prefix
$db->countAffected();                   // Rows affected by last query
$db->getLastError();                    // Last error
```

### QueryInterface - Builder Methods

```php
// SELECT
$query->select($columns);               // Specifies columns
$query->distinct();                     // DISTINCT
$query->from($table);                   // FROM
$query->where($condition);              // WHERE (multiple)
$query->group($columns);                // GROUP BY
$query->having($condition);             // HAVING
$query->order($columns);                // ORDER BY

// JOINs
$query->innerJoin($table . ' ON ' . $condition);
$query->leftJoin($table . ' ON ' . $condition);
$query->rightJoin($table . ' ON ' . $condition);
$query->outerJoin($table . ' ON ' . $condition);

// LIMIT/OFFSET
$query->setLimit($limit, $offset);      // LIMIT OFFSET
$query->limit($limit);                  // LIMIT
$query->offset($offset);                // OFFSET

// INSERT
$query->insert($table);                 // Starts INSERT
$query->columns($columns);              // INSERT columns
$query->values($values);                // INSERT values

// UPDATE
$query->update($table);                 // Starts UPDATE
$query->set($assignments);              // SET

// DELETE
$query->delete($table);                 // Starts DELETE

// Parameters
$query->bind($key, $value, $type);      // Binds parameter
$query->bindArray($array);              // Binds array
```

---

## Recommended Code Patterns

### Pattern 1: Repository Pattern

```php
<?php
namespace MyNamespace\Repository;

use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\ParameterType;

class ArticleRepository
{
    private $db;

    public function __construct(DatabaseInterface $db)
    {
        $this->db = $db;
    }

    public function getById($id)
    {
        $query = $this->db->getQuery(true)
            ->select('*')
            ->from($this->db->quoteName('#__content'))
            ->where($this->db->quoteName('id') . ' = :id')
            ->bind(':id', $id, ParameterType::INTEGER);

        $this->db->setQuery($query);
        return $this->db->loadObject();
    }

    public function getByCategory($categoryId, $limit = 20)
    {
        $query = $this->db->getQuery(true)
            ->select('*')
            ->from($this->db->quoteName('#__content'))
            ->where($this->db->quoteName('catid') . ' = :catid')
            ->bind(':catid', $categoryId, ParameterType::INTEGER)
            ->setLimit($limit);

        $this->db->setQuery($query);
        return $this->db->loadObjectList();
    }

    public function save($data)
    {
        if (!isset($data->id) || $data->id === 0) {
            return $this->db->insertObject('#__content', $data, 'id');
        }
        return $this->db->updateObject('#__content', $data, 'id');
    }
}
?>
```

### Pattern 2: Query Builder with Dynamic Filters

```php
<?php
class ArticleQueryBuilder
{
    private $db;
    private $query;

    public function __construct(DatabaseInterface $db)
    {
        $this->db = $db;
        $this->query = $db->getQuery(true);
    }

    public function withState($state = 1)
    {
        $this->query->where($this->db->quoteName('state') . ' = :state')
                    ->bind(':state', $state, ParameterType::INTEGER);
        return $this;
    }

    public function withCategory($categoryId)
    {
        $this->query->where($this->db->quoteName('catid') . ' = :catid')
                    ->bind(':catid', $categoryId, ParameterType::INTEGER);
        return $this;
    }

    public function withSearch($term)
    {
        $this->query->where(
            $this->db->quoteName('title') . ' LIKE :search OR ' .
            $this->db->quoteName('introtext') . ' LIKE :search'
        )->bind(':search', '%' . $term . '%');
        return $this;
    }

    public function orderBy($field, $direction = 'ASC')
    {
        $this->query->order($this->db->quoteName($field) . ' ' . $direction);
        return $this;
    }

    public function paginate($limit, $page = 1)
    {
        $offset = ($page - 1) * $limit;
        $this->query->setLimit($limit, $offset);
        return $this;
    }

    public function get()
    {
        $this->db->setQuery($this->query);
        return $this->db->loadObjectList();
    }

    public function count()
    {
        $countQuery = $this->db->getQuery(true)
            ->select('COUNT(*)')
            ->from($this->db->quoteName('#__content'));

        // Copy WHERE conditions from the original query
        $whereString = (string) $this->query->where;
        if ($whereString) {
            $countQuery->where($whereString);
        }

        $this->db->setQuery($countQuery);
        return $this->db->loadResult();
    }
}

// Usage:
$builder = new ArticleQueryBuilder($db);
$articles = $builder
    ->withState(1)
    ->withCategory(5)
    ->withSearch('joomla')
    ->orderBy('created', 'DESC')
    ->paginate(10, 1)
    ->get();

$total = $builder->count();
?>
```

---

## Debugging and Logging

### Logging Executed Queries

```php
// In a component or model
$query = $db->getQuery(true)
    ->select('*')
    ->from($db->quoteName('#__content'));

$db->setQuery($query);

// View SQL before executing
error_log("SQL: " . $query->__toString());

$results = $db->loadObjectList();

// Joomla logging
use Joomla\CMS\Log\Log;

Log::add('SQL Query: ' . $query->__toString(), Log::INFO, 'joomla');
Log::add('Error: ' . $db->getLastError(), Log::ERROR, 'joomla');
```

### Performance Analysis

```php
// Measure query time
$start = microtime(true);

$db->setQuery($query);
$results = $db->loadObjectList();

$elapsed = microtime(true) - $start;
error_log("Query executed in: " . $elapsed . " seconds");

// Log slow queries
if ($elapsed > 0.5) {
    Log::add(
        "Slow query: " . $query->__toString() . " (" . $elapsed . "s)",
        Log::WARNING,
        'joomla'
    );
}
```

---

## Version Differences

### Joomla 3.x vs 4.x vs 5.x

| Feature | Joomla 3.x | Joomla 4.x | Joomla 5.x |
|---------|-----------|-----------|-----------|
| Factory::getDbo() | Standard | Available | **Deprecated** |
| Prepared Statements | Optional | Recommended | **Mandatory** |
| quoteName() | Available | Improved | Standard |
| bind() | Does not exist | Available | **Standard** |
| ParameterType | Does not exist | Available | **Recommended** |
| Query Chaining | Available | Standard | Standard |
| Container/DI | Does not exist | Yes | Yes |
| Minimum PHP | 5.3 | 7.2 | **8.1** |

### Migration from Joomla 3.x to 5.x

**Old (Joomla 3.x):**
```php
$db = JFactory::getDbo();
$query = $db->getQuery(true);
$query->select('*')
  ->from($db->quoteName('#__content'))
  ->where('id = ' . $id); // Direct concatenation

$db->setQuery($query);
$results = $db->loadObjectList();
```

**New (Joomla 5.x):**
```php
use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\ParameterType;

$db = Factory::getContainer()->get(DatabaseInterface::class);
$query = $db->getQuery(true);
$query->select('*')
  ->from($db->quoteName('#__content'))
  ->where($db->quoteName('id') . ' = :id')
  ->bind(':id', $id, ParameterType::INTEGER); // Prepared statement

$db->setQuery($query);
$results = $db->loadObjectList();
```

---

## Common Troubleshooting

### Error: "Call to undefined method bind()"

**Cause:** Forgetting to use `getQuery(true)` or using an old query

**Solution:**
```php
// INCORRECT
$query = $db->getQuery();
$query->bind(':id', 5); // Error

// CORRECT
$query = $db->getQuery(true);
$query->bind(':id', 5, ParameterType::INTEGER);
```

### Error: "Table '#__content' doesn't exist"

**Cause:** Incorrect prefix or not using `#__`

**Solution:**
```php
// INCORRECT - assumes hardcoded prefix
->from($db->quoteName('joomla_content'))

// CORRECT - uses #__
->from($db->quoteName('#__content'))
```

### Error: "Syntax error in SQL query"

**Cause:** Concatenating values without bind()

**Solution:**
```php
// INCORRECT
$search = "test'; DROP TABLE users;--";
->where("title = '$search'")

// CORRECT
->where($db->quoteName('title') . ' = :search')
->bind(':search', $search, ParameterType::STRING)
```

### Error: "Access denied for user"

**Cause:** Database user without permissions on the table

**Solution:**
1. Verify user permissions in phpMyAdmin
2. Ensure the database user has SELECT/INSERT/UPDATE/DELETE permissions
3. Review configuration in `configuration.php`

---

## Security Checklist

Before using a query in production:

- [ ] Is `bind()` used for all dynamic values?
- [ ] Is `quoteName()` used for all identifiers?
- [ ] Is `ParameterType` specified in every `bind()`?
- [ ] Is input validated before passing to the query?
- [ ] Are exceptions handled with try-catch?
- [ ] Is `getQuery(true)` used for a new clean query?
- [ ] Is `Factory::getDbo()` avoided (deprecated)?
- [ ] Is HTML escaped if displayed in a view?
- [ ] Are errors logged?

---

## Official Resources

- Joomla 5 Documentation: https://docs.joomla.org/
- Joomla API: https://api.joomla.org/
- GitHub Manual: https://github.com/joomla/Manual
- Security: https://manual.joomla.org/docs/5.0/security/

---

**Last updated:** March 2024
**Joomla Version:** 5.x, 6.x
