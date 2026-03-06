---
name: helix-child-template
description: Create and customize child templates with Helix Ultimate for Joomla 5/6. Master protected overrides, custom CSS/JS, module positions, megamenus, and safe updates. Triggers - helix child template, child template joomla, helix ultimate, helix override, custom.css helix, helix template customize, helix framework
---

# Child Templates with Helix Ultimate for Joomla 5/6

## 1. Quick Introduction

Helix Ultimate 2.x is the modern framework for Joomla 4.4+, 5.x, and 6.x. Child templates allow customization without losing changes during updates. Minimal structure, maximum protection.

**Key Advantages:**
- Changes separated from the parent template
- Updates do NOT overwrite customizations
- Improved override system (v2.0.3+)
- Custom CSS/JS automatically loaded
- Automatic inheritance from parent

## 2. Child Template Folder Structure

Create minimal structure:

```
templates/your_child_template/
├── templateDetails.xml          (Required)
├── index.php                    (Optional - only if you modify it)
├── css/
│   └── custom.css              (Create manually)
├── js/
│   └── custom.js               (Create manually)
├── html/
│   └── com_content/
│       └── article/
│           └── default.php     (Load override)
└── overrides/
    └── com_content/
        └── article/
            └── default.php     (Your custom code)
```

**Only include files you modify.** Everything else is automatically inherited from the parent.

## 3. templateDetails.xml File

Identify the parent template and register folders:

```xml
<?xml version="1.0" encoding="utf-8"?>
<extension type="template" client="site">
    <name>My Site Child</name>
    <version>1.0.0</version>
    <creationDate>2025-01-01</creationDate>
    <author>Your Name</author>
    <copyright>Your Copyright</copyright>
    <license>GNU General Public License v2.0 or later</license>
    <description>Custom child template for your site</description>

    <!-- CRUCIAL: Specify parent template -->
    <inherits>shaper_helixultimate</inherits>

    <!-- Files to include in the package -->
    <files>
        <filename>index.php</filename>
        <filename>offline.php</filename>
        <filename>error.php</filename>
        <folder>css</folder>
        <folder>js</folder>
        <folder>html</folder>
        <folder>overrides</folder>
    </files>
</extension>
```

## 4. Override System (New in v2.0.3+)

**Old (< v2.0.3):** `/templates/template/html/` - Overwritten on updates
**New (v2.0.3+):** `/templates/template/overrides/` - PROTECTED on updates

Implement the new system:

```php
// File: /templates/your_child_template/html/com_content/article/default.php
<?php
defined('_JEXEC') or die;
require HelixUltimate\Framework\Platform\HTMLOverride::loadTemplate();
?>

// File: /templates/your_child_template/overrides/com_content/article/default.php
<?php
defined('_JEXEC') or die;
$article = $this->item;
?>

<article class="article-custom">
    <h1><?php echo htmlspecialchars($article->title); ?></h1>

    <div class="metadata">
        By: <?php echo $article->author; ?> |
        <?php echo JHtml::_('date', $article->publish_up, 'd/m/Y'); ?>
    </div>

    <div class="article-body">
        <?php echo $article->introtext; ?>
        <?php echo $article->fulltext; ?>
    </div>
</article>
```

## 5. Customization: Custom CSS

Create `/templates/your_child_template/css/custom.css` with your styles:

```css
:root {
    --primary: #2c3e50;
    --secondary: #3498db;
    --spacing: 8px;
}

body {
    background-color: #f5f5f5;
    font-family: 'Open Sans', sans-serif;
}

h1, h2, h3 {
    color: var(--primary);
    font-weight: 600;
    margin: calc(var(--spacing) * 2) 0;
}

.article-custom {
    background: white;
    padding: calc(var(--spacing) * 3);
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Responsive */
@media (max-width: 768px) {
    h1 { font-size: 1.8rem; }
    body { font-size: 0.95rem; }
}
```

Loads automatically AFTER template.css - your styles override the parent's.

## 6. Customization: Custom JavaScript

Create `/templates/your_child_template/js/custom.js`:

```javascript
(function() {
    'use strict';

    const MyTemplate = {
        init: function() {
            console.log('[MyTemplate] Initializing');
            this.setupMenus();
            this.setupForms();
        },

        setupMenus: function() {
            const toggle = document.querySelector('.menu-toggle');
            if (toggle) {
                toggle.addEventListener('click', (e) => {
                    e.preventDefault();
                    document.querySelector('.mobile-menu')?.classList.toggle('active');
                });
            }
        },

        setupForms: function() {
            const forms = document.querySelectorAll('form[data-validate]');
            forms.forEach(form => {
                form.addEventListener('submit', (e) => {
                    if (!this.validateForm(form)) {
                        e.preventDefault();
                    }
                });
            });
        },

        validateForm: function(form) {
            return Array.from(form.querySelectorAll('[required]'))
                .every(input => input.value.trim() !== '');
        }
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => MyTemplate.init());
    } else {
        MyTemplate.init();
    }

    window.MyTemplate = MyTemplate;
})();
```

Loads AFTER all other scripts - safe to override behaviors.

## 7. Template Options: Code Injection

Admin Panel > System > Templates > Your Template > Custom Code tab:

- **Before </head>:** Meta tags, link tags, inline CSS
- **Before </body>:** Google Analytics, tracking code
- **Custom CSS:** Inline CSS styles (< 30 lines)
- **Custom Javascript:** Inline scripts (execute at the end)

Use for small changes. For medium/large changes use custom.css/js files.

## 8. Module Positions

Helix includes 30+ predefined positions:

```
logo, logo-title, logo-tagline
menu, menu-modal, search
slide, title, breadcrumb
top1, top2, top3, user1, user2, user3, user4, feature
left, right
content-top, content-bottom
footer1, footer2, bottom1, bottom2, bottom3, bottom4
offcanvas, pagebuilder, 404, debug
```

**Add a new position in index.php:**

```php
<?php
if ($this->countModules('my-custom-position')) {
    echo '<div class="custom-section">';
    echo $this->getBuffer('module', 'my-custom-position');
    echo '</div>';
}
?>
```

**Register in templateDetails.xml:**

```xml
<positions>
    <position>my-custom-position</position>
</positions>
```

## 9. Helix Options Configuration

Template Options panel in admin:

**Typography:** 950+ Google Fonts available - configure Body, H1-H6, Navigation

**Colors:** 8 visual presets + Custom Style for custom colors

**Layout Builder:** Add/remove positions, adjust column widths per device

**Megamenu:** Type (Standard/Grid/Accordion), dropdown width, animation, built-in menu builder

**Blog Settings:** Layout, items per page, show/hide elements

**Custom Code:** Inline CSS and Javascript for small changes

## 10. Maintaining Updates Without Losing Changes

**What gets overwritten:**
- index.php (parent file)
- css/template.css
- js/template.js
- html/ (old system - DO NOT use)

**What does NOT get overwritten:**
- overrides/ (NEW protected system)
- css/custom.css
- js/custom.js
- scss/custom.scss
- Saved template parameters
- Child templates

**Update checklist:**

```
BEFORE:
☐ Backup the current template
☐ Document overrides in /overrides/
☐ Verify custom.css/js exist
☐ Note changes with git diff

UPDATE:
☐ Admin > Templates > update template
☐ Review which files changed
☐ Verify /overrides/ is intact
☐ Test in multiple browsers

AFTER:
☐ Review PHP error_log
☐ Test all overrides
☐ Test modules and components
☐ Save to version control
```

## 11. Joomla 6 Compatibility

Helix Ultimate 2.2.x+ is compatible with Joomla 6.

**Requirements:**
- PHP 8.1+ (recommended 8.3+)
- Disable "Behaviour - Backward Compatibility" plugin
- Update extensions that use deprecated APIs

**Verify:**
- All module positions work
- Custom overrides load correctly
- No errors in browser console
- No errors in PHP error_log

## 12. Complete Example Structure

```
templates/my_store_child/
├── templateDetails.xml
├── css/
│   ├── custom.css
│   └── ecommerce.css
├── js/
│   ├── custom.js
│   └── cart-handler.js
├── html/
│   ├── com_content/
│   │   └── article/
│   │       └── default.php
│   ├── com_virtuemart/
│   │   └── productdetails/
│   │       └── default.php
│   └── mod_menu/
│       └── default.php
├── overrides/
│   ├── com_content/
│   │   └── article/
│   │       └── default.php
│   ├── com_virtuemart/
│   │   └── productdetails/
│   │       └── default.php
│   └── mod_menu/
│       └── default.php
└── images/
    └── [custom images]
```

## 13. Common Troubleshooting

**Overrides not showing:**
- Verify exact path `/overrides/com_name/view/default.php`
- Check that /html/ loads correctly with `loadTemplate()`
- Verify file permissions (644 typical)
- Clear cache Joomla > System > Clear Cache

**CSS not applying:**
- Verify custom.css is at `/css/custom.css`
- CSS must be AFTER template.css (specificity)
- Use `!important` only if necessary
- Check for conflicting minification

**JavaScript not executing:**
- Custom.js loads LAST - safe for jQuery/plugins
- Use IIFE `(function() {...})()` to avoid global conflicts
- Verify `DOMContentLoaded` before accessing elements
- Check browser console for errors

**Positions not visible:**
- Use `<?php if ($this->countModules('name')) ?>` to verify
- Modules must be assigned to a menu item
- Verify exact position name
- Inspect generated HTML with browser inspector

## 14. Best Practices

1. **Git Versioning:** Track changes in `/css/custom.css`, `/js/custom.js`, `/overrides/`
2. **Documentation:** Comment complex code with author and date
3. **Modularity:** Separate CSS/JS by function (menu.css, footer.js)
4. **Testing:** Test in Chrome, Firefox, Safari, mobile
5. **Security:** Sanitize outputs with `htmlspecialchars()`, validate inputs
6. **Performance:** Minify CSS/JS in production, lazy loading for images
7. **Accessibility:** ARIA labels, keyboard navigation, contrast ratios WCAG 2.1
8. **Naming:** Use kebab-case for CSS classes, camelCase for JavaScript

## 15. Resources & Documentation

**Official:**
- https://www.joomshaper.com/documentation
- https://docs.joomla.org (Joomla Core)

**Community:**
- JoomShaper Forum: helix-ultimate support
- Joomla Forum: Template discussions
- GitHub: JoomShaper/helix-ultimate

**Useful Tools:**
- Browser DevTools (F12) - debugging
- XAMPP/Local Joomla - local testing
- Git - version control
- VS Code/Sublime - code editor

---

**Updated:** March 2025 | Helix Ultimate 2.x | Joomla 5/6
