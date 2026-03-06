<?php
/**
 * PRACTICAL EXAMPLES OF CUSTOM FIELDS IN JOOMLA 5/6
 * ====================================================
 * Complete implementations for components, modules, and templates
 */

// ============================================================================
// EXAMPLE 1: LOAD CUSTOM FIELDS IN A COMPONENT
// ============================================================================

namespace MyComponent\Site\Model;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ItemModel;
use Joomla\Component\Fields\Administrator\Helper\FieldsHelper;

class ArticleModel extends ItemModel {

    public function getItem($pk = null) {
        $item = parent::getItem($pk);

        if ($item) {
            // Load the article's custom fields
            JLoader::register('FieldsHelper', JPATH_ADMINISTRATOR . '/components/com_fields/helpers/fields.php');
            $item->jcfields = FieldsHelper::getFields('com_content.article', $item, true);
        }

        return $item;
    }
}


// ============================================================================
// EXAMPLE 2: PLUGIN THAT INJECTS FIELDS INTO A CUSTOM COMPONENT
// ============================================================================

namespace MyPlugin\SystemCustomFields;

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Factory;
use Joomla\Component\Fields\Administrator\Helper\FieldsHelper;

class SystemCustomFields extends CMSPlugin {

    /**
     * Injects custom fields into forms
     */
    public function onContentPrepareForm($form, $data) {
        if (!($form instanceof Form)) {
            return true;
        }

        // Get the context
        $context = isset($data->context) ? $data->context :
                   Factory::getApplication()->input->getCmd('option') . '.' .
                   Factory::getApplication()->input->getCmd('view');

        // Inject fields only in valid contexts
        if (in_array($context, ['com_content.article', 'com_users.user'])) {
            JLoader::register('FieldsHelper', JPATH_ADMINISTRATOR . '/components/com_fields/helpers/fields.php');
            FieldsHelper::getFields($context, $data, false);
        }

        return true;
    }

    /**
     * Prepares data for field rendering
     */
    public function onContentPrepareData($context, $data) {
        if (empty($data)) {
            return true;
        }

        // Convert Registry data to arrays if needed
        if (method_exists($data, 'toArray')) {
            $dataArray = $data->toArray();
            foreach ($dataArray as $key => $value) {
                $data->$key = $value;
            }
        }

        return true;
    }
}


// ============================================================================
// EXAMPLE 3: MODULE THAT DISPLAYS CUSTOM FIELDS FROM AN ARTICLE
// ============================================================================

namespace MyModule\Site\Helper;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Factory\MVCFactory;
use Joomla\Component\Fields\Administrator\Helper\FieldsHelper;

class ModCustomFieldsHelper {

    /**
     * Get an article with its custom fields
     */
    public static function getArticleWithFields($articleId) {
        try {
            // Get the application and model
            $app = Factory::getApplication();
            $mvcFactory = $app->bootComponent('com_content')->getMVCFactory();
            $model = $mvcFactory->createModel('Article', 'Administrator');

            // Load the article
            $article = $model->getItem((int)$articleId);

            if (!$article) {
                return null;
            }

            // Register and load the fields helper
            JLoader::register('FieldsHelper', JPATH_ADMINISTRATOR . '/components/com_fields/helpers/fields.php');

            // Get rendered fields (HTML)
            $article->jcfields = FieldsHelper::getFields('com_content.article', $article, true);

            return $article;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get fields in an array indexed by name
     */
    public static function getFieldsByName($fields) {
        $indexed = [];

        if (is_array($fields)) {
            foreach ($fields as $field) {
                $indexed[$field->name] = $field;
            }
        }

        return $indexed;
    }
}


// ============================================================================
// EXAMPLE 4: DIRECT DATABASE QUERY FOR FIELDS
// ============================================================================

use Joomla\Database\DatabaseInterface;

class FieldValueRepository {

    private $db;

    public function __construct(DatabaseInterface $db) {
        $this->db = $db;
    }

    /**
     * Get all field values for an article
     */
    public function getArticleFieldValues($articleId) {
        $query = $this->db->getQuery(true)
            ->select(['fv.*', 'f.name', 'f.label', 'f.type'])
            ->from($this->db->quoteName('#__fields_values', 'fv'))
            ->innerJoin($this->db->quoteName('#__fields', 'f') . ' ON ' .
                       $this->db->quoteName('fv.field_id') . ' = ' .
                       $this->db->quoteName('f.id'))
            ->where($this->db->quoteName('fv.item_id') . ' = ' . (int)$articleId)
            ->where($this->db->quoteName('f.context') . ' = ' . $this->db->quote('com_content.article'))
            ->where($this->db->quoteName('f.state') . ' = 1');

        $this->db->setQuery($query);
        return $this->db->loadObjectList('name');
    }

    /**
     * Get all available fields for a context
     */
    public function getFieldsForContext($context) {
        $query = $this->db->getQuery(true)
            ->select('*')
            ->from($this->db->quoteName('#__fields'))
            ->where($this->db->quoteName('context') . ' = ' . $this->db->quote($context))
            ->where($this->db->quoteName('state') . ' = 1')
            ->order($this->db->quoteName('label') . ' ASC');

        $this->db->setQuery($query);
        return $this->db->loadObjectList();
    }

    /**
     * Save a field value
     */
    public function saveFieldValue($fieldId, $itemId, $value) {
        // First try to update
        $query = $this->db->getQuery(true)
            ->update($this->db->quoteName('#__fields_values'))
            ->set($this->db->quoteName('value') . ' = ' . $this->db->quote($value))
            ->where($this->db->quoteName('field_id') . ' = ' . (int)$fieldId)
            ->where($this->db->quoteName('item_id') . ' = ' . (int)$itemId);

        $this->db->setQuery($query);
        $this->db->execute();

        // If nothing was updated, insert
        if ($this->db->getAffectedRows() === 0) {
            $obj = new \stdClass();
            $obj->field_id = (int)$fieldId;
            $obj->item_id = (int)$itemId;
            $obj->value = $value;

            $this->db->insertObject('#__fields_values', $obj);
        }

        return true;
    }

    /**
     * Delete all values for an article
     */
    public function deleteArticleValues($articleId) {
        $query = $this->db->getQuery(true)
            ->delete($this->db->quoteName('#__fields_values'))
            ->where($this->db->quoteName('item_id') . ' = ' . (int)$articleId);

        $this->db->setQuery($query);
        return $this->db->execute();
    }
}


// ============================================================================
// EXAMPLE 5: TEMPLATE OVERRIDE FOR RENDERING FIELDS
// ============================================================================

// File: templates/mytemplate/html/layouts/com_content/fields/render.php
// This file is included when custom fields are rendered

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

$field = $displayData['field'] ?? null;

if (!$field) {
    return;
}

// Determine whether to render based on field type
$renderableTypes = ['text', 'textarea', 'editor', 'list', 'radio', 'checkbox', 'media', 'calendar'];

if (!in_array($field->type, $renderableTypes)) {
    return;
}
?>

<div class="field field-<?php echo htmlspecialchars($field->type); ?>"
     data-field-name="<?php echo htmlspecialchars($field->name); ?>">

    <?php if (!empty($field->label)): ?>
        <label class="field-label">
            <?php echo htmlspecialchars($field->label); ?>
        </label>
    <?php endif; ?>

    <div class="field-value">
        <?php
        // Render the value based on type
        if ($field->type === 'media') {
            // For media fields
            echo '<img src="' . htmlspecialchars($field->value) . '" alt="' . htmlspecialchars($field->label) . '" />';
        } elseif ($field->type === 'list') {
            // For lists
            echo htmlspecialchars($field->value);
        } else {
            // For other fields
            echo $field->value;
        }
        ?>
    </div>
</div>

<style>
    .field {
        margin-bottom: 15px;
    }

    .field-label {
        font-weight: bold;
        display: block;
        margin-bottom: 5px;
    }

    .field-value {
        padding: 5px;
    }
</style>


// ============================================================================
// EXAMPLE 6: CUSTOM FIELD VALIDATION
// ============================================================================

// File: /components/com_mycomponent/models/rules/codigopostal.php

use Joomla\CMS\Form\FormRule;
use Joomla\Registry\Registry;
use SimpleXMLElement;

class JFormRuleCodigopostal extends FormRule {

    /**
     * Validates a postal code
     * Expected format: ABC-123 (3 letters, hyphen, 3 numbers)
     */
    public function test(SimpleXMLElement $element, $value, $group = null, Registry $input = null, $form = null) {
        // If empty and not required, it is valid
        if (empty($value) && $element['required'] !== 'true') {
            return true;
        }

        // Validate format
        return preg_match('/^[A-Z]{3}-\d{3}$/', $value) === 1;
    }
}

// In the field definition, use:
// <field name="codigo_postal" type="text" validate="codigopostal" message="Format: ABC-123" />


// ============================================================================
// EXAMPLE 7: MODULE VIEW WITH FIELDS
// ============================================================================

// File: mod_custom_fields/tmpl/default.php

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
?>

<div class="custom-fields-module">
    <?php if (!empty($this->article) && is_array($this->article->jcfields)): ?>

        <h3><?php echo $this->article->title; ?></h3>

        <div class="fields-container">
            <?php foreach ($this->article->jcfields as $field): ?>

                <div class="field-item field-<?php echo htmlspecialchars($field->name); ?>">
                    <div class="field-label">
                        <?php echo htmlspecialchars($field->label); ?>:
                    </div>
                    <div class="field-content">
                        <?php echo $field->value; ?>
                    </div>
                </div>

            <?php endforeach; ?>
        </div>

    <?php else: ?>
        <p><?php echo JText::_('MOD_CUSTOM_FIELDS_NO_FIELDS'); ?></p>
    <?php endif; ?>
</div>

<style>
    .custom-fields-module {
        padding: 10px;
    }

    .field-item {
        border-bottom: 1px solid #ddd;
        padding: 10px 0;
    }

    .field-label {
        font-weight: bold;
        color: #333;
    }

    .field-content {
        margin-top: 5px;
        color: #666;
    }
</style>


// ============================================================================
// EXAMPLE 8: CREATE FIELD INDEX BY NAME IN TEMPLATE
// ============================================================================

<?php
// In your template, create quick access to fields
$fields = [];

if (!empty($this->item->jcfields)) {
    foreach ($this->item->jcfields as $field) {
        $fields[$field->name] = $field;
    }
}

// Now access directly by name
if (isset($fields['galeria_imagenes'])) {
    echo '<div class="gallery">' . $fields['galeria_imagenes']->value . '</div>';
}

if (isset($fields['color_destacado'])) {
    echo '<div style="background: ' . htmlspecialchars($fields['color_destacado']->value) . '">...</div>';
}

if (isset($fields['precio'])) {
    echo '<span class="price">' . htmlspecialchars($fields['precio']->value) . '</span>';
}
?>
