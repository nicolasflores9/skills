<?php
/**
 * COMPLETE EXAMPLE: Field Override - Custom Fields
 *
 * Template location: /templates/cassiopeia/html/layouts/com_fields/field/render.php
 *
 * This file controls HOW CUSTOM FIELDS ARE DISPLAYED
 * on the frontend. Joomla iterates over jcfields and renders each one with this layout.
 *
 * AVAILABLE VARIABLES:
 * @var  array   $displayData['field']       Field object
 * @var  object  $displayData['item']        Article/item object
 * @var  string  $displayData['value']       Processed field value
 *
 * FIELD TYPES:
 * - text, textarea, editor
 * - radio, checkbox, list
 * - url, email
 * - integer, decimal, calendar
 * - file
 *
 * JOOMLA: 5.x, 6.x
 * DATE: 2024-03-06
 */

defined('_JEXEC') or die;

// Extract variables from displayData
$field = $displayData['field'] ?? null;
$item = $displayData['item'] ?? null;
$value = $displayData['value'] ?? null;

// Validation: if no field or value, display nothing
if (!$field || empty($value)):
    return;
endif;

// Additional field data
$fieldType = $field->type ?? '';
$fieldId = $field->id ?? '';
$fieldLabel = $field->label ?? '';
$fieldName = $field->name ?? '';
?>

<!-- FIELD CONTAINER -->
<div class="field-item field-type-<?php echo htmlspecialchars($fieldType); ?>"
     data-field-id="<?php echo (int)$fieldId; ?>"
     data-field-name="<?php echo htmlspecialchars($fieldName); ?>">

    <!-- LABEL -->
    <div class="field-label-wrapper">
        <label class="field-label" for="field-<?php echo (int)$fieldId; ?>">
            <?php echo htmlspecialchars($fieldLabel); ?>

            <!-- REQUIRED INDICATOR (if applicable) -->
            <?php if (!empty($field->required)): ?>
                <span class="required-indicator" title="Required field" aria-label="required">
                    <i class="fas fa-asterisk"></i>
                </span>
            <?php endif; ?>
        </label>
    </div>

    <!-- FIELD VALUE - RENDERING BY TYPE -->
    <div class="field-value" id="field-<?php echo (int)$fieldId; ?>">

        <?php switch ($fieldType):

            // SIMPLE TEXT FIELDS
            case 'text':
            case 'email':
            case 'url':
            case 'integer':
            case 'decimal':
                ?>
                <span class="field-value-text">
                    <?php echo htmlspecialchars($value); ?>
                </span>
                <?php
                break;

            // LONG TEXT FIELDS
            case 'textarea':
                ?>
                <div class="field-value-textarea">
                    <?php echo nl2br(htmlspecialchars($value)); ?>
                </div>
                <?php
                break;

            // EDITOR TYPE FIELDS (HTML)
            case 'editor':
                ?>
                <div class="field-value-editor">
                    <?php echo $value; // The editor already escapes its content ?>
                </div>
                <?php
                break;

            // MULTIPLE SELECTION FIELDS
            case 'radio':
            case 'checkbox':
            case 'list':
                // The value can be array or string
                $values = is_array($value) ? $value : [$value];
                ?>
                <ul class="field-value-list">
                    <?php foreach ($values as $v): ?>
                        <li class="field-value-item">
                            <?php echo htmlspecialchars($v); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <?php
                break;

            // DATE FIELDS
            case 'calendar':
            case 'date':
                // Try to parse as date
                $timestamp = strtotime($value);
                if ($timestamp !== false):
                    $formattedDate = date('d/m/Y', $timestamp);
                else:
                    $formattedDate = htmlspecialchars($value);
                endif;
                ?>
                <time class="field-value-date" datetime="<?php echo htmlspecialchars($value); ?>">
                    <?php echo $formattedDate; ?>
                </time>
                <?php
                break;

            // FILE FIELDS
            case 'file':
                // The value is a file URL
                $filename = basename($value);
                $extension = strtoupper(pathinfo($filename, PATHINFO_EXTENSION));
                ?>
                <a href="<?php echo htmlspecialchars($value); ?>"
                   class="field-value-file"
                   download>
                    <i class="fas fa-file"></i>
                    <?php echo htmlspecialchars($filename); ?>
                    <span class="file-extension"><?php echo $extension; ?></span>
                </a>
                <?php
                break;

            // IMAGE FIELDS
            case 'image':
                ?>
                <figure class="field-value-image">
                    <img src="<?php echo htmlspecialchars($value); ?>"
                         alt="<?php echo htmlspecialchars($fieldLabel); ?>"
                         class="field-image"
                         loading="lazy">
                </figure>
                <?php
                break;

            // DEFAULT VALUE (for unknown types)
            default:
                ?>
                <div class="field-value-default">
                    <?php echo htmlspecialchars($value); ?>
                </div>
                <?php
                break;

        endswitch; ?>

    </div>

</div>

<?php
/**
 * ============================================
 * ALTERNATIVE: MINIMALIST LAYOUT
 * ============================================
 *
 * File: /templates/cassiopeia/html/layouts/com_fields/field/minimal.php
 *
 * If you want a field without label and more compact:
 */
?>

<?php if (false): // Commented out for this example ?>

    <div class="field-minimal">
        <span class="field-content">
            <?php echo $value; ?>
        </span>
    </div>

<?php endif; ?>

<?php
/**
 * ============================================
 * ALTERNATIVE: CARD LAYOUT
 * ============================================
 *
 * File: /templates/cassiopeia/html/layouts/com_fields/field/card.php
 *
 * If you want to display fields in card format:
 */
?>

<?php if (false): // Commented out for this example ?>

    <div class="field-card">
        <div class="field-card-header">
            <h4 class="field-card-title">
                <?php echo htmlspecialchars($fieldLabel); ?>
            </h4>
        </div>
        <div class="field-card-body">
            <div class="field-card-content">
                <?php echo $value; ?>
            </div>
        </div>
    </div>

<?php endif; ?>

<?php
/**
 * ============================================
 * IMPLEMENTATION NOTES
 * ============================================
 *
 * 1. VALIDATION: Always validate $field and $value
 * 2. ESCAPING: Use htmlspecialchars() for strings
 * 3. SWITCH: Different rendering by field type
 * 4. ATTRIBUTES: Use data-* for custom attributes
 * 5. TYPES: Support all Joomla field types
 * 6. ALTERNATIVES: Create alternative layouts for different styles
 *
 * ============================================
 * HOW TO CREATE AN ALTERNATIVE LAYOUT
 * ============================================
 *
 * 1. Copy this file to:
 *    /templates/cassiopeia/html/layouts/com_fields/field/card.php
 *
 * 2. Modify the HTML as needed
 *
 * 3. In the backend:
 *    - Go to Content > Fields > [Edit Field]
 *    - Tab "Render Options"
 *    - Select layout "card" in the dropdown
 *
 * 4. The field now renders with that layout
 *
 * ============================================
 * ACCESSING FIELD DATA
 * ============================================
 *
 * $field->id               Field ID
 * $field->name             Internal name
 * $field->label            Visible label
 * $field->description      Description
 * $field->type             Type (text, email, etc.)
 * $field->required         Required?
 * $field->default          Default value
 * $field->hint             Hint/placeholder
 * $field->rawvalue         Unprocessed value
 *
 * ============================================
 * RECOMMENDED CSS
 * ============================================
 *
 * .field-item {
 *     margin-bottom: 20px;
 * }
 *
 * .field-label {
 *     display: block;
 *     font-weight: 600;
 *     margin-bottom: 5px;
 *     color: #333;
 * }
 *
 * .required-indicator {
 *     color: #dc3545;
 *     margin-left: 4px;
 * }
 *
 * .field-value {
 *     color: #666;
 *     line-height: 1.6;
 * }
 *
 * .field-type-text,
 * .field-type-email,
 * .field-type-url {
 *     /* Text fields */
 * }
 *
 * .field-type-textarea {
 *     /* Textarea fields */
 * }
 *
 * .field-type-editor {
 *     /* HTML editor fields */
 * }
 *
 * .field-type-list .field-value-list {
 *     list-style: none;
 *     padding: 0;
 * }
 *
 * .field-type-list .field-value-item {
 *     padding: 5px 0;
 * }
 *
 * .field-type-list .field-value-item:before {
 *     content: "✓ ";
 *     color: #28a745;
 *     margin-right: 5px;
 * }
 *
 * .field-type-image .field-image {
 *     max-width: 100%;
 *     height: auto;
 *     border-radius: 4px;
 * }
 *
 * ============================================
 */
?>
