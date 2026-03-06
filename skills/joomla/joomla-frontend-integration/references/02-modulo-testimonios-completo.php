<?php
/**
 * Custom Testimonials Module for Joomla 5/6
 * Complete example with CSS, JS, and WebAssetManager
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ModuleHelper;

// Load module assets
$wa = Factory::getApplication()
      ->getDocument()
      ->getWebAssetManager();

$wa->useScript('mod_testimonios.carousel');
$wa->useStyle('mod_testimonios.style');

// Get module parameters
$testimonials = json_decode($params->get('testimonials', '[]'));
$speed = (int)$params->get('speed', 5000);
$autoplay = (bool)$params->get('autoplay', true);
$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx', ''), ENT_COMPAT, 'UTF-8');

// Pass data to JavaScript
$wa->addInlineScript('
window.TestimonialConfig = {
    speed: ' . $speed . ',
    autoplay: ' . ($autoplay ? 'true' : 'false') . ',
    testimonials: ' . json_encode($testimonials) . '
};
', [], ['position' => 'before']);

?>

<div class="mod-testimonios<?php echo $moduleclass_sfx; ?>">
    <div class="testimonios-carousel" id="testimonialsCarousel">
        <?php if (!empty($testimonials)): ?>
            <?php foreach ($testimonials as $index => $testimonial): ?>
                <div class="testimonial-item<?php echo $index === 0 ? ' active' : ''; ?>">
                    <div class="testimonial-content">
                        <p class="testimonial-text">
                            "<?php echo htmlspecialchars($testimonial->text, ENT_COMPAT, 'UTF-8'); ?>"
                        </p>
                        <div class="testimonial-author">
                            <?php if (!empty($testimonial->image)): ?>
                                <img src="<?php echo htmlspecialchars($testimonial->image, ENT_COMPAT, 'UTF-8'); ?>"
                                     alt="<?php echo htmlspecialchars($testimonial->name, ENT_COMPAT, 'UTF-8'); ?>"
                                     class="author-image">
                            <?php endif; ?>
                            <div class="author-info">
                                <h4 class="author-name"><?php echo htmlspecialchars($testimonial->name, ENT_COMPAT, 'UTF-8'); ?></h4>
                                <p class="author-role"><?php echo htmlspecialchars($testimonial->role, ENT_COMPAT, 'UTF-8'); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Navigation -->
    <div class="testimonials-nav">
        <button class="nav-btn prev-btn" aria-label="Previous">
            <span class="icon">&#10094;</span>
        </button>
        <div class="dots-container" id="testimonialsDots">
            <?php if (!empty($testimonials)): ?>
                <?php foreach ($testimonials as $index => $item): ?>
                    <button class="dot<?php echo $index === 0 ? ' active' : ''; ?>"
                            data-slide="<?php echo $index; ?>"
                            aria-label="Go to testimonial <?php echo $index + 1; ?>">
                    </button>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <button class="nav-btn next-btn" aria-label="Next">
            <span class="icon">&#10095;</span>
        </button>
    </div>
</div>
