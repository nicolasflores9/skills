<?php
/**
 * EJEMPLOS PRÁCTICOS DE CUSTOM FIELDS EN JOOMLA 5/6
 * ====================================================
 * Implementaciones completas para componentes, módulos y templates
 */

// ============================================================================
// EJEMPLO 1: CARGAR CAMPOS PERSONALIZADOS EN UN COMPONENTE
// ============================================================================

namespace MyComponent\Site\Model;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ItemModel;
use Joomla\Component\Fields\Administrator\Helper\FieldsHelper;

class ArticleModel extends ItemModel {

    public function getItem($pk = null) {
        $item = parent::getItem($pk);

        if ($item) {
            // Carga los campos personalizados del artículo
            JLoader::register('FieldsHelper', JPATH_ADMINISTRATOR . '/components/com_fields/helpers/fields.php');
            $item->jcfields = FieldsHelper::getFields('com_content.article', $item, true);
        }

        return $item;
    }
}


// ============================================================================
// EJEMPLO 2: PLUGIN QUE INYECTA CAMPOS EN UN COMPONENTE PERSONALIZADO
// ============================================================================

namespace MyPlugin\SystemCustomFields;

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Factory;
use Joomla\Component\Fields\Administrator\Helper\FieldsHelper;

class SystemCustomFields extends CMSPlugin {

    /**
     * Inyecta campos personalizados en formularios
     */
    public function onContentPrepareForm($form, $data) {
        if (!($form instanceof Form)) {
            return true;
        }

        // Obtén el contexto
        $context = isset($data->context) ? $data->context :
                   Factory::getApplication()->input->getCmd('option') . '.' .
                   Factory::getApplication()->input->getCmd('view');

        // Inyecta campos solo en contextos válidos
        if (in_array($context, ['com_content.article', 'com_users.user'])) {
            JLoader::register('FieldsHelper', JPATH_ADMINISTRATOR . '/components/com_fields/helpers/fields.php');
            FieldsHelper::getFields($context, $data, false);
        }

        return true;
    }

    /**
     * Prepara datos para renderizar campos
     */
    public function onContentPrepareData($context, $data) {
        if (empty($data)) {
            return true;
        }

        // Convierte datos Registry a arrays si es necesario
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
// EJEMPLO 3: MÓDULO QUE MUESTRA CAMPOS PERSONALIZADOS DE UN ARTÍCULO
// ============================================================================

namespace MyModule\Site\Helper;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Factory\MVCFactory;
use Joomla\Component\Fields\Administrator\Helper\FieldsHelper;

class ModCustomFieldsHelper {

    /**
     * Obtén un artículo con sus campos personalizados
     */
    public static function getArticleWithFields($articleId) {
        try {
            // Obtén la aplicación y el modelo
            $app = Factory::getApplication();
            $mvcFactory = $app->bootComponent('com_content')->getMVCFactory();
            $model = $mvcFactory->createModel('Article', 'Administrator');

            // Carga el artículo
            $article = $model->getItem((int)$articleId);

            if (!$article) {
                return null;
            }

            // Registra y carga el helper de campos
            JLoader::register('FieldsHelper', JPATH_ADMINISTRATOR . '/components/com_fields/helpers/fields.php');

            // Obtén campos renderizados (HTML)
            $article->jcfields = FieldsHelper::getFields('com_content.article', $article, true);

            return $article;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Obtén campos en array indexado por nombre
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
// EJEMPLO 4: CONSULTA DIRECTA A BASE DE DATOS PARA CAMPOS
// ============================================================================

use Joomla\Database\DatabaseInterface;

class FieldValueRepository {

    private $db;

    public function __construct(DatabaseInterface $db) {
        $this->db = $db;
    }

    /**
     * Obtén todos los valores de campos para un artículo
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
     * Obtén todos los campos disponibles para un contexto
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
     * Guarda un valor de campo
     */
    public function saveFieldValue($fieldId, $itemId, $value) {
        // Primero intenta actualizar
        $query = $this->db->getQuery(true)
            ->update($this->db->quoteName('#__fields_values'))
            ->set($this->db->quoteName('value') . ' = ' . $this->db->quote($value))
            ->where($this->db->quoteName('field_id') . ' = ' . (int)$fieldId)
            ->where($this->db->quoteName('item_id') . ' = ' . (int)$itemId);

        $this->db->setQuery($query);
        $this->db->execute();

        // Si no fue actualizado, inserta
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
     * Elimina todos los valores de un artículo
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
// EJEMPLO 5: OVERRIDE DE TEMPLATE PARA RENDERIZAR CAMPOS
// ============================================================================

// Archivo: templates/mytemplate/html/layouts/com_content/fields/render.php
// Este archivo se incluye cuando se renderizan campos personalizados

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

$field = $displayData['field'] ?? null;

if (!$field) {
    return;
}

// Determina si renderizar basado en tipo de campo
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
        // Renderiza el valor según el tipo
        if ($field->type === 'media') {
            // Para campos multimedia
            echo '<img src="' . htmlspecialchars($field->value) . '" alt="' . htmlspecialchars($field->label) . '" />';
        } elseif ($field->type === 'list') {
            // Para listas
            echo htmlspecialchars($field->value);
        } else {
            // Para otros campos
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
// EJEMPLO 6: VALIDACIÓN PERSONALIZADA DE CAMPOS
// ============================================================================

// Archivo: /components/com_mycomponent/models/rules/codigopostal.php

use Joomla\CMS\Form\FormRule;
use Joomla\Registry\Registry;
use SimpleXMLElement;

class JFormRuleCodigopostal extends FormRule {

    /**
     * Valida un código postal
     * Formato esperado: ABC-123 (3 letras, guión, 3 números)
     */
    public function test(SimpleXMLElement $element, $value, $group = null, Registry $input = null, $form = null) {
        // Si está vacío y no es requerido, es válido
        if (empty($value) && $element['required'] !== 'true') {
            return true;
        }

        // Valida formato
        return preg_match('/^[A-Z]{3}-\d{3}$/', $value) === 1;
    }
}

// En la definición del campo, usa:
// <field name="codigo_postal" type="text" validate="codigopostal" message="Formato: ABC-123" />


// ============================================================================
// EJEMPLO 7: VISTA DE MÓDULO CON CAMPOS
// ============================================================================

// Archivo: mod_custom_fields/tmpl/default.php

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
// EJEMPLO 8: CREAR ÍNDICE DE CAMPOS POR NOMBRE EN TEMPLATE
// ============================================================================

<?php
// En tu template, crea un acceso rápido a campos
$fields = [];

if (!empty($this->item->jcfields)) {
    foreach ($this->item->jcfields as $field) {
        $fields[$field->name] = $field;
    }
}

// Ahora accede directamente por nombre
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
