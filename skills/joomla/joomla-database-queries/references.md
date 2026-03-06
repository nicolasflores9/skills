# Referencia Extendida: Joomla 5/6 - Consultas a Base de Datos

## APIs Completas

### DatabaseInterface - Métodos Principales

```php
// Obtención de instancia
$db = Factory::getContainer()->get(DatabaseInterface::class);

// Métodos de Query
$query = $db->getQuery(true);           // Nueva query
$db->setQuery($query);                  // Establece query actual
$db->execute();                         // Ejecuta query

// Métodos de Resultado
$results = $db->loadObjectList();       // Array de stdClass
$result = $db->loadObject();            // Un objeto
$assoc = $db->loadAssocList();          // Array asociativos
$row = $db->loadAssoc();                // Un array asociativo
$column = $db->loadColumn();            // Una columna
$value = $db->loadResult();             // Un valor único

// Métodos de Conveniencia
$db->insertObject($table, $object);     // Insertar objeto
$db->updateObject($table, $object);     // Actualizar objeto

// Métodos de Seguridad
$db->quoteName($identifier);            // Escapa identificadores
$db->quote($value);                     // Escapa valores (legacy)

// Información
$db->getPrefix();                       // Obtiene prefijo
$db->countAffected();                   // Filas afectadas última query
$db->getLastError();                    // Último error
```

### QueryInterface - Métodos de Construcción

```php
// SELECT
$query->select($columns);               // Especifica columnas
$query->distinct();                     // DISTINCT
$query->from($table);                   // FROM
$query->where($condition);              // WHERE (múltiple)
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
$query->insert($table);                 // Inicia INSERT
$query->columns($columns);              // Columnas INSERT
$query->values($values);                // Valores INSERT

// UPDATE
$query->update($table);                 // Inicia UPDATE
$query->set($assignments);              // SET

// DELETE
$query->delete($table);                 // Inicia DELETE

// Parámetros
$query->bind($key, $value, $type);      // Vincula parámetro
$query->bindArray($array);              // Vincula array
```

---

## Patrones de Código Recomendados

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

### Pattern 2: Query Builder con Filtros Dinámicos

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

        // Copiar condiciones WHERE desde query original
        $whereString = (string) $this->query->where;
        if ($whereString) {
            $countQuery->where($whereString);
        }

        $this->db->setQuery($countQuery);
        return $this->db->loadResult();
    }
}

// Uso:
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

## Debugging y Logging

### Logging de Queries Ejecutadas

```php
// En componente o modelo
$query = $db->getQuery(true)
    ->select('*')
    ->from($db->quoteName('#__content'));

$db->setQuery($query);

// Ver SQL antes de ejecutar
error_log("SQL: " . $query->__toString());

$results = $db->loadObjectList();

// Logging en Joomla
use Joomla\CMS\Log\Log;

Log::add('Query SQL: ' . $query->__toString(), Log::INFO, 'joomla');
Log::add('Error: ' . $db->getLastError(), Log::ERROR, 'joomla');
```

### Análisis de Rendimiento

```php
// Medir tiempo de query
$start = microtime(true);

$db->setQuery($query);
$results = $db->loadObjectList();

$elapsed = microtime(true) - $start;
error_log("Query executada en: " . $elapsed . " segundos");

// Registrar queries lentas
if ($elapsed > 0.5) {
    Log::add(
        "Query lenta: " . $query->__toString() . " (" . $elapsed . "s)",
        Log::WARNING,
        'joomla'
    );
}
```

---

## Diferencias entre Versiones

### Joomla 3.x vs 4.x vs 5.x

| Feature | Joomla 3.x | Joomla 4.x | Joomla 5.x |
|---------|-----------|-----------|-----------|
| Factory::getDbo() | Estándar | Disponible | **Deprecated** |
| Prepared Statements | Opcional | Recomendado | **Obligatorio** |
| quoteName() | Disponible | Mejorado | Estándar |
| bind() | No existe | Disponible | **Estándar** |
| ParameterType | No existe | Disponible | **Recomendado** |
| Query Chaining | Disponible | Estándar | Estándar |
| Container/DI | No existe | Sí | Sí |
| PHP Mínimo | 5.3 | 7.2 | **8.1** |

### Migración de Joomla 3.x a 5.x

**Viejo (Joomla 3.x):**
```php
$db = JFactory::getDbo();
$query = $db->getQuery(true);
$query->select('*')
  ->from($db->quoteName('#__content'))
  ->where('id = ' . $id); // Concatenación directa

$db->setQuery($query);
$results = $db->loadObjectList();
```

**Nuevo (Joomla 5.x):**
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

## Troubleshooting Común

### Error: "Call to undefined method bind()"

**Causa:** Olvidar usar `getQuery(true)` o usar query vieja

**Solución:**
```php
// INCORRECTO
$query = $db->getQuery();
$query->bind(':id', 5); // Error

// CORRECTO
$query = $db->getQuery(true);
$query->bind(':id', 5, ParameterType::INTEGER);
```

### Error: "Table '#__content' doesn't exist"

**Causa:** Prefijo incorrecto o no usar `#__`

**Solución:**
```php
// INCORRECTO - asume prefijo hardcoded
->from($db->quoteName('joomla_content'))

// CORRECTO - usa #__
->from($db->quoteName('#__content'))
```

### Error: "Syntax error in SQL query"

**Causa:** Concatenación de valores sin bind()

**Solución:**
```php
// INCORRECTO
$search = "test'; DROP TABLE users;--";
->where("title = '$search'")

// CORRECTO
->where($db->quoteName('title') . ' = :search')
->bind(':search', $search, ParameterType::STRING)
```

### Error: "Access denied for user"

**Causa:** Usuario BD sin permisos en tabla

**Solución:**
1. Verificar en phpMyAdmin permisos del usuario
2. Asegurar que usuario BD tiene permisos SELECT/INSERT/UPDATE/DELETE
3. Revisar configuración en `configuration.php`

---

## Checklist de Seguridad

Antes de usar query en producción:

- [ ] ¿Se usa `bind()` para todos los valores dinámicos?
- [ ] ¿Se usa `quoteName()` para todos los identificadores?
- [ ] ¿Se especifica `ParameterType` en cada `bind()`?
- [ ] ¿Se valida la entrada antes de pasar a query?
- [ ] ¿Se maneja excepciones con try-catch?
- [ ] ¿Se usa `getQuery(true)` para nueva query limpia?
- [ ] ¿Se evita `Factory::getDbo()` (deprecated)?
- [ ] ¿Se escapa HTML si se muestra en vista?
- [ ] ¿Se registran errores en el log?

---

## Recursos Oficiales

- Documentación Joomla 5: https://docs.joomla.org/
- API Joomla: https://api.joomla.org/
- Manual GitHub: https://github.com/joomla/Manual
- Seguridad: https://manual.joomla.org/docs/5.0/security/

---

**Última actualización:** Marzo 2024
**Versión Joomla:** 5.x, 6.x
