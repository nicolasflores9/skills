<?php
/**
 * EJEMPLO COMPLETO: Override - Blog Item Personalizado
 *
 * Ubicación en template: /templates/cassiopeia/html/com_content/category/blog_item.php
 *
 * Este archivo se renderiza para CADA ARTÍCULO en el blog de categoría.
 * Se incluye desde blog.php dentro de un loop.
 *
 * CAMBIOS REALIZADOS:
 * - Estructura mejorada con HTML semántico
 * - Imagen destacada responsiva
 * - Metadatos en card
 * - Link "Leer más" personalizado
 * - Campos personalizados mostrados
 * - Autor bio personalizado
 * - Categoría breadcrumb
 *
 * VARIABLES DISPONIBLES:
 * @var  object  $this->item          Objeto del artículo actual
 * @var  object  $this->params        Parámetros de la vista
 *
 * JOOMLA: 5.x, 6.x
 * FECHA: 2024-03-06
 */

defined('_JEXEC') or die;

$item = $this->item;
$params = $this->params;
$images = !empty($item->images) ? json_decode($item->images) : null;
?>

<article class="blog-item article-card">

    <!-- CONTENEDOR DE IMAGEN -->
    <?php if (!empty($images) && !empty($images->image_intro)): ?>
        <div class="blog-item-image">
            <figure class="article-figure">
                <a href="<?php echo JRoute::_($item->link); ?>" title="<?php echo htmlspecialchars($item->title); ?>">
                    <img src="<?php echo htmlspecialchars($images->image_intro); ?>"
                         alt="<?php echo htmlspecialchars($images->image_intro_alt ?? $item->title); ?>"
                         class="img-fluid"
                         loading="lazy">
                </a>
            </figure>
        </div>
    <?php endif; ?>

    <!-- CONTENEDOR DE CONTENIDO -->
    <div class="blog-item-content">

        <!-- ENCABEZADO DEL ARTÍCULO -->
        <header class="blog-item-header">

            <!-- CATEGORÍA (BREADCRUMB) -->
            <?php if ($params->get('show_category_title', 1) && !empty($item->catslug)): ?>
                <div class="article-category">
                    <a href="<?php echo JRoute::_('index.php?option=com_content&view=category&id=' . $item->catid); ?>"
                       class="category-link">
                        <?php echo htmlspecialchars($item->category_title); ?>
                    </a>
                </div>
            <?php endif; ?>

            <!-- TÍTULO DEL ARTÍCULO -->
            <h2 class="blog-item-title">
                <a href="<?php echo JRoute::_($item->link); ?>"
                   title="<?php echo htmlspecialchars($item->title); ?>">
                    <?php echo htmlspecialchars($item->title); ?>
                </a>
            </h2>

            <!-- METADATOS (FILA COMPACTA) -->
            <div class="blog-item-meta">

                <?php if ($params->get('show_author', 1)): ?>
                    <span class="meta-item meta-author">
                        <i class="fas fa-user"></i>
                        <?php echo htmlspecialchars($item->author); ?>
                    </span>
                <?php endif; ?>

                <?php if ($params->get('show_publish_date', 1)): ?>
                    <span class="meta-item meta-date">
                        <i class="fas fa-calendar"></i>
                        <time datetime="<?php echo JHtml::date($item->publish_up, 'c'); ?>">
                            <?php echo JHtml::_('date', $item->publish_up, 'd/m/Y'); ?>
                        </time>
                    </span>
                <?php endif; ?>

                <?php if ($params->get('show_hits', 0)): ?>
                    <span class="meta-item meta-hits">
                        <i class="fas fa-eye"></i>
                        <?php echo $item->hits; ?> vistas
                    </span>
                <?php endif; ?>

            </div>

        </header>

        <!-- CONTENIDO INTRODUCTORIO -->
        <div class="blog-item-intro-text">
            <?php echo $item->introtext; ?>
        </div>

        <!-- CAMPOS PERSONALIZADOS (RESUMIDO) -->
        <?php if (!empty($item->jcfields) && is_array($item->jcfields)): ?>
            <div class="blog-item-custom-fields">
                <?php foreach (array_slice($item->jcfields, 0, 2) as $field): ?>
                    <?php if (!empty($field->rawvalue)): ?>
                        <div class="custom-field-snippet">
                            <strong><?php echo htmlspecialchars($field->label); ?>:</strong>
                            <?php echo JHtml::_('string.truncate', strip_tags($field->rawvalue), 50); ?>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- PIE DEL ARTÍCULO -->
        <footer class="blog-item-footer">

            <!-- BOTÓN LEER MÁS -->
            <a href="<?php echo JRoute::_($item->link); ?>"
               class="btn btn-primary btn-sm read-more-btn"
               title="Leer <?php echo htmlspecialchars($item->title); ?>">
                Leer más
                <i class="fas fa-arrow-right"></i>
            </a>

            <!-- TAGS (RESUMIDO) -->
            <?php if (!empty($item->tags->itemTags)): ?>
                <div class="blog-item-tags">
                    <?php foreach (array_slice($item->tags->itemTags, 0, 3) as $tag): ?>
                        <a href="<?php echo JRoute::_('index.php?option=com_tags&view=tag&id=' . $tag->id); ?>"
                           class="tag-badge">
                            #<?php echo htmlspecialchars($tag->title); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </footer>

    </div>

</article>

<?php
/**
 * NOTAS DE IMPLEMENTACIÓN:
 *
 * 1. ESTRUCTURA: Cada artículo es independiente
 * 2. IMAGEN: Mostrar solo intro_image (responsivo)
 * 3. METADATOS: Compactos en una línea
 * 4. CAMPOS: Mostrar solo primeros 2 (resumido)
 * 5. TAGS: Mostrar solo primeros 3 (resumido)
 * 6. LINK: Usar $item->link en lugar de construir URL
 * 7. CATEGORÍA: Usar $item->catid para link
 * 8. ROUTING: Usar JRoute::_() para URLs compatibles
 * 9. FECHA: Usar JHtml::_('date') para formato local
 *
 * CSS ESPERADO:
 * - .blog-item: contenedor principal
 * - .blog-item-image: área de imagen
 * - .blog-item-content: área de contenido
 * - .blog-item-title: título H2
 * - .blog-item-meta: metadatos compactos
 * - .blog-item-intro-text: texto introductorio
 * - .blog-item-footer: pie con botones
 */
?>
