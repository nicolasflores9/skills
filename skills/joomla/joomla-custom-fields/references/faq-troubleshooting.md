# FAQ y Troubleshooting: Custom Fields en Joomla 5/6

## Preguntas Frecuentes

### P: ¿Cómo cargo campos personalizados en un componente que no es core?

R: Implementa un plugin de sistema que responda a `onContentPrepareForm` y llame a `FieldsHelper::getFields()`. El contexto debe ser único para tu componente (ejemplo: `com_micomponente.mielemento`).

```php
$context = 'com_micomponente.mielemento';
JLoader::register('FieldsHelper', JPATH_ADMINISTRATOR . '/components/com_fields/helpers/fields.php');
$fields = FieldsHelper::getFields($context, $data, false);
```

### P: ¿Puedo crear campos compartidos entre múltiples contextos?

R: No directamente. Cada contexto tiene su propia definición de campos. Sin embargo, puedes crear un plugin que replique campos manualmente o usar el mismo nombre en múltiples contextos.

### P: ¿Cómo accedo a valores crudos sin renderizar?

R: Usa la propiedad `$field->rawvalue` en lugar de `$field->value`:

```php
// HTML renderizado
echo $field->value;

// Valor sin procesar
echo $field->rawvalue;
```

### P: ¿Puedo validar campos personalizados server-side?

R: Sí, implementa `onContentValidateForm` en un plugin:

```php
public function onContentValidateForm($form, $data) {
    // Valida campos personalizados
    if (empty($data->mi_campo)) {
        $form->setError('El campo es requerido');
        return false;
    }
    return true;
}
```

### P: ¿Cómo almaceno múltiples valores en un campo?

R: Los campos como Checkboxes y List (múltiple) almacenan valores como JSON:

```php
// Campo con múltiples valores
$value = ['opcion1', 'opcion2', 'opcion3'];
$db->setQuery(
    "INSERT INTO #__fields_values (field_id, item_id, value)
     VALUES ($fieldId, $itemId, " . $db->quote(json_encode($value)) . ")"
);
```

### P: ¿Cuál es la diferencia entre "Automatic Display" y renderizar manualmente?

R: **Automatic Display:** Joomla renderiza automáticamente el campo en el frontend (si está configurado). **Manual:** Tú controlas cuándo y cómo se muestra usando `FieldsHelper::render()` o tu propio HTML.

### P: ¿Puedo limitar un campo a usuarios específicos?

R: Usa el parámetro **Access** en la configuración del campo. Selecciona el nivel de acceso (Public, Registered, Special, o nivel personalizado).

### P: ¿Cómo migro campos de una Joomla a otra?

R: Exporta las tablas #__fields y #__fields_groups:

```sql
-- Exporta
mysqldump db_origen #__fields #__fields_groups #__fields_values > campos.sql

-- Importa
mysql db_destino < campos.sql

-- Verifica que los contextos sean válidos en el destino
```

### P: ¿Puedo crear un campo con opciones dinámicas desde base de datos?

R: Usa un campo de tipo **SQL** y proporciona una consulta que devuelva los valores:

```sql
SELECT id, name FROM #__categories WHERE state = 1
```

O crea un tipo de campo personalizado (más avanzado).

### P: ¿Cómo elimino un campo sin perder datos?

R: Simplemente desactiva el campo (state = 0) en lugar de eliminarlo. Los datos se conservan en #__fields_values. Puedes reactivarlo después.

```sql
UPDATE #__fields SET state = 0 WHERE id = X;
```

Para eliminar definitivamente:

```sql
DELETE FROM #__fields WHERE id = X;  -- Los valores se eliminan automáticamente (ON DELETE CASCADE)
```

### P: ¿Puedo reordenar campos en el formulario?

R: Usa Field Groups. Los campos se muestran en orden dentro de cada grupo. Sin grupo asignado, se muestran en la pestaña "Fields" en orden de creación.

### P: ¿Cómo cacheo resultados de campos para mejorar performance?

R: Usa JCache:

```php
$cache = Factory::getContainer()->get('cache');
$key = 'article_fields_' . $articleId;
$fields = $cache->get($key);

if (!$fields) {
    $fields = FieldsHelper::getFields('com_content.article', $article, true);
    $cache->store($fields, $key, 3600);  // 1 hora
}
```

### P: ¿Cuál es el máximo número de campos por elemento?

R: Técnicamente no hay límite duro, pero la UI se vuelve lenta con >100 campos. Usa Field Groups para organizar mejor.

---

## Troubleshooting

### Problema: Los campos no aparecen en el formulario

**Causas posibles:**

1. **Campo no publicado:** Verifica que `state = 1` en #__fields
2. **Categoría limitada:** Si el campo está limitado a categorías, verifica que corresponda
3. **Nivel de acceso:** El usuario no tiene permiso para ver el campo (comprueba `access`)
4. **Contexto incorrecto:** El contexto del formulario no coincide con el contexto del campo

**Solución:**

```sql
SELECT * FROM #__fields
WHERE context = 'com_content.article'
  AND state = 1
  AND access <= [nivel_usuario];
```

### Problema: Los valores de campos no se guardan

**Causas posibles:**

1. **onContentPrepareData no inyecta valores:** El plugin no está preparando los datos correctamente
2. **Campos_values no se actualiza:** El usuario no tiene permiso para editar campos
3. **Valor con formato incorrecto:** El valor no es string o JSON válido

**Solución:**

En el plugin, asegúrate de que `onContentPrepareData` inyecte los valores:

```php
public function onContentPrepareData($context, $data) {
    if (strpos($context, 'com_content.article') === 0) {
        // Asegúrate de que los campos estén disponibles
        if (!isset($data->jcfields)) {
            $data->jcfields = [];
        }
    }
}
```

### Problema: El template override no funciona

**Verificaciones:**

1. **Ubicación correcta:** `templates/[template]/html/layouts/com_content/fields/render.php`
2. **Syntax válido:** Comprueba que el PHP sea válido (sin errores de parse)
3. **Cache limpiado:** Limpia el caché de Joomla (Sistema → Caché)
4. **Override creado correctamente:** Usa el gestor de templates para crear/editar

**Debug:**

```php
<?php
// Agrega esto en el override para verificar
error_log('Override ejecutado para campo: ' . $displayData['field']->name);
?>
```

Revisa `/logs/everything.php` para ver si el override se ejecuta.

### Problema: Campo aparece pero sin valor

**Causas:**

1. **Valor es NULL o vacío:** Verifica #__fields_values
2. **rawvalue vs value:** Estás usando `$field->value` en lugar de `$field->rawvalue`
3. **Filtro aplicado incorrectamente:** El filtro elimina el contenido

**Solución:**

```php
// Verifica directamente en BD
SELECT * FROM #__fields_values
WHERE field_id = X AND item_id = Y;

// En código, usa rawvalue
echo $field->rawvalue;  // Sin filtros
```

### Problema: Campos lentos en grandes tablas

**Causas:**

- Muchos elementos con muchos campos
- Consultas sin índices apropiados
- Cargando todo en memoria

**Soluciones:**

1. **Caché los resultados:**
   ```php
   $fields = Cache::get('article_fields_' . $id)
              ?: FieldsHelper::getFields(...);
   ```

2. **Limita los campos cargados:**
   ```php
   // En lugar de cargar todos, carga solo los necesarios
   $query = $db->getQuery(true)
       ->select(['fv.*', 'f.name'])
       ->from('#__fields_values fv')
       ->innerJoin('#__fields f...')
       ->where('f.name IN (' . $db->quote(['campo1', 'campo2']) . ')');
   ```

3. **Usa paginación** si muestras muchos elementos.

### Problema: Campo repetible no funciona correctamente

**Verificación:**

- El JSON es válido: `json_decode($field->value)` funciona
- Accedes correctamente al array:
  ```php
  $items = json_decode($field->value, true);
  foreach ($items as $item) {
      echo $item['nombre'];
  }
  ```

### Problema: Permisos de acceso no funcionan

**Verificación:**

```php
// Comprueba niveles de acceso del usuario
$user = Factory::getUser();
$userLevels = $user->getAuthorisedViewLevels();

// Verifica si field->access está en esos niveles
if (in_array($field->access, $userLevels)) {
    // Usuario puede ver el campo
}
```

### Problema: Campo de media no muestra imagen

**Causas:**

1. **Ruta incorrecta:** El valor contiene ruta relativa pero necesita absoluta
2. **Archivo eliminado:** La imagen fue borrada pero el campo sigue referenciándola
3. **Permisos:** Usuario no tiene permiso para ver el directorio

**Solución:**

```php
// Construye ruta completa si es necesario
$imagePath = $field->value;
if (strpos($imagePath, 'http') === false) {
    $imagePath = JURI::base() . $imagePath;
}

echo '<img src="' . htmlspecialchars($imagePath) . '" />';
```

### Problema: Validación personalizada no se ejecuta

**Verificaciones:**

1. **Archivo de validación existe:** `/components/com_mycomponent/models/rules/miregla.php`
2. **Nombre correcto:** Debe ser `JFormRuleMiregla`
3. **En el campo:** `validate="miregla"`

```php
// Estructura correcta
class JFormRuleMiregla extends JFormRule {
    public function test(SimpleXMLElement $element, $value, $group = null, Registry $input = null, $form = null) {
        // Retorna true si válido, false si inválido
        return preg_match('/^[A-Z0-9]+$/', $value) === 1;
    }
}
```

### Problema: "Field not found" en REST API

**Verificación:**

- El campo está publicado
- El contexto es correcto
- El usuario tiene acceso al contexto

**Solución:**

Expón campos manualmente en respuesta JSON:

```php
$fields = FieldsHelper::getFields('com_content.article', $article, false);
$response['custom_fields'] = [];

foreach ($fields as $field) {
    $response['custom_fields'][$field->name] = $field->rawvalue;
}
```

### Problema: Upgrade de Joomla rompe campos

**Prevención:**

1. Haz backup antes de actualizar
2. Verifica que los contextos aún sean válidos post-upgrade
3. Limpia caché después de actualizar

**Recuperación:**

```sql
-- Verifica integridad
SELECT f.*, COUNT(fv.id) as values_count
FROM #__fields f
LEFT JOIN #__fields_values fv ON f.id = fv.field_id
GROUP BY f.id;

-- Elimina valores orfanados (field_id sin coincidencia en #__fields)
DELETE FROM #__fields_values
WHERE field_id NOT IN (SELECT id FROM #__fields);
```

---

## Herramientas de Debug

### 1. Inspeccionar Campos Cargados

```php
<?php
$item = Factory::getUser();
echo '<pre>';
var_dump($item->jcfields);
echo '</pre>';
?>
```

### 2. Verificar BD Directamente

```sql
-- Todos los campos disponibles
SELECT * FROM #__fields ORDER BY context, label;

-- Valores de un elemento
SELECT f.name, f.label, fv.value
FROM #__fields_values fv
JOIN #__fields f ON fv.field_id = f.id
WHERE fv.item_id = [ID];
```

### 3. Logs de Sistema

Verifica `/administrator/logs/everything.php` para errores de carga de campos.

### 4. Herramientas de Terceros

- **System Information:** Menú Sistema → Información del Sistema → Debug
- **Joomla Debug Bar:** Extensión que muestra queries y variables
- **Database Debugger:** Ver queries SQL ejecutadas

---

## Checklist de Deployment

Antes de ir a producción con campos personalizados:

- [ ] Campos están publicados (state = 1)
- [ ] Permisos de acceso configurados correctamente
- [ ] Valores por defecto definidos
- [ ] Validación server-side implementada
- [ ] Template override probado en navegador
- [ ] Performance probado con datos reales
- [ ] Backup de BD realizado
- [ ] Tests en navegadores diferentes
- [ ] Documentación actualizada
- [ ] Plan de rollback en caso de error
