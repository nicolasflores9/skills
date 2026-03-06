# Joomla Template Overrides - Complete Skill

An exhaustive system for customizing the output of components, modules, and plugins in Joomla 5/6 without modifying core files.

## Skill Contents

### Main File
- **SKILL.md** (480 lines) - Complete documentation with integrated examples

### Reference Files (examples/references)

1. **01-com-content-article-completo.php**
   - Complete single article override
   - Improved semantic structure
   - Custom fields included
   - Breadcrumbs, metadata, tags, navigation

2. **02-blog-item-personalizado.php**
   - Category blog article override
   - Rendering of each article in listing
   - Compact metadata, responsive image
   - Summarized fields, limited tags

3. **03-mod-login-avanzado.php**
   - Login module override
   - Modern form with validation
   - Logged-in vs not logged-in user
   - Improved accessibility, additional links

4. **04-jlayout-custom-card.php**
   - Custom reusable JLayout
   - Article card component
   - Multiple usage across different views
   - Commented code with usage examples

5. **05-child-template-config.xml**
   - Complete child template configuration
   - Structure and parent inheritance
   - Configurable parameters
   - Module positions

6. **06-field-override-ejemplo.php**
   - Custom fields override
   - Rendering by field type
   - Alternative layouts (minimal, card)
   - Recommended CSS

### Quick Guide
- **GUIA-RAPIDA.md**
  - Basic steps to create overrides
  - Common paths (components, modules, JLayout)
  - Useful variables
  - Security and escaping
  - Troubleshooting
  - Checklist
  - Useful commands

---

## Topics Covered

### Fundamental Concepts
- What are template overrides
- Why use them
- How the system works
- Loading hierarchy

### Component Overrides (com_content)
- Single articles
- Category blog mode (blog.php, blog_item.php)
- Category list mode
- Other common components

### Module Overrides
- Module structure
- Common modules (login, menu, custom, etc.)
- Step-by-step override creation
- Alternative layouts

### Plugin Overrides
- Requirements for overriding
- Plugin override structure
- Example plg_content_pagenavigation

### Alternative Layouts
- Difference vs template override
- Creating for modules
- Creating for components
- Selecting in backend

### JLayout - Reusable Components
- JLayout concept
- Override of joomla/ layouts
- Creating custom layouts
- JLayoutHelper::render()

### Child Templates
- Concept and advantages
- Creating a child template
- templateDetails.xml configuration
- Automatic inheritance

### Field Overrides
- Custom fields layout overrides
- Rendering by field type
- Creating alternative layouts
- Selecting in backend

### Best Practices
- Code documentation
- Version control
- Post-update testing
- Security and escaping
- Performance

### Troubleshooting
- Override not working
- File permissions
- Caching issues
- Debugging

---

## How to Use This Skill

### For Beginners
1. Read SKILL.md - Fundamental Concepts
2. Consult GUIA-RAPIDA.md - Basic steps
3. Examine example 01 (com_content/article)
4. Apply steps to your own project

### For Intermediate Users
1. Review specific examples (blog_item, mod_login)
2. Create overrides following the structure
3. Use examples as templates
4. Adapt to your needs

### For Advanced Users
1. Study JLayout (example 04)
2. Create child template (example 05)
3. Implement field overrides (example 06)
4. Combine advanced techniques

### For Quick Reference
- Use GUIA-RAPIDA.md
- Copy structure from examples
- Look up file paths
- Consult useful variables

---

## File Structure

```
joomla-template-overrides/
├── SKILL.md                              (main file - 480 lines)
├── README.md                             (this file)
└── referencias/
    ├── 01-com-content-article-completo.php
    ├── 02-blog-item-personalizado.php
    ├── 03-mod-login-avanzado.php
    ├── 04-jlayout-custom-card.php
    ├── 05-child-template-config.xml
    ├── 06-field-override-ejemplo.php
    └── GUIA-RAPIDA.md
```

---

## Supported Versions

- **Joomla 6.x**
- **Joomla 5.x**
- **Joomla 4.x** (compatible)

---

## Triggers to Activate the Skill

Questions that automatically activate this skill:

- "override template joomla"
- "override joomla view"
- "blog_item.php"
- "com_content override"
- "JLayout"
- "alternative layout"
- "html override joomla"
- "template overrides"
- "child template joomla"
- "customize joomla article"
- "modify joomla module"
- "joomla custom field"
- "field override"
- "template manager"
- "alternative layout"

---

## Main Features

### Complete Examples
- 6 fully functional examples
- Extensive comments
- Real use cases
- Ready-to-copy code

### Clear Documentation
- English explanations
- Logical structure
- ASCII directory visualization
- Comparative tables

### Quick Guide
- Step-by-step instructions
- Common file paths
- Useful variables
- Useful commands

### Best Practices
- Security and escaping
- Version control
- Testing
- Performance

### Troubleshooting
- Common errors
- Quick solutions
- Debugging tips
- File permissions

---

## Key Points

### The Most Important Override Path
```
ORIGINAL: /components/com_content/views/article/tmpl/default.php
OVERRIDE: /templates/[template]/html/com_content/article/default.php
```

### Always Validate
```php
if (!empty($variable)) {
    // use variable
}
```

### Always Escape
```php
<?php echo htmlspecialchars($variable, ENT_QUOTES, 'UTF-8'); ?>
```

### Always Document
```php
<?php
/**
 * Override: Description
 * CHANGES: list of changes
 * JOOMLA: version
 */
```

---

## Common Use Cases

1. **Customize articles** → Use example 01
2. **Modify blog listing** → Use example 02
3. **Change login form** → Use example 03
4. **Create reusable component** → Use example 04
5. **Create custom theme** → Use example 05
6. **Display custom fields** → Use example 06

---

## External Resources

- [Joomla Documentation](https://docs.joomla.org/Understanding_Output_Overrides)
- [Joomla Developer](https://developer.joomla.org)
- [Joomla Community Magazine](https://magazine.joomla.org)

---

## Technical Information

- **Language**: PHP 8.0+
- **Framework**: Joomla Framework 5/6
- **Approach**: Imperative, with practical code
- **SKILL.md Lines**: ~480
- **Examples**: 6 files
- **Language**: English

---

## Author

Documentation compiled from:
- Joomla Official Documentation
- Joomla Community Magazine
- Community best practices
- Practical experience

---

## Last Updated

March 2024 - Joomla 5.x, 6.x compatible

---

## License

Free documentation for educational and commercial use.
Example code: Creative Commons.

---

## Frequently Asked Questions

**Q: Are overrides lost when updating Joomla?**
A: No. Overrides are in `/templates/` which are not updated. They may need review if core files changed.

**Q: Can I have multiple overrides?**
A: Yes. One override per file. You can also create alternative layouts.

**Q: Is it safe to use overrides?**
A: Completely. It is the recommended way to customize Joomla.

**Q: What is better: override or alternative layout?**
A: It depends. Override replaces, alternative layout is selectable. Use according to your needs.

**Q: How to debug overrides?**
A: Enable debug in configuration.php, check `/logs/error.log`, use `var_dump()`.

---

**Get started now! Read SKILL.md or consult GUIA-RAPIDA.md for quick steps.**
