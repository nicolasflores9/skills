---
name: joomla-database-queries
description: "Consultas a base de datos en Joomla 5/6: SELECT, INSERT, UPDATE, DELETE, JOINs, Prepared Statements, DatabaseDriver, query chaining, seguridad contra inyección SQL, filtrado avanzado, ordenamiento y paginación."
---

# Joomla 5/6: Sistema de Consultas a Base de Datos

## Tabla de Contenidos
1. [Introducción](#introducción)
2. [Conceptos Fundamentales](#conceptos-fundamentales)
3. [Consultas SELECT](#consultas-select)
4. [Prepared Statements](#prepared-statements-obligatorios)
5. [JOINs entre Tablas](#joins-entre-tablas)
6. [Filtrado Avanzado](#filtrado-avanzado)
7. [Ordenamiento y Paginación](#ordenamiento-y-paginación)
8. [Operaciones INSERT](#operaciones-insert)
9. [Operaciones UPDATE](#operaciones-update)
10. [Operaciones DELETE](#operaciones-delete)
11. [Seguridad en Consultas](#seguridad-en-consultas)

---

## Introducción

Joomla 5 y 6 implementan un sistema de consultas a base de datos completamente modernizado. Los principales cambios respecto a versiones anteriores son:

- **Prepared Statements obligatorios**: Previene inyecciones SQL
- **Query Chaining**: Encadenamiento de métodos para código más legible
- **Container/Factory mejorado**: Acceso a la base de datos a través del contenedor de inyección de dependencias
- **Deprecated `Factory::getDbo()`**: Usar `Factory::getContainer()->get(DatabaseInterface::class)`
- **ParameterType**: Sistema de tipos para parámetros vinculados

Esta skill cubre todo lo necesario para realizar consultas seguras y eficientes en Joomla 5/6.

---

## Conceptos Fundamentales

### Obtener la Instancia de Base de Datos

En **modelos**:
```php
$db = $this->getDatabase();
```

En **otros contextos**:
```php
use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;

$db = Factory::getContainer()->get(DatabaseInterface::class);
```

### Crear una Consulta

```php
$query = $db->getQuery(true); // true = nueva query limpia
// o
$query = $db->createQuery();
```

### Nomenclatura y quoteName()

`#__` es el prefijo de tablas (se reemplaza automáticamente):
```php
$db->quoteName('#__content')    // Devuelve: `joomla_content`
$db->quoteName('title')          // Devuelve: `title`
$db->quoteName(['id', 'title']) // Devuelve: `id`, `title`
$db->quoteName('#__content', 'c') // Alias: `joomla_content` AS `c`
```

### Importar ParameterType

```php
use Joomla\Database\ParameterType;
```

---

## Consultas SELECT

### Estructura Básica

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

### Métodos Principales

| Método | Descripción |
|--------|-------------|
| `select()` | Especifica campos a recuperar (array o string) |
| `from()` | Tabla origen con alias opcional |
| `where()` | Condiciones WHERE (múltiples permitidas) |
| `order()` | Ordenamiento ASC/DESC |
| `group()` | Agrupación de resultados |
| `having()` | Condiciones post-GROUP BY |
| `setLimit(limit, offset)` | Paginación de resultados |
| `innerJoin()` | INNER JOIN |
| `leftJoin()` | LEFT JOIN |
| `rightJoin()` | RIGHT JOIN |

### Carga de Resultados

```php
$db->loadObjectList();   // Array de objetos StdClass
$db->loadObject();       // Un solo objeto
$db->loadAssocList();    // Array de arrays asociativos
$db->loadAssoc();        // Un array asociativo
$db->loadColumn();       // Array de una columna
$db->loadResult();       // Un solo valor
```

### Ejemplo Progresivo: SELECT

**Básico:**
```php
$query = $db->getQuery(true)
  ->select('*')
  ->from($db->quoteName('#__content'));

$db->setQuery($query);
$articles = $db->loadObjectList();
```

**Con WHERE:**
```php
$query = $db->getQuery(true)
  ->select(['id', 'title'])
  ->from($db->quoteName('#__content'))
  ->where($db->quoteName('state') . ' = :state')
  ->bind(':state', 1, ParameterType::INTEGER);

$db->setQuery($query);
$articles = $db->loadObjectList();
```

**Con múltiples WHERE:**
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

## Prepared Statements (Obligatorios)

Los prepared statements son la forma segura de inyectar valores dinámicos en consultas SQL.

### Sintaxis de Placeholders Nombrados

```php
$query = $db->getQuery(true)
  ->select('*')
  ->from($db->quoteName('#__users'))
  ->where($db->quoteName('username') . ' = :username')
  ->bind(':username', $username, ParameterType::STRING);

$db->setQuery($query);
$user = $db->loadObject();
```

### Vinculación Múltiple

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

### ParameterType - Tipos Disponibles

```php
ParameterType::STRING      // Texto
ParameterType::INTEGER     // Números enteros
ParameterType::FLOAT       // Números decimales
ParameterType::BOOLEAN     // Verdadero/Falso
ParameterType::NULL        // NULL
```

### Vinculación de Arrays

Para consultas IN con valores dinámicos:

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

## JOINs entre Tablas

### Estructura General

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

### Tipos de JOIN

```php
// INNER JOIN
->innerJoin(
  $db->quoteName('#__users', 'u') . ' ON ' .
  $db->quoteName('c.created_by') . ' = ' . $db->quoteName('u.id')
)

// LEFT JOIN (mantiene registros de tabla izquierda)
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

### JOIN Triple: Content + Categories + Users

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

### JOIN con Campos Personalizados

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

## Filtrado Avanzado

### Por Categoría

```php
$query = $db->getQuery(true)
  ->select('*')
  ->from($db->quoteName('#__content'))
  ->where($db->quoteName('catid') . ' = :catid')
  ->bind(':catid', 18, ParameterType::INTEGER);
```

### Por Estado de Publicación

```php
// Solo publicados
->where($db->quoteName('state') . ' = :state')
->bind(':state', 1, ParameterType::INTEGER)

// Múltiples estados (publicado o pendiente)
->where($db->quoteName('state') . ' IN (:state1, :state2)')
->bind(':state1', 0, ParameterType::INTEGER)
->bind(':state2', 1, ParameterType::INTEGER)
```

### Por Rango de Fechas

```php
->where(
  $db->quoteName('created') . ' >= :start_date AND ' .
  $db->quoteName('created') . ' <= :end_date'
)
->bind(':start_date', '2024-01-01 00:00:00')
->bind(':end_date', '2024-12-31 23:59:59')
```

### Por Búsqueda de Texto (LIKE)

```php
$search = 'joomla';
->where(
  $db->quoteName('title') . ' LIKE :search OR ' .
  $db->quoteName('introtext') . ' LIKE :search'
)
->bind(':search', '%' . $search . '%')
```

### Filtrado por Campo Personalizado

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
  ->bind(':value', 'especial', ParameterType::STRING);
```

---

## Ordenamiento y Paginación

### ORDER BY

```php
// Ordenamiento simple
->order($db->quoteName('created') . ' DESC')

// Múltiples campos
->order([
  $db->quoteName('catid') . ' ASC',
  $db->quoteName('created') . ' DESC'
])
```

### LIMIT y OFFSET

```php
$limit = 10;
$page = 2;
$offset = ($page - 1) * $limit;

->setLimit($limit, $offset);
// O alternativamente:
->limit($limit)->offset($offset);
```

### Ejemplo Completo: Paginación

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

// Para contar total (mismo query sin LIMIT)
$countQuery = $db->getQuery(true)
  ->select('COUNT(*)')
  ->from($db->quoteName('#__content'))
  ->where($db->quoteName('state') . ' = :state')
  ->bind(':state', 1, ParameterType::INTEGER);

$db->setQuery($countQuery);
$total = $db->loadResult();
```

---

## Operaciones INSERT

### INSERT con Query Chaining

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
  ->bind(':title', 'Mi Nuevo Artículo', ParameterType::STRING)
  ->bind(':introtext', 'Texto introductorio', ParameterType::STRING)
  ->bind(':state', 1, ParameterType::INTEGER)
  ->bind(':catid', 5, ParameterType::INTEGER)
  ->bind(':created', date('Y-m-d H:i:s'), ParameterType::STRING)
  ->bind(':created_by', 42, ParameterType::INTEGER);

$db->setQuery($query);
$db->execute();
```

### INSERT Conveniente: insertObject()

```php
$data = new stdClass();
$data->title = 'Nuevo Artículo';
$data->introtext = 'Texto intro';
$data->state = 1;
$data->catid = 5;
$data->created = date('Y-m-d H:i:s');
$data->created_by = 42;

$db->insertObject('#__content', $data, 'id');
```

### INSERT Múltiple

```php
$query = $db->getQuery(true)
  ->insert($db->quoteName('#__content'))
  ->columns(['title', 'state', 'catid']);

$articles = [
  ['Artículo 1', 1, 5],
  ['Artículo 2', 1, 5],
  ['Artículo 3', 1, 5]
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

## Operaciones UPDATE

### UPDATE Básico

```php
$query = $db->getQuery(true)
  ->update($db->quoteName('#__content'))
  ->set([
    $db->quoteName('title') . ' = :title',
    $db->quoteName('state') . ' = :state',
    $db->quoteName('modified') . ' = :modified'
  ])
  ->where($db->quoteName('id') . ' = :id')
  ->bind(':title', 'Título Actualizado', ParameterType::STRING)
  ->bind(':state', 1, ParameterType::INTEGER)
  ->bind(':modified', date('Y-m-d H:i:s'), ParameterType::STRING)
  ->bind(':id', 42, ParameterType::INTEGER);

$db->setQuery($query);
$db->execute();
```

### UPDATE Conveniente: updateObject()

```php
$data = new stdClass();
$data->id = 42;
$data->title = 'Título Actualizado';
$data->state = 1;
$data->modified = date('Y-m-d H:i:s');

$db->updateObject('#__content', $data, 'id');
```

### UPDATE Condicional

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

## Operaciones DELETE

### DELETE Simple

```php
$query = $db->getQuery(true)
  ->delete($db->quoteName('#__content'))
  ->where($db->quoteName('id') . ' = :id')
  ->bind(':id', 42, ParameterType::INTEGER);

$db->setQuery($query);
$db->execute();
```

### DELETE Condicional

```php
$query = $db->getQuery(true)
  ->delete($db->quoteName('#__content'))
  ->where(
    $db->quoteName('catid') . ' = :catid AND ' .
    $db->quoteName('state') . ' = :state'
  )
  ->bind(':catid', 8, ParameterType::INTEGER)
  ->bind(':state', -2, ParameterType::INTEGER); // -2 = Papelera

$db->setQuery($query);
$db->execute();
```

### DELETE en Cascada

```php
// Primero eliminar campos personalizados
$query1 = $db->getQuery(true)
  ->delete($db->quoteName('#__fields_values'))
  ->where($db->quoteName('item_id') . ' = :item_id')
  ->bind(':item_id', 42, ParameterType::INTEGER);
$db->setQuery($query1);
$db->execute();

// Luego eliminar el artículo
$query2 = $db->getQuery(true)
  ->delete($db->quoteName('#__content'))
  ->where($db->quoteName('id') . ' = :id')
  ->bind(':id', 42, ParameterType::INTEGER);
$db->setQuery($query2);
$db->execute();
```

---

## Seguridad en Consultas

### Reglas de Oro

1. **SIEMPRE usa `quoteName()` para identificadores** (tablas, campos):
```php
// CORRECTO
$db->quoteName('title')
$db->quoteName('#__content')

// INCORRECTO
"title"
'#__content'
```

2. **SIEMPRE usa `bind()` para valores**:
```php
// CORRECTO
->where($db->quoteName('username') . ' = :username')
->bind(':username', $user_input, ParameterType::STRING)

// INCORRECTO - NUNCA hagas esto
->where("username = '$user_input'")
```

3. **Especifica tipos de parámetros**:
```php
// CORRECTO
->bind(':id', $id, ParameterType::INTEGER)
->bind(':name', $name, ParameterType::STRING)

// Menos seguro (sin especificar tipo)
->bind(':id', $id)
```

### Prevención de Inyección SQL

```php
// VULNERABLE
$title = "'; DROP TABLE #__content; --";
$query->where("title = '$title'"); // ¡MAL!

// SEGURO con Prepared Statements
$query->where($db->quoteName('title') . ' = :title')
  ->bind(':title', $title, ParameterType::STRING);
```

### Validación de Entrada

Aunque prepared statements protegen, valida también:

```php
$search = htmlspecialchars($search);
$id = (int) $_GET['id']; // Convertir a entero

$query->bind(':search', '%' . $search . '%', ParameterType::STRING)
  ->bind(':id', $id, ParameterType::INTEGER);
```

---

## Casos de Uso Completos

### Listar Artículos de Categoría con Paginación

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

### Búsqueda Avanzada

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

## Resumen de Buenas Prácticas

- Usa `$this->getDatabase()` en modelos
- Siempre usa `quoteName()` para identificadores
- Siempre usa `bind()` con prepared statements
- Especifica `ParameterType` en bind()
- Encadena métodos para código limpio
- Valida entrada antes de usar en queries
- Maneja excepciones en try-catch
- Prueba queries complejas en phpMyAdmin primero
- Documenta queries complejas con comentarios
- Usa alias de tabla cortos pero claros

---

**Versión:** 1.0
**Última actualización:** 2024
**Compatibilidad:** Joomla 5.x, 6.x
**Nivel:** Intermedio-Avanzado
