# Quick Guide - Template Overrides in Joomla 5/6

## Basic Steps to Create an Override

### 1. Locate the Original File
```bash
# Example: Single article
/components/com_content/views/article/tmpl/default.php
```

### 2. Create Structure in Template
```
/templates/cassiopeia/html/
└── com_content/
    └── article/
        └── default.php  (copy here)
```

### 3. Copy and Modify
- Copy original file to the override path
- Make necessary changes
- Escape outputs correctly
- Document changes in header

### 4. Test
```bash
# Clear cache in backend: System > Clear Cache
# Or via command line:
php -r "JFactory::getCache()->clean();"
```

---

## Common Paths

### Components

```
SINGLE ARTICLE
Original:  /components/com_content/views/article/tmpl/default.php
Override:  /templates/[t]/html/com_content/article/default.php

CATEGORY - BLOG
Original:  /components/com_content/views/category/tmpl/blog.php
Override:  /templates/[t]/html/com_content/category/blog.php

CATEGORY - BLOG ITEM
Original:  /components/com_content/views/category/tmpl/blog_item.php
Override:  /templates/[t]/html/com_content/category/blog_item.php

CATEGORY - LIST
Original:  /components/com_content/views/category/tmpl/default.php
Override:  /templates/[t]/html/com_content/category/default.php
```

### Modules

```
LOGIN
Original:  /modules/mod_login/tmpl/default.php
Override:  /templates/[t]/html/mod_login/default.php

MENU
Original:  /modules/mod_menu/tmpl/default.php
Override:  /templates/[t]/html/mod_menu/default.php

CUSTOM
Original:  /modules/mod_custom/tmpl/default.php
Override:  /templates/[t]/html/mod_custom/default.php
```

### JLayout

```
FEATURED IMAGE
Original:  /layouts/joomla/content/intro_image.php
Override:  /templates/[t]/html/layouts/joomla/content/intro_image.php

INFO BLOCK
Original:  /layouts/joomla/content/info_block.php
Override:  /templates/[t]/html/layouts/joomla/content/info_block.php
```

---

## Useful Variables

### Articles ($this->item)

```php
$this->item->id                    // Article ID
$this->item->title                 // Title
$this->item->slug                  // URL slug
$this->item->introtext             // Introductory text
$this->item->text                  // Main content
$this->item->images                // JSON with images
$this->item->publish_up            // Publication date
$this->item->author                // Author name
$this->item->author_email          // Author email
$this->item->category_title        // Category
$this->item->jcfields              // Custom fields (array)
$this->item->tags->itemTags        // Tags/labels
$this->item->link                  // Article URL
$this->item->hits                  // Number of views
```

### Parameters ($this->params)

```php
$this->params->get('show_author')          // Show author
$this->params->get('show_category')        // Show category
$this->params->get('show_publish_date')    // Show date
$this->params->get('show_hits')            // Show views
$this->params->get('show_tags')            // Show tags
```

### Modules ($this->module, $this->params)

```php
$this->module->id                  // Module ID
$this->module->title               // Module title
$this->params->get('key')          // Specific parameter
```

---

## Security - Escaping

### GOOD
```php
<?php echo htmlspecialchars($item->title, ENT_QUOTES, 'UTF-8'); ?>
<?php echo JHtml::_('string.truncate', $item->text, 100); ?>
<?php echo JRoute::_('index.php?option=com_content&view=article&id=' . $item->id); ?>
<?php echo JText::_('COM_CONTENT_READ_MORE'); ?>
```

### BAD
```php
<?php echo $item->title; ?>
<?php echo $item->introtext; ?>
<?php echo 'index.php?option=com_content&id=' . $item->id; ?>
```

---

## Alternative Layouts

### Creating an Alternative
```
/templates/cassiopeia/html/mod_login/
├── default.php     (original layout)
└── grid.php        (alternative layout)
```

### Selecting in Backend
1. Go to Extensions > Modules
2. Edit module
3. Tab "Advanced"
4. Layout: select "grid"

---

## JLayout - Reusable

### Create
```php
// /templates/cassiopeia/html/layouts/joomla/custom/article-card.php
<?php defined('_JEXEC') or die;
$title = $displayData['title'] ?? '';
$content = $displayData['content'] ?? '';
?>
<article class="card">
    <h3><?php echo htmlspecialchars($title); ?></h3>
    <p><?php echo $content; ?></p>
</article>
```

### Use
```php
<?php
echo JLayoutHelper::render('joomla.custom.article-card', [
    'title' => $this->item->title,
    'content' => $this->item->introtext,
]);
?>
```

---

## Troubleshooting

### Override Not Working
1. Verify exact path in `/templates/[active]/html/`
2. Clear cache: System > Clear Cache
3. Verify permissions: `chmod 755 /templates/cassiopeia/html/`
4. Verify syntax: `php -l file.php`
5. Check logs: `/logs/error.log`

### File Permissions
```bash
# Folders
chmod 755 /templates/cassiopeia/html/

# Files
chmod 644 /templates/cassiopeia/html/com_content/article/default.php
```

### Clear Cache
```bash
# Backend: System > Clear Cache
# Or command line:
rm -rf cache/*
```

---

## Child Template

### Minimum Structure
```
/templates/cassiopeia-child/
├── html/
│   └── com_content/article/default.php
└── templateDetails.xml
```

### templateDetails.xml
```xml
<?xml version="1.0" encoding="utf-8"?>
<extension type="template" client="site">
    <name>Cassiopeia Child</name>
    <version>1.0.0</version>
    <parent>cassiopeia</parent>
    <files>
        <folder>html</folder>
    </files>
</extension>
```

---

## Custom Fields

### Override Layout
```php
// /templates/cassiopeia/html/layouts/com_fields/field/render.php
<?php defined('_JEXEC') or die;
$field = $displayData['field'] ?? null;
$value = $displayData['value'] ?? null;

if (!$field || !$value) return;
?>

<div class="field">
    <strong><?php echo htmlspecialchars($field->label); ?></strong>
    <div><?php echo $value; ?></div>
</div>
```

### Using in an Article
```php
<?php if (!empty($item->jcfields)): ?>
    <?php foreach ($item->jcfields as $field): ?>
        <div><?php echo htmlspecialchars($field->label); ?>:
             <?php echo $field->rawvalue; ?></div>
    <?php endforeach; ?>
<?php endif; ?>
```

---

## Documenting an Override

Always include a header:

```php
<?php
/**
 * Override: Descriptive name
 *
 * Original view: path/original.php
 * Component: com_content
 *
 * CHANGES:
 * - Change 1
 * - Change 2
 *
 * DEPENDENCIES: custom field 'author-bio'
 * JOOMLA: 5.0+
 * DATE: 2024-03-06
 */
defined('_JEXEC') or die;
```

---

## Checklist

- [ ] Locate original file
- [ ] Create correct `/html/` structure
- [ ] Copy file
- [ ] Make modifications
- [ ] Escape outputs
- [ ] Document changes
- [ ] Test in browser
- [ ] Clear cache
- [ ] Verify on mobile
- [ ] Use git/versioning

---

## Useful Resources

- [Joomla Docs - Template Overrides](https://docs.joomla.org/Understanding_Output_Overrides)
- Backend: Extensions > Templates > [Template] > Create Overrides
- Debugging: enable debug in configuration.php
- Logs: /logs/error.log

---

## Common Errors

**"Override not showing"**
- Verify exact path
- Clear cache
- Check active template name

**"Empty variables"**
- Verify $this->item exists
- Validate before using: `if (!empty($item->field))`
- Debug with `var_dump($item)`

**"Broken layout"**
- Verify CSS is loading
- Check closed HTML tags
- Validate JavaScript is not breaking

**"Permissions"**
```bash
chmod -R 755 /templates/cassiopeia/html/
chmod -R 644 /templates/cassiopeia/html/*.php
```

---

## Useful Commands

```bash
# Validate PHP syntax
php -l /templates/cassiopeia/html/com_content/article/default.php

# Find original files
find /components -name "*.php" -path "*/tmpl/*"

# Compare override with original
diff /components/com_content/views/article/tmpl/default.php \
     /templates/cassiopeia/html/com_content/article/default.php

# Correct permissions
chmod 755 /templates/cassiopeia/html/
find /templates/cassiopeia/html -type f -exec chmod 644 {} \;
```

---

## Supported Versions

- Joomla 5.0.x
- Joomla 6.0.x

(Fully compatible with Joomla 4.x as well)
