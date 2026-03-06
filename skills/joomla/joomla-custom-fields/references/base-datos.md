# Referencia de Base de Datos: Custom Fields en Joomla 5/6

## Tablas Principales

### #__fields - Definiciones de Campos

Almacena la configuración de cada campo personalizado.

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

**Campos:**
- `id` - ID único del campo
- `context` - Contexto (com_content.article, com_users.user, etc.)
- `name` - Nombre técnico (snake_case, único por contexto)
- `label` - Etiqueta visible en formularios
- `description` - Descripción / instrucciones
- `type` - Tipo de campo (text, textarea, list, media, etc.)
- `default_value` - Valor por defecto para nuevos elementos
- `params` - Configuración general en JSON
- `fieldparams` - Parámetros específicos del tipo en JSON
- `access` - Nivel de acceso Joomla (1=Public, 2=Registered, 3=Special, etc.)
- `state` - Publicado (1) o No publicado (0)
- `group_id` - ID del grupo de campos (Field Group)

### #__fields_values - Valores de Campos

Almacena los valores reales de campos para cada elemento.

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

**Campos:**
- `id` - ID único del valor
- `field_id` - Referencia a #__fields.id
- `item_id` - ID del elemento (artículo, usuario, etc.)
- `value` - Valor almacenado (JSON para múltiples valores)

### #__fields_groups - Grupos de Campos

Organiza campos en pestañas en el formulario de edición.

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

**Campos:**
- `id` - ID único del grupo
- `title` - Nombre visible en pestaña
- `description` - Descripción del grupo
- `context` - Contexto (com_content.article, etc.)
- `access` - Nivel de acceso
- `state` - Publicado (1) o No (0)
- `params` - Configuración adicional en JSON

## Ejemplos de Consultas Comunes

### 1. Obtener todos los campos de un artículo

```sql
SELECT fv.*, f.name, f.label, f.type
FROM `#__fields_values` fv
INNER JOIN `#__fields` f ON fv.field_id = f.id
WHERE fv.item_id = 123
  AND f.context = 'com_content.article'
  AND f.state = 1;
```

### 2. Obtener todas las definiciones de campos para un contexto

```sql
SELECT *
FROM `#__fields`
WHERE context = 'com_content.article'
  AND state = 1
ORDER BY label ASC;
```

### 3. Obtener campos agrupados con información del grupo

```sql
SELECT f.*, fg.title as group_title
FROM `#__fields` f
LEFT JOIN `#__fields_groups` fg ON f.group_id = fg.id
WHERE f.context = 'com_content.article'
  AND f.state = 1
ORDER BY f.group_id, f.label;
```

### 4. Campos con permisos visibles para un usuario

```sql
-- Asumir que el usuario tiene niveles de acceso [1, 2, 4]
SELECT *
FROM `#__fields`
WHERE context = 'com_content.article'
  AND state = 1
  AND access IN (1, 2, 4)
ORDER BY label ASC;
```

### 5. Obtener últimos valores modificados de un campo

```sql
SELECT fv.*, a.title
FROM `#__fields_values` fv
INNER JOIN `#__content` a ON fv.item_id = a.id
WHERE fv.field_id = 5
ORDER BY a.modified DESC
LIMIT 10;
```

### 6. Contar cuántos elementos tienen un campo completado

```sql
SELECT COUNT(DISTINCT item_id) as elementos_con_valor
FROM `#__fields_values`
WHERE field_id = 5 AND value IS NOT NULL AND value != '';
```

### 7. Buscar artículos por valor de campo

```sql
-- Buscar artículos donde "color_destacado" = "rojo"
SELECT DISTINCT a.*
FROM `#__content` a
INNER JOIN `#__fields_values` fv ON a.id = fv.item_id
INNER JOIN `#__fields` f ON fv.field_id = f.id
WHERE f.name = 'color_destacado'
  AND fv.value = 'rojo'
  AND f.context = 'com_content.article';
```

## Estructura JSON en Fields

### Parámetro `params`

Contiene configuración general del campo en JSON:

```json
{
    "class": "campo-personalizado",
    "hint": "Ingresa un valor",
    "required": false,
    "filter": "RAW",
    "automatic_display": "false"
}
```

### Parámetro `fieldparams`

Parámetros específicos del tipo de campo:

**Para tipo List:**
```json
{
    "options": "Opción 1\nOpción 2\nOpción 3",
    "multiple": false
}
```

**Para tipo Text:**
```json
{
    "maxlength": 255,
    "rows": 1,
    "cols": 50
}
```

**Para tipo Media:**
```json
{
    "directory": "images",
    "preview": "true"
}
```

### Campo `value` en fields_values

Para campos simples:
```
"valor simple"
```

Para campos múltiples (checkbox, list múltiple):
```json
["opcion1", "opcion2", "opcion3"]
```

Para campos repetibles:
```json
[
    {"nombre": "Item 1", "descripcion": "Descripción 1"},
    {"nombre": "Item 2", "descripcion": "Descripción 2"}
]
```

## Consultas en JDatabase (Joomla)

### Clase RepositorioDBCompleto

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
     * Obtén todos los campos de un artículo con sus valores
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
     * Guarda o actualiza un valor de campo
     */
    public function saveValue($fieldId, $itemId, $value) {
        // Busca si ya existe
        $query = $this->db->getQuery(true)
            ->select('id')
            ->from($this->db->quoteName('#__fields_values'))
            ->where($this->db->quoteName('field_id') . ' = ' . (int)$fieldId)
            ->where($this->db->quoteName('item_id') . ' = ' . (int)$itemId);

        $this->db->setQuery($query);
        $existing = $this->db->loadResult();

        if ($existing) {
            // Actualiza
            $query = $this->db->getQuery(true)
                ->update($this->db->quoteName('#__fields_values'))
                ->set($this->db->quoteName('value') . ' = ' . $this->db->quote(json_encode($value)))
                ->where($this->db->quoteName('id') . ' = ' . (int)$existing);

            $this->db->setQuery($query);
        } else {
            // Inserta
            $obj = new \stdClass();
            $obj->field_id = (int)$fieldId;
            $obj->item_id = (int)$itemId;
            $obj->value = json_encode($value);

            $this->db->insertObject('#__fields_values', $obj);
        }

        return true;
    }

    /**
     * Obtén campo por nombre
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
     * Elimina todos los valores de un elemento
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

## Consideraciones de Performance

1. **Índices:** Las tablas vienen optimizadas con índices en `context`, `state`, y la clave única en `context+name`.

2. **Joins:** Si necesitas muchos campos, considera cachear resultados con JCache:
   ```php
   $cache = Factory::getContainer()->get('cache');
   $key = 'article_fields_' . $articleId;
   $fields = $cache->get($key);

   if (!$fields) {
       $fields = FieldsHelper::getFields('com_content.article', $article, true);
       $cache->store($fields, $key, 3600); // 1 hora
   }
   ```

3. **Batch Operations:** Para múltiples actualizaciones, usa transacciones:
   ```php
   $this->db->transactionStart();

   foreach ($values as $fieldId => $value) {
       $this->saveValue($fieldId, $itemId, $value);
   }

   $this->db->transactionCommit();
   ```

## Versionado y Migración

Para cambios en estructura de campos, crea un script SQL en tu componente:

```sql
-- Agregar campo nuevo
INSERT INTO `#__fields` (context, name, label, type, state)
VALUES ('com_content.article', 'nuevo_campo', 'Nuevo Campo', 'text', 1);

-- Eliminar campo (cascade delete funciona automáticamente)
DELETE FROM `#__fields` WHERE id = X;

-- Modificar tipo de campo
UPDATE `#__fields` SET type = 'textarea' WHERE id = X;
```

Siempre realiza backups antes de modificar estructura de campos en producción.
