<?php
/**
 * EJEMPLO COMPLETO: Override - Artículo Individual Personalizado
 *
 * Ubicación en template: /templates/cassiopeia/html/com_content/article/default.php
 *
 * CAMBIOS REALIZADOS:
 * - Estructura semántica mejorada (article, header, section)
 * - Imagen destacada con figcaption
 * - Metadatos ordenados debajo del título
 * - Campos personalizados (jcfields)
 * - Tags mostrados
 * - Navegación anterior/siguiente
 * - Breadcrumbs
 *
 * VARIABLES DISPONIBLES:
 * @var  object  $this->item          Objeto del artículo completo
 * @var  object  $this->params        Parámetros del artículo
 * @var  array   $this->jcfields      Campos personalizados
 *
 * JOOMLA: 5.x, 6.x
 * FECHA: 2024-03-06
 */

defined('_JEXEC') or die;

// Datos principales
$item = $this->item;
$params = $this->params;
$images = !empty($item->images) ? json_decode($item->images) : null;
?>

<article class="article-container" itemscope itemtype="https://schema.org/Article">

    <!-- BREADCRUMBS -->
    <?php if ($params->get('show_category_title')): ?>
        <?php echo JLayoutHelper::render('joomla.content.breadcrumbs',
            ['pathway' => $this->pathway, 'item' => $item]); ?>
    <?php endif; ?>

    <!-- HEADER DEL ARTÍCULO -->
    <header class="article-header">

        <!-- TÍTULO -->
        <h1 class="article-title" itemprop="headline">
            <?php echo htmlspecialchars($item->title); ?>
        </h1>

        <!-- METADATOS (AUTOR, FECHA, CATEGORÍA) -->
        <?php echo JLayoutHelper::render('joomla.content.info_block', [
            'item' => $item,
            'params' => $params,
            'show_author' => $params->get('show_author', 1),
            'show_category' => $params->get('show_category', 1),
            'show_publish_date' => $params->get('show_publish_date', 1),
            'show_create_date' => $params->get('show_create_date', 1),
            'show_hits' => $params->get('show_hits', 1),
        ]); ?>

    </header>

    <!-- CUERPO DEL ARTÍCULO -->
    <section class="article-body" itemprop="articleBody">

        <!-- IMAGEN DESTACADA -->
        <?php if (!empty($images) && !empty($images->image_intro)): ?>
            <figure class="article-intro-image">
                <img src="<?php echo htmlspecialchars($images->image_intro); ?>"
                     alt="<?php echo htmlspecialchars($images->image_intro_alt ?? $item->title); ?>"
                     itemprop="image">
                <?php if (!empty($images->image_intro_caption)): ?>
                    <figcaption class="image-caption">
                        <?php echo htmlspecialchars($images->image_intro_caption); ?>
                    </figcaption>
                <?php endif; ?>
            </figure>
        <?php endif; ?>

        <!-- CONTENIDO PRINCIPAL -->
        <div class="article-text" itemprop="text">
            <?php echo $item->text; ?>
        </div>

    </section>

    <!-- CAMPOS PERSONALIZADOS -->
    <?php if (!empty($item->jcfields) && is_array($item->jcfields)): ?>
        <aside class="article-custom-fields">
            <h3 class="custom-fields-title">Información Adicional</h3>
            <div class="custom-fields-list">
                <?php foreach ($item->jcfields as $field): ?>
                    <?php if (!empty($field->rawvalue)): ?>
                        <div class="field-item field-type-<?php echo htmlspecialchars($field->type); ?>"
                             data-field-id="<?php echo (int)$field->id; ?>">
                            <strong class="field-label">
                                <?php echo htmlspecialchars($field->label); ?>
                            </strong>
                            <div class="field-value">
                                <?php echo JLayoutHelper::render('joomla.content.field_value', [
                                    'field' => $field,
                                    'item' => $item,
                                    'value' => $field->rawvalue,
                                ]); ?>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </aside>
    <?php endif; ?>

    <!-- TAGS -->
    <?php if ($params->get('show_tags', 1) && !empty($item->tags->itemTags)): ?>
        <footer class="article-tags">
            <h4 class="tags-title">Etiquetas</h4>
            <div class="tags-list">
                <?php foreach ($item->tags->itemTags as $tag): ?>
                    <a href="<?php echo JRoute::_('index.php?option=com_tags&view=tag&id=' . $tag->id); ?>"
                       class="tag-item">
                        <?php echo htmlspecialchars($tag->title); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </footer>
    <?php endif; ?>

    <!-- NAVEGACIÓN ANTERIOR/SIGUIENTE -->
    <?php if ($params->get('show_navigation')): ?>
        <?php echo JLayoutHelper::render('joomla.content.navigation', [
            'item' => $item,
            'params' => $params,
        ]); ?>
    <?php endif; ?>

    <!-- PLUGIN OUTPUT (PAGE NAVIGATION, ETC.) -->
    <?php if ($params->get('show_page_navigation')): ?>
        <div class="article-plugins">
            <?php echo $item->event->afterDisplayContent; ?>
        </div>
    <?php endif; ?>

</article>

<?php
/**
 * NOTAS DE IMPLEMENTACIÓN:
 *
 * 1. BREADCRUMBS: Mostrar ruta de navegación (opcional)
 * 2. METADATOS: Usar JLayout para mantener separación
 * 3. IMAGEN: Usar figura con figcaption para semántica
 * 4. CAMPOS PERSONALIZADOS: Iterar sobre jcfields
 * 5. TAGS: Usar item->tags->itemTags
 * 6. ESCAPADO: htmlspecialchars() para strings
 * 7. RUTAS: Usar JRoute::_() para URLs
 * 8. LAYOUTS: Usar JLayoutHelper::render() para reutilización
 * 9. SCHEMA: Usar itemscope/itemtype para SEO
 */
?>
