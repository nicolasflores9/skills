---
name: joomla-database-queries
description: "Joomla 5/6 database queries: SELECT, INSERT, UPDATE, DELETE, JOINs, Prepared Statements, DatabaseDriver, query chaining, SQL injection prevention, advanced filtering, sorting and pagination."
---

# Joomla 5/6: Database Query System

## Table of Contents
1. [Introduction](#introduction)
2. [Fundamental Concepts](#fundamental-concepts)
3. [SELECT Queries](#select-queries)
4. [Prepared Statements](#prepared-statements-mandatory)
5. [JOINs Between Tables](#joins-between-tables)
6. [Advanced Filtering](#advanced-filtering)
7. [Sorting and Pagination](#sorting-and-pagination)
8. [INSERT Operations](#insert-operations)
9. [UPDATE Operations](#update-operations)
10. [DELETE Operations](#delete-operations)
11. [Query Security](#query-security)

---

## Introduction

Joomla 5 and 6 implement a fully modernized database query system. The main changes from previous versions are:

- **Mandatory Prepared Statements**: Prevents SQL injections
- **Query Chaining**: Method chaining for more readable code
- **Improved Container/Factory**: Database access through the dependency injection container
- **Deprecated `Factory::getDbo()`**: Use `Factory::getContainer()->get(DatabaseInterface::class)`
- **ParameterType**: Type system for bound parameters

This skill covers everything needed to perform secure and efficient queries in Joomla 5/6.

---

## Fundamental Concepts

### Getting the Database Instance

In **models**:
```php
$db = $this->getDatabase();
```

In **other contexts**:
```php
use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;

$db = Factory::getContainer()->get(DatabaseInterface::class);
```

### Creating a Query

```php
$query = $db->getQuery(true); // true = new clean query
// or
$query = $db->createQuery();
```

### Naming and quoteName()

`#__` is the table prefix (replaced automatically):
```php
$db->quoteName('#__content')    // Returns: `joomla_content`
$db->quoteName('title')          // Returns: `title`
$db->quoteName(['id', 'title']) // Returns: `id`, `title`
$db->quoteName('#__content', 'c') // Alias: `joomla_content` AS `c`
```

### Importing ParameterType

```php
use Joomla\Database\ParameterType;
```

---

## SELECT Queries

### Basic Structure

```php
$query = $db->getQuery(true)
  ->select($db->quoteName(['id', 'title', 'created']))
  ->from($db->quoteName('#__content'))
  ->where($db->quoteName('state') . ' = :state')
  ->bind(':state', 1, ParameterType::INTEGER)
  ->order($db->quoteName('created') . ' DESC');

$db->setQuery($query);
$results = $db->loadObjectList();
```

### Main Methods

| Method | Description |
|--------|-------------|
| `select()` | Specifies fields to retrieve (array or string) |
| `from()` | Source table with optional alias |
| `where()` | WHERE conditions (multiple allowed) |
| `order()` | ASC/DESC sorting |
| `group()` | Result grouping |
| `having()` | Post-GROUP BY conditions |
| `setLimit(limit, offset)` | Result pagination |
| `innerJoin()` | INNER JOIN |
| `leftJoin()` | LEFT JOIN |
| `rightJoin()` | RIGHT JOIN |

### Loading Results

```php
$db->loadObjectList();   // Array of StdClass objects
$db->loadObject();       // A single object
$db->loadAssocList();    // Array of associative arrays
$db->loadAssoc();        // One associative array
$db->loadColumn();       // Array of a single column
$db->loadResult();       // A single value
```

### Progressive Example: SELECT

**Basic:**
```php
$query = $db->getQuery(true)
  ->select('*')
  ->from($db->quoteName('#__content'));

$db->setQuery($query);
$articles = $db->loadObjectList();
```

**With WHERE:**
```php
$query = $db->getQuery(true)
  ->select(['id', 'title'])
  ->from($db->quoteName('#__content'))
  ->where($db->quoteName('state') . ' = :state')
  ->bind(':state', 1, ParameterType::INTEGER);

$db->setQuery($query);
$articles = $db->loadObjectList();
```

**With multiple WHERE:**
```php
$query = $db->getQuery(true)
  ->select('*')
  ->from($db->quoteName('#__content'))
  ->where($db->quoteName('state') . ' = :state')
  ->where($db->quoteName('catid') . ' = :catid')
  ->bind(':state', 1, ParameterType::INTEGER)
  ->bind(':catid', 18, ParameterType::INTEGER);

$db->setQuery($query);
$articles = $db->loadObjectList();
```

---

## Prepared Statements (Mandatory)

Prepared statements are the secure way to inject dynamic values into SQL queries.

### Named Placeholder Syntax

```php
$query = $db->getQuery(true)
  ->select('*')
  ->from($db->quoteName('#__users'))
  ->where($db->quoteName('username') . ' = :username')
  ->bind(':username', $username, ParameterType::STRING);

$db->setQuery($query);
$user = $db->loadObject();
```

### Multiple Binding

```php
$query = $db->getQuery(true)
  ->select('*')
  ->from($db->quoteName('#__content'))
  ->where(
    $db->quoteName('created_by') . ' = :author AND ' .
    $db->quoteName('state') . ' = :state'
  )
  ->bind(':author', 42, ParameterType::INTEGER)
  ->bind(':state', 1, ParameterType::INTEGER);

$db->setQuery($query);
$articles = $db->loadObjectList();
```

### ParameterType - Available Types

```php
ParameterType::STRING      // Text
ParameterType::INTEGER     // Whole numbers
ParameterType::FLOAT       // Decimal numbers
ParameterType::BOOLEAN     // True/False
ParameterType::NULL        // NULL
```

### Array Binding

For IN queries with dynamic values:

```php
$ids = [1, 2, 3, 4];
$query = $db->getQuery(true)
  ->select(['id', 'username'])
  ->from($db->quoteName('#__users'));

$placeholders = $query->bindArray($ids);
$query->where($db->quoteName('id') . ' IN (' . implode(',', $placeholders) . ')');

$db->setQuery($query);
$users = $db->loadObjectList();
```

---

## JOINs Between Tables

### General Structure

```php
$query = $db->getQuery(true)
  ->select(['c.id', 'c.title', 'cat.title AS category_name'])
  ->from($db->quoteName('#__content', 'c'))
  ->leftJoin(
    $db->quoteName('#__categories', 'cat') . ' ON ' .
    $db->quoteName('c.catid') . ' = ' . $db->quoteName('cat.id')
  )
  ->where($db->quoteName('c.state') . ' = :state')
  ->bind(':state', 1, ParameterType::INTEGER);

$db->setQuery($query);
$results = $db->loadObjectList();
```

### JOIN Types

```php
// INNER JOIN
->innerJoin(
  $db->quoteName('#__users', 'u') . ' ON ' .
  $db->quoteName('c.created_by') . ' = ' . $db->quoteName('u.id')
)

// LEFT JOIN (keeps records from the left table)
->leftJoin(
  $db->quoteName('#__categories', 'cat') . ' ON ' .
  $db->quoteName('c.catid') . ' = ' . $db->quoteName('cat.id')
)

// RIGHT JOIN
->rightJoin(
  $db->quoteName('#__assets', 'a') . ' ON ' .
  $db->quoteName('c.id') . ' = ' . $db->quoteName('a.name')
)
```

### Triple JOIN: Content + Categories + Users

```php
$query = $db->getQuery(true)
  ->select([
    'c.id', 'c.title', 'c.introtext',
    'cat.id AS cat_id', 'cat.title AS cat_name',
    'u.name AS author_name'
  ])
  ->from($db->quoteName('#__content', 'c'))
  ->leftJoin(
    $db->quoteName('#__categories', 'cat') . ' ON ' .
    $db->quoteName('c.catid') . ' = ' . $db->quoteName('cat.id')
  )
  ->innerJoin(
    $db->quoteName('#__users', 'u') . ' ON ' .
    $db->quoteName('c.created_by') . ' = ' . $db->quoteName('u.id')
  )
  ->where($db->quoteName('c.state') . ' = :state')
  ->bind(':state', 1, ParameterType::INTEGER)
  ->order($db->quoteName('c.created') . ' DESC');

$db->setQuery($query);
$articles = $db->loadObjectList();
```

### JOIN with Custom Fields

```php
$query = $db->getQuery(true)
  ->select([
    'c.id', 'c.title',
    'fv.value AS custom_field_value'
  ])
  ->from($db->quoteName('#__content', 'c'))
  ->leftJoin(
    $db->quoteName('#__fields_values', 'fv') . ' ON ' .
    $db->quoteName('c.id') . ' = ' . $db->quoteName('fv.item_id') . ' AND ' .
    $db->quoteName('fv.field_id') . ' = :field_id'
  )
  ->where($db->quoteName('c.state') . ' = :state')
  ->bind(':field_id', 5, ParameterType::INTEGER)
  ->bind(':state', 1, ParameterType::INTEGER);

$db->setQuery($query);
$results = $db->loadObjectList();
```

---

## Advanced Filtering

### By Category

```php
$query = $db->getQuery(true)
  ->select('*')
  ->from($db->quoteName('#__content'))
  ->where($db->quoteName('catid') . ' = :catid')
  ->bind(':catid', 18, ParameterType::INTEGER);
```

### By Publication State

```php
// Published only
->where($db->quoteName('state') . ' = :state')
->bind(':state', 1, ParameterType::INTEGER)

// Multiple states (published or pending)
->where($db->quoteName('state') . ' IN (:state1, :state2)')
->bind(':state1', 0, ParameterType::INTEGER)
->bind(':state2', 1, ParameterType::INTEGER)
```

### By Date Range

```php
->where(
  $db->quoteName('created') . ' >= :start_date AND ' .
  $db->quoteName('created') . ' <= :end_date'
)
->bind(':start_date', '2024-01-01 00:00:00')
->bind(':end_date', '2024-12-31 23:59:59')
```

### By Text Search (LIKE)

```php
$search = 'joomla';
->where(
  $db->quoteName('title') . ' LIKE :search OR ' .
  $db->quoteName('introtext') . ' LIKE :search'
)
->bind(':search', '%' . $search . '%')
```

### Filtering by Custom Field

```php
$query = $db->getQuery(true)
  ->select(['c.id', 'c.title', 'fv.value'])
  ->from($db->quoteName('#__content', 'c'))
  ->innerJoin(
    $db->quoteName('#__fields_values', 'fv') . ' ON ' .
    $db->quoteName('c.id') . ' = ' . $db->quoteName('fv.item_id')
  )
  ->where(
    $db->quoteName('fv.field_id') . ' = :field_id AND ' .
    $db->quoteName('fv.value') . ' = :value'
  )
  ->bind(':field_id', 12, ParameterType::INTEGER)
  ->bind(':value', 'special', ParameterType::STRING);
```

---

## Sorting and Pagination

### ORDER BY

```php
// Simple sorting
->order($db->quoteName('created') . ' DESC')

// Multiple fields
->order([
  $db->quoteName('catid') . ' ASC',
  $db->quoteName('created') . ' DESC'
])
```

### LIMIT and OFFSET

```php
$limit = 10;
$page = 2;
$offset = ($page - 1) * $limit;

->setLimit($limit, $offset);
// Or alternatively:
->limit($limit)->offset($offset);
```

### Complete Example: Pagination

```php
$query = $db->getQuery(true)
  ->select('*')
  ->from($db->quoteName('#__content'))
  ->where($db->quoteName('state') . ' = :state')
  ->bind(':state', 1, ParameterType::INTEGER)
  ->order($db->quoteName('created') . ' DESC')
  ->setLimit(10, ($page - 1) * 10);

$db->setQuery($query);
$articles = $db->loadObjectList();

// To count total (same query without LIMIT)
$countQuery = $db->getQuery(true)
  ->select('COUNT(*)')
  ->from($db->quoteName('#__content'))
  ->where($db->quoteName('state') . ' = :state')
  ->bind(':state', 1, ParameterType::INTEGER);

$db->setQuery($countQuery);
$total = $db->loadResult();
```

---

## INSERT Operations

### INSERT with Query Chaining

```php
use Joomla\Database\ParameterType;

$query = $db->getQuery(true)
  ->insert($db->quoteName('#__content'))
  ->columns([
    $db->quoteName('title'),
    $db->quoteName('introtext'),
    $db->quoteName('state'),
    $db->quoteName('catid'),
    $db->quoteName('created'),
    $db->quoteName('created_by')
  ])
  ->values(':title, :introtext, :state, :catid, :created, :created_by')
  ->bind(':title', 'My New Article', ParameterType::STRING)
  ->bind(':introtext', 'Introductory text', ParameterType::STRING)
  ->bind(':state', 1, ParameterType::INTEGER)
  ->bind(':catid', 5, ParameterType::INTEGER)
  ->bind(':created', date('Y-m-d H:i:s'), ParameterType::STRING)
  ->bind(':created_by', 42, ParameterType::INTEGER);

$db->setQuery($query);
$db->execute();
```

### Convenient INSERT: insertObject()

```php
$data = new stdClass();
$data->title = 'New Article';
$data->introtext = 'Intro text';
$data->state = 1;
$data->catid = 5;
$data->created = date('Y-m-d H:i:s');
$data->created_by = 42;

$db->insertObject('#__content', $data, 'id');
```

### Multiple INSERT

```php
$query = $db->getQuery(true)
  ->insert($db->quoteName('#__content'))
  ->columns(['title', 'state', 'catid']);

$articles = [
  ['Article 1', 1, 5],
  ['Article 2', 1, 5],
  ['Article 3', 1, 5]
];

foreach ($articles as $i => $article) {
  $query->values(':title' . $i . ', :state' . $i . ', :catid' . $i);
  $query->bind(':title' . $i, $article[0], ParameterType::STRING);
  $query->bind(':state' . $i, $article[1], ParameterType::INTEGER);
  $query->bind(':catid' . $i, $article[2], ParameterType::INTEGER);
}

$db->setQuery($query);
$db->execute();
```

---

## UPDATE Operations

### Basic UPDATE

```php
$query = $db->getQuery(true)
  ->update($db->quoteName('#__content'))
  ->set([
    $db->quoteName('title') . ' = :title',
    $db->quoteName('state') . ' = :state',
    $db->quoteName('modified') . ' = :modified'
  ])
  ->where($db->quoteName('id') . ' = :id')
  ->bind(':title', 'Updated Title', ParameterType::STRING)
  ->bind(':state', 1, ParameterType::INTEGER)
  ->bind(':modified', date('Y-m-d H:i:s'), ParameterType::STRING)
  ->bind(':id', 42, ParameterType::INTEGER);

$db->setQuery($query);
$db->execute();
```

### Convenient UPDATE: updateObject()

```php
$data = new stdClass();
$data->id = 42;
$data->title = 'Updated Title';
$data->state = 1;
$data->modified = date('Y-m-d H:i:s');

$db->updateObject('#__content', $data, 'id');
```

### Conditional UPDATE

```php
$query = $db->getQuery(true)
  ->update($db->quoteName('#__content'))
  ->set($db->quoteName('state') . ' = :state')
  ->where(
    $db->quoteName('catid') . ' = :catid AND ' .
    $db->quoteName('state') . ' = :old_state'
  )
  ->bind(':state', 1, ParameterType::INTEGER)
  ->bind(':catid', 5, ParameterType::INTEGER)
  ->bind(':old_state', 0, ParameterType::INTEGER);

$db->setQuery($query);
$affected = $db->execute();
```

---

## DELETE Operations

### Simple DELETE

```php
$query = $db->getQuery(true)
  ->delete($db->quoteName('#__content'))
  ->where($db->quoteName('id') . ' = :id')
  ->bind(':id', 42, ParameterType::INTEGER);

$db->setQuery($query);
$db->execute();
```

### Conditional DELETE

```php
$query = $db->getQuery(true)
  ->delete($db->quoteName('#__content'))
  ->where(
    $db->quoteName('catid') . ' = :catid AND ' .
    $db->quoteName('state') . ' = :state'
  )
  ->bind(':catid', 8, ParameterType::INTEGER)
  ->bind(':state', -2, ParameterType::INTEGER); // -2 = Trash

$db->setQuery($query);
$db->execute();
```

### Cascade DELETE

```php
// First delete custom fields
$query1 = $db->getQuery(true)
  ->delete($db->quoteName('#__fields_values'))
  ->where($db->quoteName('item_id') . ' = :item_id')
  ->bind(':item_id', 42, ParameterType::INTEGER);
$db->setQuery($query1);
$db->execute();

// Then delete the article
$query2 = $db->getQuery(true)
  ->delete($db->quoteName('#__content'))
  ->where($db->quoteName('id') . ' = :id')
  ->bind(':id', 42, ParameterType::INTEGER);
$db->setQuery($query2);
$db->execute();
```

---

## Query Security

### Golden Rules

1. **ALWAYS use `quoteName()` for identifiers** (tables, fields):
```php
// CORRECT
$db->quoteName('title')
$db->quoteName('#__content')

// INCORRECT
"title"
'#__content'
```

2. **ALWAYS use `bind()` for values**:
```php
// CORRECT
->where($db->quoteName('username') . ' = :username')
->bind(':username', $user_input, ParameterType::STRING)

// INCORRECT - NEVER do this
->where("username = '$user_input'")
```

3. **Specify parameter types**:
```php
// CORRECT
->bind(':id', $id, ParameterType::INTEGER)
->bind(':name', $name, ParameterType::STRING)

// Less secure (no type specified)
->bind(':id', $id)
```

### SQL Injection Prevention

```php
// VULNERABLE
$title = "'; DROP TABLE #__content; --";
$query->where("title = '$title'"); // BAD!

// SAFE with Prepared Statements
$query->where($db->quoteName('title') . ' = :title')
  ->bind(':title', $title, ParameterType::STRING);
```

### Input Validation

Even though prepared statements provide protection, also validate:

```php
$search = htmlspecialchars($search);
$id = (int) $_GET['id']; // Cast to integer

$query->bind(':search', '%' . $search . '%', ParameterType::STRING)
  ->bind(':id', $id, ParameterType::INTEGER);
```

---

## Complete Use Cases

### List Category Articles with Pagination

```php
public function getArticlesBy($categoryId, $page = 1, $limit = 10)
{
    $db = $this->getDatabase();
    $offset = ($page - 1) * $limit;

    $query = $db->getQuery(true)
        ->select(['c.id', 'c.title', 'c.introtext', 'c.created', 'cat.title AS category'])
        ->from($db->quoteName('#__content', 'c'))
        ->leftJoin(
            $db->quoteName('#__categories', 'cat') . ' ON ' .
            $db->quoteName('c.catid') . ' = ' . $db->quoteName('cat.id')
        )
        ->where($db->quoteName('c.state') . ' = :state')
        ->where($db->quoteName('c.catid') . ' = :catid')
        ->bind(':state', 1, ParameterType::INTEGER)
        ->bind(':catid', $categoryId, ParameterType::INTEGER)
        ->order($db->quoteName('c.created') . ' DESC')
        ->setLimit($limit, $offset);

    $db->setQuery($query);
    return $db->loadObjectList();
}
```

### Advanced Search

```php
public function search($searchTerm, $categoryId = null, $limit = 20)
{
    $db = $this->getDatabase();

    $query = $db->getQuery(true)
        ->select(['id', 'title', 'introtext', 'created'])
        ->from($db->quoteName('#__content'))
        ->where(
            $db->quoteName('title') . ' LIKE :search OR ' .
            $db->quoteName('introtext') . ' LIKE :search'
        )
        ->where($db->quoteName('state') . ' = :state')
        ->bind(':search', '%' . $searchTerm . '%')
        ->bind(':state', 1, ParameterType::INTEGER);

    if ($categoryId) {
        $query->where($db->quoteName('catid') . ' = :catid')
              ->bind(':catid', $categoryId, ParameterType::INTEGER);
    }

    $query->order($db->quoteName('created') . ' DESC')
          ->setLimit($limit);

    $db->setQuery($query);
    return $db->loadObjectList();
}
```

---

## Best Practices Summary

- Use `$this->getDatabase()` in models
- Always use `quoteName()` for identifiers
- Always use `bind()` with prepared statements
- Specify `ParameterType` in bind()
- Chain methods for clean code
- Validate input before using in queries
- Handle exceptions with try-catch
- Test complex queries in phpMyAdmin first
- Document complex queries with comments
- Use short but clear table aliases

---

**Version:** 1.0
**Last updated:** 2024
**Compatibility:** Joomla 5.x, 6.x
**Level:** Intermediate-Advanced
