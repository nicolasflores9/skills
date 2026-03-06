<?php
/**
 * COMPLETE EXAMPLE: Custom JLayout - Article Card
 *
 * Template location: /templates/cassiopeia/html/layouts/joomla/custom/article-card.php
 *
 * This layout is REUSABLE and can be used in multiple views:
 * - blog_item.php
 * - featured.php
 * - alternative layouts
 * - custom modules
 *
 * PASSED VARIABLES:
 * @var  string  $displayData['title']      Article title
 * @var  string  $displayData['content']    Content/intro
 * @var  string  $displayData['image']      Image URL
 * @var  string  $displayData['link']       Article URL
 * @var  string  $displayData['category']   Category name
 * @var  string  $displayData['author']     Author name
 * @var  string  $displayData['date']       Publication date
 * @var  string  $displayData['cssClass']   Additional CSS classes
 *
 * JOOMLA: 5.x, 6.x
 * DATE: 2024-03-06
 */

defined('_JEXEC') or die;

// Extract variables from displayData with default values
$title = $displayData['title'] ?? '';
$content = $displayData['content'] ?? '';
$image = $displayData['image'] ?? '';
$link = $displayData['link'] ?? '#';
$category = $displayData['category'] ?? '';
$author = $displayData['author'] ?? '';
$date = $displayData['date'] ?? '';
$cssClass = $displayData['cssClass'] ?? 'card-default';

// Validations
if (empty($title)):
    return;
endif;
?>

<article class="article-card <?php echo htmlspecialchars($cssClass); ?>">

    <!-- IMAGE -->
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

                <!-- CATEGORY BADGE (OPTIONAL) -->
                <?php if ($category): ?>
                    <span class="card-category-badge">
                        <?php echo htmlspecialchars($category); ?>
                    </span>
                <?php endif; ?>
            </figure>
        </div>
    <?php endif; ?>

    <!-- CONTENT -->
    <div class="card-body">

        <!-- TITLE -->
        <h3 class="card-title">
            <a href="<?php echo htmlspecialchars($link); ?>"
               title="<?php echo htmlspecialchars($title); ?>"
               class="card-title-link">
                <?php echo htmlspecialchars($title); ?>
            </a>
        </h3>

        <!-- COMPACT METADATA -->
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

        <!-- CONTENT/INTRO -->
        <?php if ($content): ?>
            <div class="card-content">
                <?php echo $content; ?>
            </div>
        <?php endif; ?>

    </div>

    <!-- FOOTER -->
    <footer class="card-footer">
        <a href="<?php echo htmlspecialchars($link); ?>"
           class="btn btn-sm btn-primary card-link"
           title="Read <?php echo htmlspecialchars($title); ?>">
            Read more
            <i class="fas fa-arrow-right"></i>
        </a>
    </footer>

</article>

<?php
/**
 * ============================================
 * HOW TO USE THIS LAYOUT
 * ============================================
 *
 * In blog_item.php or any other view:
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
 * ADVANTAGES OF USING JLAYOUT
 * ============================================
 *
 * 1. REUSE: Use in multiple views without duplicating code
 * 2. MAINTENANCE: Change design in one place
 * 3. CONSISTENCY: Same structure across all views
 * 4. MODULARITY: Clear separation of responsibilities
 * 5. TESTING: Easier to test in isolation
 *
 * ============================================
 * USAGE ALTERNATIVES
 * ============================================
 *
 * Use in different contexts:
 *
 * // In category/blog_item.php
 * echo JLayoutHelper::render('joomla.custom.article-card', [
 *     // article data
 * ]);
 *
 * // In featured/default.php
 * foreach ($this->items as $item):
 *     echo JLayoutHelper::render('joomla.custom.article-card', [
 *         // article data
 *     ]);
 * endforeach;
 *
 * // In custom module
 * foreach ($items as $item):
 *     echo JLayoutHelper::render('joomla.custom.article-card', [
 *         // article data
 *     ]);
 * endforeach;
 *
 * ============================================
 * RECOMMENDED CSS
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
