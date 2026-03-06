<?php
/**
 * COMPLETE EXAMPLE: Override - Custom Single Article
 *
 * Template location: /templates/cassiopeia/html/com_content/article/default.php
 *
 * CHANGES MADE:
 * - Improved semantic structure (article, header, section)
 * - Featured image with figcaption
 * - Metadata ordered below the title
 * - Custom fields (jcfields)
 * - Tags displayed
 * - Previous/next navigation
 * - Breadcrumbs
 *
 * AVAILABLE VARIABLES:
 * @var  object  $this->item          Complete article object
 * @var  object  $this->params        Article parameters
 * @var  array   $this->jcfields      Custom fields
 *
 * JOOMLA: 5.x, 6.x
 * DATE: 2024-03-06
 */

defined('_JEXEC') or die;

// Main data
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

    <!-- ARTICLE HEADER -->
    <header class="article-header">

        <!-- TITLE -->
        <h1 class="article-title" itemprop="headline">
            <?php echo htmlspecialchars($item->title); ?>
        </h1>

        <!-- METADATA (AUTHOR, DATE, CATEGORY) -->
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

    <!-- ARTICLE BODY -->
    <section class="article-body" itemprop="articleBody">

        <!-- FEATURED IMAGE -->
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

        <!-- MAIN CONTENT -->
        <div class="article-text" itemprop="text">
            <?php echo $item->text; ?>
        </div>

    </section>

    <!-- CUSTOM FIELDS -->
    <?php if (!empty($item->jcfields) && is_array($item->jcfields)): ?>
        <aside class="article-custom-fields">
            <h3 class="custom-fields-title">Additional Information</h3>
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
            <h4 class="tags-title">Tags</h4>
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

    <!-- PREVIOUS/NEXT NAVIGATION -->
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
 * IMPLEMENTATION NOTES:
 *
 * 1. BREADCRUMBS: Show navigation path (optional)
 * 2. METADATA: Use JLayout to maintain separation
 * 3. IMAGE: Use figure with figcaption for semantics
 * 4. CUSTOM FIELDS: Iterate over jcfields
 * 5. TAGS: Use item->tags->itemTags
 * 6. ESCAPING: htmlspecialchars() for strings
 * 7. ROUTES: Use JRoute::_() for URLs
 * 8. LAYOUTS: Use JLayoutHelper::render() for reuse
 * 9. SCHEMA: Use itemscope/itemtype for SEO
 */
?>
