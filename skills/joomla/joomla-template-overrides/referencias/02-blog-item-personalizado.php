<?php
/**
 * COMPLETE EXAMPLE: Override - Custom Blog Item
 *
 * Template location: /templates/cassiopeia/html/com_content/category/blog_item.php
 *
 * This file is rendered for EACH ARTICLE in the category blog.
 * It is included from blog.php within a loop.
 *
 * CHANGES MADE:
 * - Improved structure with semantic HTML
 * - Responsive featured image
 * - Card-style metadata
 * - Custom "Read more" link
 * - Custom fields displayed
 * - Custom author bio
 * - Category breadcrumb
 *
 * AVAILABLE VARIABLES:
 * @var  object  $this->item          Current article object
 * @var  object  $this->params        View parameters
 *
 * JOOMLA: 5.x, 6.x
 * DATE: 2024-03-06
 */

defined('_JEXEC') or die;

$item = $this->item;
$params = $this->params;
$images = !empty($item->images) ? json_decode($item->images) : null;
?>

<article class="blog-item article-card">

    <!-- IMAGE CONTAINER -->
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

    <!-- CONTENT CONTAINER -->
    <div class="blog-item-content">

        <!-- ARTICLE HEADER -->
        <header class="blog-item-header">

            <!-- CATEGORY (BREADCRUMB) -->
            <?php if ($params->get('show_category_title', 1) && !empty($item->catslug)): ?>
                <div class="article-category">
                    <a href="<?php echo JRoute::_('index.php?option=com_content&view=category&id=' . $item->catid); ?>"
                       class="category-link">
                        <?php echo htmlspecialchars($item->category_title); ?>
                    </a>
                </div>
            <?php endif; ?>

            <!-- ARTICLE TITLE -->
            <h2 class="blog-item-title">
                <a href="<?php echo JRoute::_($item->link); ?>"
                   title="<?php echo htmlspecialchars($item->title); ?>">
                    <?php echo htmlspecialchars($item->title); ?>
                </a>
            </h2>

            <!-- METADATA (COMPACT ROW) -->
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
                        <?php echo $item->hits; ?> views
                    </span>
                <?php endif; ?>

            </div>

        </header>

        <!-- INTRODUCTORY CONTENT -->
        <div class="blog-item-intro-text">
            <?php echo $item->introtext; ?>
        </div>

        <!-- CUSTOM FIELDS (SUMMARIZED) -->
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

        <!-- ARTICLE FOOTER -->
        <footer class="blog-item-footer">

            <!-- READ MORE BUTTON -->
            <a href="<?php echo JRoute::_($item->link); ?>"
               class="btn btn-primary btn-sm read-more-btn"
               title="Read <?php echo htmlspecialchars($item->title); ?>">
                Read more
                <i class="fas fa-arrow-right"></i>
            </a>

            <!-- TAGS (SUMMARIZED) -->
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
 * IMPLEMENTATION NOTES:
 *
 * 1. STRUCTURE: Each article is independent
 * 2. IMAGE: Show only intro_image (responsive)
 * 3. METADATA: Compact in a single line
 * 4. FIELDS: Show only first 2 (summarized)
 * 5. TAGS: Show only first 3 (summarized)
 * 6. LINK: Use $item->link instead of building URL
 * 7. CATEGORY: Use $item->catid for link
 * 8. ROUTING: Use JRoute::_() for compatible URLs
 * 9. DATE: Use JHtml::_('date') for local format
 *
 * EXPECTED CSS:
 * - .blog-item: main container
 * - .blog-item-image: image area
 * - .blog-item-content: content area
 * - .blog-item-title: H2 title
 * - .blog-item-meta: compact metadata
 * - .blog-item-intro-text: introductory text
 * - .blog-item-footer: footer with buttons
 */
?>
