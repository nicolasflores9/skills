# Casos de Uso Prácticos: Custom Fields en Joomla 5/6

## Caso 1: Galería de Imágenes para Artículos

**Objetivo:** Permitir autores agregar múltiples imágenes a cada artículo con descripción.

### Campos Necesarios

**Campo 1: Imágenes** (Tipo: List of images)
```
Nombre: articulo_galeria
Etiqueta: Galería de Imágenes
Directorio: images/galeria
Múltiple: Sí
Requerido: No
Access: Public
```

**Campo 2: Descripciones** (Tipo: Repeatable)
```
Nombre: galeria_descripciones
Etiqueta: Descripciones de Imágenes
Campo interno: Textarea
Máximo items: 10
Requerido: No
```

### Implementación en Template

```php
<?php
// Archivo: templates/mytemplate/html/com_content/article/default.php

// Crear índice de campos
$fields = [];
foreach ($this->item->jcfields as $field) {
    $fields[$field->name] = $field;
}

// Mostrar galería
if (isset($fields['articulo_galeria']) && !empty($fields['articulo_galeria']->value)) {
    echo '<div class="article-gallery">';
    echo '<h3>Galería</h3>';

    $images = json_decode($fields['articulo_galeria']->value);
    $descriptions = [];

    if (isset($fields['galeria_descripciones'])) {
        $descriptions = json_decode($fields['galeria_descripciones']->value, true);
    }

    echo '<div class="gallery-grid">';
    foreach ($images as $index => $image) {
        $desc = $descriptions[$index]['descripcion'] ?? '';
        echo '<div class="gallery-item">';
        echo '<img src="' . htmlspecialchars($image) . '" alt="' . htmlspecialchars($desc) . '" />';
        if (!empty($desc)) {
            echo '<p class="gallery-caption">' . htmlspecialchars($desc) . '</p>';
        }
        echo '</div>';
    }
    echo '</div>';
    echo '</div>';
}
?>
```

## Caso 2: SEO Personalizado por Artículo

**Objetivo:** Agregar campos SEO específicos más allá de los estándar de Joomla.

### Campos Necesarios

```
1. meta_description - Text (140-160 caracteres)
2. keywords_seo - Textarea
3. canonical_url - URL Field
4. index_page - Radio (Index/Noindex)
5. follow_links - Radio (Follow/Nofollow)
```

### Plugin que Inyecta Meta Tags

```php
<?php
namespace MyPlugin\SystemSEO;

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Factory;
use Joomla\CMS\Document\HtmlDocument;

class SystemSEO extends CMSPlugin {

    public function onAfterRender() {
        $app = Factory::getApplication();
        $article = $app->getDocument()->getBuffer('component');

        if (!$article) {
            return;
        }

        $document = Factory::getApplication()->getDocument();

        if (!($document instanceof HtmlDocument)) {
            return;
        }

        // Obtén el artículo actual
        $view = $app->input->getCmd('view');
        $id = $app->input->getInt('id');

        if ($view !== 'article' || !$id) {
            return;
        }

        // Carga artículo con campos
        $model = $app->bootComponent('com_content')
            ->getMVCFactory()
            ->createModel('Article', 'Administrator');
        $article = $model->getItem($id);

        JLoader::register('FieldsHelper', JPATH_ADMINISTRATOR . '/components/com_fields/helpers/fields.php');
        $fields = FieldsHelper::getFields('com_content.article', $article, true);

        // Indexa campos
        $fieldMap = [];
        foreach ($fields as $field) {
            $fieldMap[$field->name] = $field;
        }

        // Meta description
        if (isset($fieldMap['meta_description'])) {
            $document->setMetaData('description', $fieldMap['meta_description']->rawvalue);
        }

        // Meta keywords
        if (isset($fieldMap['keywords_seo'])) {
            $document->setMetaData('keywords', $fieldMap['keywords_seo']->rawvalue);
        }

        // Canonical URL
        if (isset($fieldMap['canonical_url'])) {
            $document->addHeadLink($fieldMap['canonical_url']->rawvalue, 'canonical');
        }

        // Robots meta
        $robots = 'index, follow';
        if (isset($fieldMap['index_page']) && $fieldMap['index_page']->rawvalue === 'noindex') {
            $robots = 'noindex, follow';
        }
        if (isset($fieldMap['follow_links']) && $fieldMap['follow_links']->rawvalue === 'nofollow') {
            $robots = str_replace('follow', 'nofollow', $robots);
        }

        $document->setMetaData('robots', $robots);
    }
}
?>
```

## Caso 3: Información Adicional en Registro de Usuario

**Objetivo:** Recopilar información personalizada en el registro frontend.

### Campos para Usuarios

```
1. empresa - Text
2. puesto_laboral - Text
3. numero_telefono - Text (validación numérica)
4. sector_industria - List (Seleccionar sector)
5. politica_privacidad - Checkbox
6. interes_newsletter - Checkbox
```

### Plugin para Validación Frontend

```php
<?php
class PlgUserCustomFields extends CMSPlugin {

    public function onUserBeforeSave($user, $isnew, &$error) {
        $app = Factory::getApplication();

        // Validar campos personalizados
        if (!isset($user['numero_telefono']) || empty($user['numero_telefono'])) {
            $error[] = 'Teléfono es requerido';
            return false;
        }

        // Validar teléfono
        if (!preg_match('/^[0-9]{10,}$/', $user['numero_telefono'])) {
            $error[] = 'Teléfono inválido';
            return false;
        }

        // Validar política privacidad
        if (!isset($user['politica_privacidad']) || $user['politica_privacidad'] !== '1') {
            $error[] = 'Debes aceptar la política de privacidad';
            return false;
        }

        return true;
    }

    public function onUserAfterSave(&$user, $isnew, $success, &$msg) {
        if (!$success) {
            return false;
        }

        // Guardar campos personalizados
        $db = Factory::getContainer()->get(DatabaseInterface::class);

        $customFields = [
            'empresa' => $user->get('empresa'),
            'puesto_laboral' => $user->get('puesto_laboral'),
        ];

        foreach ($customFields as $fieldName => $value) {
            // Obtén field_id
            $query = $db->getQuery(true)
                ->select('id')
                ->from($db->quoteName('#__fields'))
                ->where($db->quoteName('name') . ' = ' . $db->quote($fieldName))
                ->where($db->quoteName('context') . ' = ' . $db->quote('com_users.user'));

            $db->setQuery($query);
            $fieldId = $db->loadResult();

            if ($fieldId) {
                // Guarda valor
                $this->saveFieldValue($fieldId, $user['id'], $value);
            }
        }

        return true;
    }

    private function saveFieldValue($fieldId, $userId, $value) {
        $db = Factory::getContainer()->get(DatabaseInterface::class);

        // Busca existente
        $query = $db->getQuery(true)
            ->select('id')
            ->from($db->quoteName('#__fields_values'))
            ->where($db->quoteName('field_id') . ' = ' . (int)$fieldId)
            ->where($db->quoteName('item_id') . ' = ' . (int)$userId);

        $db->setQuery($query);
        $existing = $db->loadResult();

        if ($existing) {
            $query = $db->getQuery(true)
                ->update($db->quoteName('#__fields_values'))
                ->set($db->quoteName('value') . ' = ' . $db->quote($value))
                ->where($db->quoteName('id') . ' = ' . (int)$existing);
            $db->setQuery($query);
        } else {
            $obj = new stdClass();
            $obj->field_id = (int)$fieldId;
            $obj->item_id = (int)$userId;
            $obj->value = $value;
            $db->insertObject('#__fields_values', $obj);
        }
    }
}
?>
```

## Caso 4: Tipología de Contenido por Categoría

**Objetivo:** Diferentes tipos de campos según la categoría del artículo.

### Configuración

- **Categoría "Recetas":** Campos para Ingredientes, Tiempo Preparación, Dificultad
- **Categoría "Noticias":** Campos para Fuente, Verificado, Editor
- **Categoría "Productos":** Campos para Precio, Stock, Proveedor

### Template Dinámico

```php
<?php
// Archivo: templates/mytemplate/html/com_content/article/default.php

$categoryId = $this->item->catid;
$fields = [];

foreach ($this->item->jcfields as $field) {
    $fields[$field->name] = $field;
}

switch ($categoryId) {
    case 5: // Recetas
        echo $this->renderRecipe($fields);
        break;

    case 3: // Noticias
        echo $this->renderNews($fields);
        break;

    case 7: // Productos
        echo $this->renderProduct($fields);
        break;

    default:
        echo $this->renderDefault($fields);
}

private function renderRecipe($fields) {
    $html = '<div class="recipe-content">';

    if (isset($fields['ingredientes'])) {
        $html .= '<h3>Ingredientes</h3>';
        $html .= '<ul>' . nl2br($fields['ingredientes']->value) . '</ul>';
    }

    if (isset($fields['tiempo_preparacion'])) {
        $html .= '<p class="time">' . htmlspecialchars($fields['tiempo_preparacion']->value) . ' minutos</p>';
    }

    if (isset($fields['dificultad'])) {
        $html .= '<p class="difficulty">' . htmlspecialchars($fields['dificultad']->value) . '</p>';
    }

    $html .= '</div>';
    return $html;
}

private function renderProduct($fields) {
    $html = '<div class="product-content">';

    if (isset($fields['precio'])) {
        $html .= '<p class="price">$' . htmlspecialchars($fields['precio']->value) . '</p>';
    }

    if (isset($fields['stock'])) {
        $stock = (int)$fields['stock']->rawvalue;
        $status = $stock > 0 ? '<span class="in-stock">Disponible</span>' : '<span class="out-stock">Agotado</span>';
        $html .= '<p>' . $status . '</p>';
    }

    $html .= '</div>';
    return $html;
}
?>
```

## Caso 5: Contactos con Información Extendida

**Objetivo:** Extender contactos con redes sociales, especialidades, etc.

### Campos para Contactos

```
1. especialidad - List
2. experiencia_anos - Integer
3. certificaciones - Repeatable (name, url)
4. linkedin - URL
5. twitter - Text (@usuario)
6. foto_perfil - Media
7. bio_extendida - Editor
```

### Módulo que Muestra Contacto

```php
<?php
// mod_contact_extended/helper.php

class ModContactExtendedHelper {

    public static function getContactWithFields($contactId) {
        $app = Factory::getApplication();

        // Obtén contacto
        $model = $app->bootComponent('com_contact')
            ->getMVCFactory()
            ->createModel('Contact', 'Administrator');

        $contact = $model->getItem((int)$contactId);

        if (!$contact) {
            return null;
        }

        // Carga campos
        JLoader::register('FieldsHelper', JPATH_ADMINISTRATOR . '/components/com_fields/helpers/fields.php');
        $contact->jcfields = FieldsHelper::getFields('com_contact.contact', $contact, true);

        return $contact;
    }
}
?>
```

```php
<?php
// mod_contact_extended/tmpl/default.php

if (!isset($this->contact) || !$this->contact) {
    return;
}

$fields = [];
foreach ($this->contact->jcfields as $field) {
    $fields[$field->name] = $field;
}
?>

<div class="contact-extended">
    <h2><?php echo htmlspecialchars($this->contact->name); ?></h2>

    <?php if (isset($fields['foto_perfil'])): ?>
        <img src="<?php echo htmlspecialchars($fields['foto_perfil']->value); ?>"
             alt="<?php echo htmlspecialchars($this->contact->name); ?>"
             class="contact-photo" />
    <?php endif; ?>

    <?php if (isset($fields['especialidad'])): ?>
        <p class="specialty">
            <strong>Especialidad:</strong> <?php echo htmlspecialchars($fields['especialidad']->value); ?>
        </p>
    <?php endif; ?>

    <?php if (isset($fields['experiencia_anos'])): ?>
        <p class="experience">
            <strong>Años de Experiencia:</strong> <?php echo htmlspecialchars($fields['experiencia_anos']->value); ?>
        </p>
    <?php endif; ?>

    <?php if (isset($fields['bio_extendida'])): ?>
        <div class="bio">
            <?php echo $fields['bio_extendida']->value; ?>
        </div>
    <?php endif; ?>

    <div class="social-links">
        <?php if (isset($fields['linkedin'])): ?>
            <a href="<?php echo htmlspecialchars($fields['linkedin']->value); ?>" target="_blank">LinkedIn</a>
        <?php endif; ?>

        <?php if (isset($fields['twitter'])): ?>
            <a href="https://twitter.com/<?php echo htmlspecialchars(str_replace('@', '', $fields['twitter']->value)); ?>" target="_blank">Twitter</a>
        <?php endif; ?>
    </div>
</div>
```

## Caso 6: Dashboard de Usuario con Campos Personalizados

**Objetivo:** Mostrar información personalizada en el perfil del usuario.

### Acceso a Campos en Perfil Frontend

```php
<?php
// En template de usuario

$user = Factory::getUser();

if (!$user->id) {
    echo 'No autenticado';
    return;
}

JLoader::register('FieldsHelper', JPATH_ADMINISTRATOR . '/components/com_fields/helpers/fields.php');
$fields = FieldsHelper::getFields('com_users.user', $user, true);

$fieldMap = [];
foreach ($fields as $field) {
    $fieldMap[$field->name] = $field;
}
?>

<div class="user-dashboard">
    <h1>Mi Perfil</h1>

    <div class="user-info">
        <p><strong>Nombre:</strong> <?php echo htmlspecialchars($user->name); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($user->email); ?></p>

        <?php if (isset($fieldMap['empresa'])): ?>
            <p><strong>Empresa:</strong> <?php echo htmlspecialchars($fieldMap['empresa']->value); ?></p>
        <?php endif; ?>

        <?php if (isset($fieldMap['puesto_laboral'])): ?>
            <p><strong>Puesto:</strong> <?php echo htmlspecialchars($fieldMap['puesto_laboral']->value); ?></p>
        <?php endif; ?>
    </div>
</div>
```

## Caso 7: REST API con Custom Fields

**Objetivo:** Exponer campos personalizados a través de REST API.

### Extensión de Component JSON Response

```php
<?php
// En tu componente, al cargar elemento para JSON

$article = $this->model->getItem($id);

JLoader::register('FieldsHelper', JPATH_ADMINISTRATOR . '/components/com_fields/helpers/fields.php');
$fields = FieldsHelper::getFields('com_content.article', $article, false);

// Agrega campos al response
$response = [];
foreach ($fields as $field) {
    $response['custom_fields'][$field->name] = $field->rawvalue;
}

return json_encode($response);
```

## Mejores Prácticas en Casos de Uso

1. **Validación:** Siempre valida server-side, no confíes solo en validación frontend
2. **Permisos:** Usa niveles de acceso apropiados para campos sensibles
3. **Performance:** Cachea resultados de getFields() si se llama múltiples veces
4. **JSON:** Para datos complejos, usa JSON y json_decode en PHP
5. **Documentación:** Documenta qué campos requiere cada componente/módulo
6. **Testing:** Prueba campos con diferentes tipos de datos y permisos
