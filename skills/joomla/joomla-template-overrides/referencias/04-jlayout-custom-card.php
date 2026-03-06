<?php
/**
 * EJEMPLO COMPLETO: JLayout Personalizado - Article Card
 *
 * Ubicación en template: /templates/cassiopeia/html/layouts/joomla/custom/article-card.php
 *
 * Este layout es REUTILIZABLE y puede usarse en múltiples vistas:
 * - blog_item.php
 * - featured.php
 * - layouts alternativos
 * - módulos personalizados
 *
 * VARIABLES PASADAS:
 * @var  string  $displayData['title']      Título del artículo
 * @var  string  $displayData['content']    Contenido/intro
 * @var  string  $displayData['image']      URL de imagen
 * @var  string  $displayData['link']       URL del artículo
 * @var  string  $displayData['category']   Nombre categoría
 * @var  string  $displayData['author']     Nombre autor
 * @var  string  $displayData['date']       Fecha publicación
 * @var  string  $displayData['cssClass']   Clases CSS adicionales
 *
 * JOOMLA: 5.x, 6.x
 * FECHA: 2024-03-06
 */

defined('_JEXEC') or die;

// Extraer variables de displayData con valores por defecto
$title = $displayData['title'] ?? '';
$content = $displayData['content'] ?? '';
$image = $displayData['image'] ?? '';
$link = $displayData['link'] ?? '#';
$category = $displayData['category'] ?? '';
$author = $displayData['author'] ?? '';
$date = $displayData['date'] ?? '';
$cssClass = $displayData['cssClass'] ?? 'card-default';

// Validaciones
if (empty($title)):
    return;
endif;
?>

<article class="article-card <?php echo htmlspecialchars($cssClass); ?>">

    <!-- IMAGEN -->
    <?php if ($image): ?>
        <div class="card-image-wrapper">
            <figure class="card-image">
                <a href="<?php echo htmlspecialchars($link); ?>"
                   title="<?php echo htmlspecialchars($title); ?>"
                   class="card-image-link">
                    <img src="<?php echo htmlspecialchars($image); ?>"
                         alt="<?php echo htmlspecialchars($title); ?>"
                         class="card-img"
                         loading="lazy">
                </a>

                <!-- BADGE CATEGORÍA (OPCIONAL) -->
                <?php if ($category): ?>
                    <span class="card-category-badge">
                        <?php echo htmlspecialchars($category); ?>
                    </span>
                <?php endif; ?>
            </figure>
        </div>
    <?php endif; ?>

    <!-- CONTENIDO -->
    <div class="card-body">

        <!-- TÍTULO -->
        <h3 class="card-title">
            <a href="<?php echo htmlspecialchars($link); ?>"
               title="<?php echo htmlspecialchars($title); ?>"
               class="card-title-link">
                <?php echo htmlspecialchars($title); ?>
            </a>
        </h3>

        <!-- METADATOS COMPACTOS -->
        <div class="card-meta">
            <?php if ($author): ?>
                <span class="meta-author">
                    <i class="fas fa-user-circle"></i>
                    <?php echo htmlspecialchars($author); ?>
                </span>
            <?php endif; ?>

            <?php if ($date): ?>
                <span class="meta-date">
                    <i class="fas fa-calendar-alt"></i>
                    <?php echo htmlspecialchars($date); ?>
                </span>
            <?php endif; ?>
        </div>

        <!-- CONTENIDO/INTRO -->
        <?php if ($content): ?>
            <div class="card-content">
                <?php echo $content; ?>
            </div>
        <?php endif; ?>

    </div>

    <!-- PIE -->
    <footer class="card-footer">
        <a href="<?php echo htmlspecialchars($link); ?>"
           class="btn btn-sm btn-primary card-link"
           title="Leer <?php echo htmlspecialchars($title); ?>">
            Leer más
            <i class="fas fa-arrow-right"></i>
        </a>
    </footer>

</article>

<?php
/**
 * ============================================
 * CÓMO USAR ESTE LAYOUT
 * ============================================
 *
 * En blog_item.php o cualquier otra vista:
 *
 * <?php
 * echo JLayoutHelper::render('joomla.custom.article-card', [
 *     'title' => $this->item->title,
 *     'content' => $this->item->introtext,
 *     'image' => !empty($this->item->images)
 *         ? json_decode($this->item->images)->image_intro
 *         : '',
 *     'link' => JRoute::_($this->item->link),
 *     'category' => $this->item->category_title,
 *     'author' => $this->item->author,
 *     'date' => JHtml::_('date', $this->item->publish_up, 'd/m/Y'),
 *     'cssClass' => 'featured-card',
 * ]);
 * ?>
 *
 * ============================================
 * VENTAJAS DE USAR JLAYOUT
 * ============================================
 *
 * 1. REUTILIZACIÓN: Usar en múltiples vistas sin duplicar código
 * 2. MANTENIMIENTO: Cambiar diseño en un solo lugar
 * 3. CONSISTENCIA: Misma estructura en todas las vistas
 * 4. MODULARIDAD: Separación clara de responsabilidades
 * 5. TESTEO: Más fácil de probar aisladamente
 *
 * ============================================
 * ALTERNATIVAS DE USO
 * ============================================
 *
 * Usar en diferentes contextos:
 *
 * // En category/blog_item.php
 * echo JLayoutHelper::render('joomla.custom.article-card', [
 *     // datos del artículo
 * ]);
 *
 * // En featured/default.php
 * foreach ($this->items as $item):
 *     echo JLayoutHelper::render('joomla.custom.article-card', [
 *         // datos del artículo
 *     ]);
 * endforeach;
 *
 * // En módulo personalizado
 * foreach ($items as $item):
 *     echo JLayoutHelper::render('joomla.custom.article-card', [
 *         // datos del artículo
 *     ]);
 * endforeach;
 *
 * ============================================
 * CSS RECOMENDADO
 * ============================================
 *
 * .article-card {
 *     display: flex;
 *     flex-direction: column;
 *     border: 1px solid #e0e0e0;
 *     border-radius: 8px;
 *     overflow: hidden;
 *     transition: transform 0.3s, box-shadow 0.3s;
 * }
 *
 * .article-card:hover {
 *     transform: translateY(-4px);
 *     box-shadow: 0 8px 16px rgba(0,0,0,0.1);
 * }
 *
 * .card-image-wrapper {
 *     position: relative;
 *     overflow: hidden;
 *     height: 250px;
 * }
 *
 * .card-image img {
 *     width: 100%;
 *     height: 100%;
 *     object-fit: cover;
 * }
 *
 * .card-category-badge {
 *     position: absolute;
 *     top: 10px;
 *     right: 10px;
 *     background: rgba(0,0,0,0.7);
 *     color: white;
 *     padding: 4px 12px;
 *     border-radius: 20px;
 *     font-size: 12px;
 * }
 *
 * .card-body {
 *     padding: 20px;
 *     flex-grow: 1;
 *     display: flex;
 *     flex-direction: column;
 * }
 *
 * .card-title {
 *     margin: 0 0 10px;
 *     font-size: 18px;
 *     font-weight: 600;
 * }
 *
 * .card-title-link {
 *     color: inherit;
 *     text-decoration: none;
 *     transition: color 0.3s;
 * }
 *
 * .card-title-link:hover {
 *     color: #007bff;
 * }
 *
 * .card-meta {
 *     display: flex;
 *     gap: 15px;
 *     font-size: 12px;
 *     color: #666;
 *     margin-bottom: 15px;
 *     flex-wrap: wrap;
 * }
 *
 * .card-content {
 *     margin-bottom: 15px;
 *     flex-grow: 1;
 * }
 *
 * .card-footer {
 *     padding: 0 20px 20px;
 * }
 *
 * .card-link {
 *     text-decoration: none;
 * }
 *
 * ============================================
 */
?>
