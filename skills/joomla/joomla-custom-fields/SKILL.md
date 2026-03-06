---
name: joomla-custom-fields
description: Domina los campos personalizados en Joomla 5/6. Aprende a crear, gestionar y renderizar Custom Fields. Utiliza FieldsHelper para acceso programático, consulta #__fields y #__fields_values, implementa overrides de templates, integra en módulos y componentes. Triggers internos&#58; campo personalizado joomla, custom field, FieldsHelper, #__fields, campos artículos joomla, field group joomla.
---

# Custom Fields en Joomla 5/6

Domina los campos personalizados en Joomla. Los Custom Fields permiten agregar atributos adicionales a artículos, usuarios, contactos y categorías sin necesidad de extender el core. Ofrecen 16 tipos diferentes con control de acceso y validación nativa.

## Inicio Rápido

Carga campos personalizados en tu código:

```php
// Registra y carga el helper
JLoader::register('FieldsHelper', JPATH_ADMINISTRATOR . '/components/com_fields/helpers/fields.php');

// Obtén campos de un artículo
$fields = FieldsHelper::getFields('com_content.article', $article, true);

// Renderiza cada campo
foreach ($fields as $field) {
    echo $field->label . ': ' . $field->value;
}
```

## Los 16 Tipos de Campos

**Campos de Texto:** Text (una línea), Textarea (multilínea), Editor (WYSIWYG completo)

**Campos de Selección:** List (lista simple), Checkboxes (múltiples), Radio (opción única), User (selector usuarios), User Groups (selector grupos)

**Campos Multimedia:** Media (selector de archivos), List of images (galería), Color (color picker)

**Campos Especializados:** Calendar (fecha/hora), Integer (números enteros), URL (URLs validadas), SQL (consultas dinámicas), Repeatable (múltiples instancias)

Cada tipo ofrece parámetros específicos. Los parámetros comunes incluyen: Label (etiqueta), Required (obligatorio), Filter (NOHTML, RAW, SAFEHTML), Default value (valor por defecto), Access (nivel acceso).

## API FieldsHelper

Accede programáticamente a campos usando FieldsHelper. Este helper centraliza toda la lógica de campos personalizados.

**FieldsHelper::getFields()** devuelve array de campos para un elemento:

```php
// Firma: getFields($context, $item, $asArray = true)
$fields = FieldsHelper::getFields('com_content.article', $article, true);

// Contextos comunes
// com_content.article - Artículos
// com_content.categories - Categorías
// com_users.user - Usuarios
// com_contact.contact - Contactos
```

Propiedades del objeto campo: `$field->id`, `$field->name` (técnico), `$field->label`, `$field->type`, `$field->value` (renderizado HTML), `$field->rawvalue` (valor crudo).

**FieldsHelper::render()** renderiza HTML de un campo:

```php
foreach ($this->item->jcfields as $field) {
    echo FieldsHelper::render($field->context, 'field.render', array('field' => $field));
}
```

## Crear Campos desde Admin

Navega a Contenido → Campos (o Usuarios → Campos, Contactos → Campos).

1. Haz clic en "Nuevo"
2. Completa: Title (nombre visible), Name (técnico), Type (selecciona tipo)
3. En Field Group asigna a un grupo si deseas (organiza en pestañas)
4. En Category limita a categorías específicas (opcional)
5. Define parámetros específicos del tipo (máx. caracteres, opciones, etc.)
6. Configura validación: Filter y reglas personalizadas
7. Establece Access para control de permisos
8. Publica el campo (state = Published)

Los Field Groups se crean en Contenido → Field Groups. Agrupan campos en pestañas para mejorar UX. Sin grupo asignado, los campos aparecen en pestaña "Fields".

## Bases de Datos

Estructura de tablas principales:

**#__fields** almacena definiciones:
- `id` - Identificador único
- `context` - Contexto (com_content.article, etc.)
- `name` - Nombre técnico (snake_case)
- `label` - Etiqueta visible
- `type` - Tipo de campo
- `params` - Configuración JSON
- `access` - Nivel de acceso
- `state` - 1=publicado

**#__fields_values** almacena valores:
- `id` - Identificador único
- `field_id` - Referencia a #__fields
- `item_id` - ID del elemento (artículo, usuario, etc.)
- `value` - Valor almacenado (JSON para múltiples)

Consulta típica con JDatabase:

```php
$db = Factory::getContainer()->get(DatabaseInterface::class);
$query = $db->getQuery(true)
    ->select(['fv.*', 'f.name', 'f.label', 'f.type'])
    ->from($db->quoteName('#__fields_values', 'fv'))
    ->innerJoin($db->quoteName('#__fields', 'f') . ' ON fv.field_id = f.id')
    ->where($db->quoteName('vf.item_id') . ' = ' . (int)$itemId)
    ->where($db->quoteName('f.context') . ' = ' . $db->quote('com_content.article'));
$db->setQuery($query);
$results = $db->loadObjectList();
```

## Renderizado en Templates

Accede a campos renderizados mediante `$this->item->jcfields`. Este array contiene todos los campos ya cargados.

**Método 1: Renderizado básico con FieldsHelper**

```php
<?php foreach ($this->item->jcfields as $field) : ?>
    <div class="field field-<?php echo htmlspecialchars($field->type); ?>">
        <label><?php echo htmlspecialchars($field->label); ?></label>
        <div class="field-value">
            <?php echo $field->value; ?>
        </div>
    </div>
<?php endforeach; ?>
```

**Método 2: Acceso directo por nombre**

```php
<?php
// Crea índice por nombre
foreach($this->item->jcfields as $jcfield) {
    $this->item->jcFields[$jcfield->name] = $jcfield;
}

// Accede directamente
echo $this->item->jcFields['mi_campo']->value;
?>
```

**Crea overrides** en: `templates/tutemplate/html/layouts/com_content/fields/render.php`

En el override personaliza el HTML generado. Cada campo renderizado pasa por este layout.

## Uso en Módulos

En el helper del módulo carga campos de artículos:

```php
<?php
class ModTuModuloHelper {
    public static function getArticleWithFields($articleId) {
        $model = Factory::getApplication()->bootComponent('com_content')
            ->getMVCFactory()
            ->createModel('Article', 'Administrator');
        $article = $model->getItem($articleId);

        JLoader::register('FieldsHelper', JPATH_ADMINISTRATOR . '/components/com_fields/helpers/fields.php');
        $article->jcfields = FieldsHelper::getFields('com_content.article', $article, true);

        return $article;
    }
}
?>
```

En la vista del módulo renderiza:

```php
<?php foreach ($this->article->jcfields as $field) : ?>
    <div class="field-<?php echo $field->name; ?>">
        <strong><?php echo $field->label; ?></strong>:
        <?php echo $field->value; ?>
    </div>
<?php endforeach; ?>
```

## Integración en Componentes Personalizados

Implementa el evento `onContentPrepareForm` en un plugin para inyectar campos en tu componente:

```php
<?php
use Joomla\CMS\Form\Form;
use Joomla\CMS\Factory;

class PlgSystemTuComponente extends CMSPlugin {
    public function onContentPrepareForm($form, $data) {
        if (!($form instanceof Form)) {
            return true;
        }

        $context = isset($data->context) ? $data->context :
                   Factory::getApplication()->input->getCmd('option') . '.' .
                   Factory::getApplication()->input->getCmd('view');

        // Verifica contexto de tu componente
        if (strpos($context, 'com_micomponente.mielemento') === 0) {
            JLoader::register('FieldsHelper', JPATH_ADMINISTRATOR . '/components/com_fields/helpers/fields.php');
            FieldsHelper::getFields('com_micomponente.mielemento', $data, false);
        }

        return true;
    }
}
?>
```

## Validación y Filtros

Define validación en la configuración del campo usando el parámetro `validate`:

```
validate="required" - Campo obligatorio
validate="integer" - Solo números enteros
validate="integer:1,100" - Rango numérico
validate="email" - Formato email
validate="url" - URL válida
validate="color" - Color válido
```

Aplicar filtros mediante Filter:

```
NOHTML - Sin etiquetas HTML
RAW - Valor sin procesar
SAFEHTML - HTML seguro (filtra scripts)
STRING - String simple
WORD - Solo palabras
ALNUM - Alfanumérico
```

## Eventos del Sistema

Joomla dispara eventos en el ciclo de vida de campos:

**onContentPrepareForm** - Antes de mostrar formulario (inyecta campos)

**onContentPrepareData** - Después de cargar datos (prepara para renderizar)

**onContentValidateForm** - Validación server-side

**onContentAfterSave** - Después de guardar (procesa valores)

Estos eventos permiten interceptar el flujo y aplicar lógica personalizada. Implementa en un plugin de sistema.

## Acceso Directo a Base de Datos

Para consultas avanzadas accede directamente a #__fields y #__fields_values:

```php
// Obtén todos los campos de un artículo
$db = Factory::getContainer()->get(DatabaseInterface::class);
$query = $db->getQuery(true)
    ->select('*')
    ->from($db->quoteName('#__fields_values'))
    ->where($db->quoteName('item_id') . ' = ' . (int)$articleId);
$db->setQuery($query);
$values = $db->loadObjectList('field_id');

// Guarda un valor de campo
$obj = new stdClass();
$obj->field_id = 5;
$obj->item_id = 123;
$obj->value = 'Mi valor';
$db->insertObject('#__fields_values', $obj);
```

## Contextos Soportados

Los contextos válidos para getFields() son:

```
com_content.article - Artículos
com_content.categories - Categorías de contenido
com_users.user - Perfiles de usuario
com_contact.contact - Contactos
```

Cada contexto posee sus propias definiciones de campos. Verifica el contexto antes de cargar campos.

## Campos en Editores Frontend

En formularios frontend (registro usuario, envío artículo), el sistema carga automáticamente campos si:

1. El campo está publicado (state = 1)
2. La categoría coincide (si está limitada)
3. El usuario tiene acceso (nivel de acceso)

Los campos aparecen automáticamente en el formulario. Para renderizar manualmente usa el mismo FieldsHelper.

## Mejores Prácticas

**Nomenclatura:** Usa snake_case para nombres técnicos (`mi_campo`, no `miCampo`)

**Organización:** Agrupa campos relacionados en Field Groups para mejorar UX en editor

**Permisos:** Establece Access adecuadamente (Registered, Special, etc.)

**Validación:** Define siempre validación server-side en componente

**Performance:** Cachea resultados de getFields() si se llama múltiples veces

**Documentación:** Documenta qué campos requiere tu componente

**Testing:** Prueba en frontend y backend que campos se muestren correctamente

## Troubleshooting

**Campos no aparecen:** Verifica que estado = published, categoría coincida, nivel de acceso sea visible

**Valores no se guardan:** Comprueba que onContentPrepareData esté inyectando valores en objeto

**Errores en renderizado:** Valida que jcfields contenga objetos correctos, no null

**Performance lenta:** Reduce cantidad de campos cargados, implementa caché

**Permisos denegados:** Verifica access level del usuario vs field access

Ver referencias/ para ejemplos completos de módulos y componentes.
