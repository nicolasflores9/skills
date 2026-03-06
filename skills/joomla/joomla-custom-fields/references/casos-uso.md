# Practical Use Cases: Custom Fields in Joomla 5/6

## Case 1: Image Gallery for Articles

**Objective:** Allow authors to add multiple images to each article with descriptions.

### Required Fields

**Field 1: Images** (Type: List of images)
```
Name: articulo_galeria
Label: Image Gallery
Directory: images/galeria
Multiple: Yes
Required: No
Access: Public
```

**Field 2: Descriptions** (Type: Repeatable)
```
Name: galeria_descripciones
Label: Image Descriptions
Internal field: Textarea
Max items: 10
Required: No
```

### Template Implementation

```php
<?php
// File: templates/mytemplate/html/com_content/article/default.php

// Create field index
$fields = [];
foreach ($this->item->jcfields as $field) {
    $fields[$field->name] = $field;
}

// Display gallery
if (isset($fields['articulo_galeria']) && !empty($fields['articulo_galeria']->value)) {
    echo '<div class="article-gallery">';
    echo '<h3>Gallery</h3>';

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

## Case 2: Custom SEO per Article

**Objective:** Add specific SEO fields beyond Joomla's standard ones.

### Required Fields

```
1. meta_description - Text (140-160 characters)
2. keywords_seo - Textarea
3. canonical_url - URL Field
4. index_page - Radio (Index/Noindex)
5. follow_links - Radio (Follow/Nofollow)
```

### Plugin that Injects Meta Tags

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

        // Get the current article
        $view = $app->input->getCmd('view');
        $id = $app->input->getInt('id');

        if ($view !== 'article' || !$id) {
            return;
        }

        // Load article with fields
        $model = $app->bootComponent('com_content')
            ->getMVCFactory()
            ->createModel('Article', 'Administrator');
        $article = $model->getItem($id);

        JLoader::register('FieldsHelper', JPATH_ADMINISTRATOR . '/components/com_fields/helpers/fields.php');
        $fields = FieldsHelper::getFields('com_content.article', $article, true);

        // Index fields
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

## Case 3: Additional Information in User Registration

**Objective:** Collect custom information during frontend registration.

### User Fields

```
1. empresa - Text
2. puesto_laboral - Text
3. numero_telefono - Text (numeric validation)
4. sector_industria - List (Select sector)
5. politica_privacidad - Checkbox
6. interes_newsletter - Checkbox
```

### Frontend Validation Plugin

```php
<?php
class PlgUserCustomFields extends CMSPlugin {

    public function onUserBeforeSave($user, $isnew, &$error) {
        $app = Factory::getApplication();

        // Validate custom fields
        if (!isset($user['numero_telefono']) || empty($user['numero_telefono'])) {
            $error[] = 'Phone number is required';
            return false;
        }

        // Validate phone
        if (!preg_match('/^[0-9]{10,}$/', $user['numero_telefono'])) {
            $error[] = 'Invalid phone number';
            return false;
        }

        // Validate privacy policy
        if (!isset($user['politica_privacidad']) || $user['politica_privacidad'] !== '1') {
            $error[] = 'You must accept the privacy policy';
            return false;
        }

        return true;
    }

    public function onUserAfterSave(&$user, $isnew, $success, &$msg) {
        if (!$success) {
            return false;
        }

        // Save custom fields
        $db = Factory::getContainer()->get(DatabaseInterface::class);

        $customFields = [
            'empresa' => $user->get('empresa'),
            'puesto_laboral' => $user->get('puesto_laboral'),
        ];

        foreach ($customFields as $fieldName => $value) {
            // Get field_id
            $query = $db->getQuery(true)
                ->select('id')
                ->from($db->quoteName('#__fields'))
                ->where($db->quoteName('name') . ' = ' . $db->quote($fieldName))
                ->where($db->quoteName('context') . ' = ' . $db->quote('com_users.user'));

            $db->setQuery($query);
            $fieldId = $db->loadResult();

            if ($fieldId) {
                // Save value
                $this->saveFieldValue($fieldId, $user['id'], $value);
            }
        }

        return true;
    }

    private function saveFieldValue($fieldId, $userId, $value) {
        $db = Factory::getContainer()->get(DatabaseInterface::class);

        // Check for existing value
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

## Case 4: Content Typology by Category

**Objective:** Different field types depending on the article category.

### Configuration

- **"Recipes" Category:** Fields for Ingredients, Preparation Time, Difficulty
- **"News" Category:** Fields for Source, Verified, Editor
- **"Products" Category:** Fields for Price, Stock, Supplier

### Dynamic Template

```php
<?php
// File: templates/mytemplate/html/com_content/article/default.php

$categoryId = $this->item->catid;
$fields = [];

foreach ($this->item->jcfields as $field) {
    $fields[$field->name] = $field;
}

switch ($categoryId) {
    case 5: // Recipes
        echo $this->renderRecipe($fields);
        break;

    case 3: // News
        echo $this->renderNews($fields);
        break;

    case 7: // Products
        echo $this->renderProduct($fields);
        break;

    default:
        echo $this->renderDefault($fields);
}

private function renderRecipe($fields) {
    $html = '<div class="recipe-content">';

    if (isset($fields['ingredientes'])) {
        $html .= '<h3>Ingredients</h3>';
        $html .= '<ul>' . nl2br($fields['ingredientes']->value) . '</ul>';
    }

    if (isset($fields['tiempo_preparacion'])) {
        $html .= '<p class="time">' . htmlspecialchars($fields['tiempo_preparacion']->value) . ' minutes</p>';
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
        $status = $stock > 0 ? '<span class="in-stock">Available</span>' : '<span class="out-stock">Out of Stock</span>';
        $html .= '<p>' . $status . '</p>';
    }

    $html .= '</div>';
    return $html;
}
?>
```

## Case 5: Contacts with Extended Information

**Objective:** Extend contacts with social media, specialties, etc.

### Contact Fields

```
1. especialidad - List
2. experiencia_anos - Integer
3. certificaciones - Repeatable (name, url)
4. linkedin - URL
5. twitter - Text (@username)
6. foto_perfil - Media
7. bio_extendida - Editor
```

### Module that Displays Contact

```php
<?php
// mod_contact_extended/helper.php

class ModContactExtendedHelper {

    public static function getContactWithFields($contactId) {
        $app = Factory::getApplication();

        // Get contact
        $model = $app->bootComponent('com_contact')
            ->getMVCFactory()
            ->createModel('Contact', 'Administrator');

        $contact = $model->getItem((int)$contactId);

        if (!$contact) {
            return null;
        }

        // Load fields
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
            <strong>Specialty:</strong> <?php echo htmlspecialchars($fields['especialidad']->value); ?>
        </p>
    <?php endif; ?>

    <?php if (isset($fields['experiencia_anos'])): ?>
        <p class="experience">
            <strong>Years of Experience:</strong> <?php echo htmlspecialchars($fields['experiencia_anos']->value); ?>
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

## Case 6: User Dashboard with Custom Fields

**Objective:** Display custom information in the user profile.

### Frontend Profile Field Access

```php
<?php
// In user template

$user = Factory::getUser();

if (!$user->id) {
    echo 'Not authenticated';
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
    <h1>My Profile</h1>

    <div class="user-info">
        <p><strong>Name:</strong> <?php echo htmlspecialchars($user->name); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($user->email); ?></p>

        <?php if (isset($fieldMap['empresa'])): ?>
            <p><strong>Company:</strong> <?php echo htmlspecialchars($fieldMap['empresa']->value); ?></p>
        <?php endif; ?>

        <?php if (isset($fieldMap['puesto_laboral'])): ?>
            <p><strong>Position:</strong> <?php echo htmlspecialchars($fieldMap['puesto_laboral']->value); ?></p>
        <?php endif; ?>
    </div>
</div>
```

## Case 7: REST API with Custom Fields

**Objective:** Expose custom fields through the REST API.

### Extending Component JSON Response

```php
<?php
// In your component, when loading an element for JSON

$article = $this->model->getItem($id);

JLoader::register('FieldsHelper', JPATH_ADMINISTRATOR . '/components/com_fields/helpers/fields.php');
$fields = FieldsHelper::getFields('com_content.article', $article, false);

// Add fields to the response
$response = [];
foreach ($fields as $field) {
    $response['custom_fields'][$field->name] = $field->rawvalue;
}

return json_encode($response);
```

## Best Practices for Use Cases

1. **Validation:** Always validate server-side, do not rely solely on frontend validation
2. **Permissions:** Use appropriate access levels for sensitive fields
3. **Performance:** Cache getFields() results if called multiple times
4. **JSON:** For complex data, use JSON and json_decode in PHP
5. **Documentation:** Document which fields each component/module requires
6. **Testing:** Test fields with different data types and permissions
