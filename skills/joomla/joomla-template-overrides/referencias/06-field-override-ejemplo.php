<?php
/**
 * EJEMPLO COMPLETO: Field Override - Campos Personalizados
 *
 * Ubicación en template: /templates/cassiopeia/html/layouts/com_fields/field/render.php
 *
 * Este archivo controla CÓMO SE MUESTRAN los campos personalizados (custom fields)
 * en el frontend. Joomla itera sobre jcfields y renderiza cada uno con este layout.
 *
 * VARIABLES DISPONIBLES:
 * @var  array   $displayData['field']       Objeto del campo
 * @var  object  $displayData['item']        Objeto del artículo/ítem
 * @var  string  $displayData['value']       Valor procesado del campo
 *
 * TIPOS DE CAMPO:
 * - text, textarea, editor
 * - radio, checkbox, list
 * - url, email
 * - integer, decimal, calendar
 * - file
 *
 * JOOMLA: 5.x, 6.x
 * FECHA: 2024-03-06
 */

defined('_JEXEC') or die;

// Extraer variables de displayData
$field = $displayData['field'] ?? null;
$item = $displayData['item'] ?? null;
$value = $displayData['value'] ?? null;

// Validación: si no hay campo o valor, no mostrar nada
if (!$field || empty($value)):
    return;
endif;

// Datos adicionales del campo
$fieldType = $field->type ?? '';
$fieldId = $field->id ?? '';
$fieldLabel = $field->label ?? '';
$fieldName = $field->name ?? '';
?>

<!-- CONTENEDOR DEL CAMPO -->
<div class="field-item field-type-<?php echo htmlspecialchars($fieldType); ?>"
     data-field-id="<?php echo (int)$fieldId; ?>"
     data-field-name="<?php echo htmlspecialchars($fieldName); ?>">

    <!-- ETIQUETA/LABEL -->
    <div class="field-label-wrapper">
        <label class="field-label" for="field-<?php echo (int)$fieldId; ?>">
            <?php echo htmlspecialchars($fieldLabel); ?>

            <!-- INDICADOR REQUERIDO (si aplica) -->
            <?php if (!empty($field->required)): ?>
                <span class="required-indicator" title="Campo requerido" aria-label="requerido">
                    <i class="fas fa-asterisk"></i>
                </span>
            <?php endif; ?>
        </label>
    </div>

    <!-- VALOR DEL CAMPO - RENDIMIENTO POR TIPO -->
    <div class="field-value" id="field-<?php echo (int)$fieldId; ?>">

        <?php switch ($fieldType):

            // CAMPOS DE TEXTO SIMPLE
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

            // CAMPOS DE TEXTO LARGO
            case 'textarea':
                ?>
                <div class="field-value-textarea">
                    <?php echo nl2br(htmlspecialchars($value)); ?>
                </div>
                <?php
                break;

            // CAMPOS TIPO EDITOR (HTML)
            case 'editor':
                ?>
                <div class="field-value-editor">
                    <?php echo $value; // El editor ya escapa su contenido ?>
                </div>
                <?php
                break;

            // CAMPOS DE SELECCIÓN MÚLTIPLE
            case 'radio':
            case 'checkbox':
            case 'list':
                // El value puede ser array o string
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

            // CAMPOS DE FECHA
            case 'calendar':
            case 'date':
                // Intentar parsear como fecha
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

            // CAMPOS DE ARCHIVO
            case 'file':
                // El value es una URL de archivo
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

            // CAMPOS DE IMAGEN
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

            // VALOR POR DEFECTO (para tipos desconocidos)
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
 * ALTERNATIVA: LAYOUT MINIMALISTAA
 * ============================================
 *
 * Archivo: /templates/cassiopeia/html/layouts/com_fields/field/minimal.php
 *
 * Si quieres un campo sin etiqueta y más compacto:
 */
?>

<?php if (false): // Comentado para este ejemplo ?>

    <div class="field-minimal">
        <span class="field-content">
            <?php echo $value; ?>
        </span>
    </div>

<?php endif; ?>

<?php
/**
 * ============================================
 * ALTERNATIVA: LAYOUT EN TARJETA
 * ============================================
 *
 * Archivo: /templates/cassiopeia/html/layouts/com_fields/field/card.php
 *
 * Si quieres mostrar campos en formato tarjeta:
 */
?>

<?php if (false): // Comentado para este ejemplo ?>

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
 * NOTAS DE IMPLEMENTACIÓN
 * ============================================
 *
 * 1. VALIDACIÓN: Siempre validar $field y $value
 * 2. ESCAPADO: Usar htmlspecialchars() para strings
 * 3. SWITCH: Diferentes rendimientos por tipo de campo
 * 4. ATRIBUTOS: Usar data-* para atributos personalizados
 * 5. TIPOS: Soportar todos los tipos de Joomla
 * 6. ALTERNATIVAS: Crear layouts alternativos para diferentes estilos
 *
 * ============================================
 * CÓMO CREAR LAYOUT ALTERNATIVO
 * ============================================
 *
 * 1. Copiar este archivo a:
 *    /templates/cassiopeia/html/layouts/com_fields/field/card.php
 *
 * 2. Modificar el HTML según necesites
 *
 * 3. En el backend:
 *    - Ir a Content > Fields > [Editar Campo]
 *    - Tab "Render Options"
 *    - Seleccionar layout "card" en el dropdown
 *
 * 4. El campo ahora renderiza con ese layout
 *
 * ============================================
 * ACCESO A DATOS DEL CAMPO
 * ============================================
 *
 * $field->id               ID del campo
 * $field->name             Nombre interno
 * $field->label            Etiqueta visible
 * $field->description      Descripción
 * $field->type             Tipo (text, email, etc.)
 * $field->required         ¿Requerido?
 * $field->default          Valor por defecto
 * $field->hint             Hint/placeholder
 * $field->rawvalue         Valor sin procesar
 *
 * ============================================
 * CSS RECOMENDADO
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
 *     /* Campos de texto */
 * }
 *
 * .field-type-textarea {
 *     /* Campos de área de texto */
 * }
 *
 * .field-type-editor {
 *     /* Campos de editor HTML */
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
