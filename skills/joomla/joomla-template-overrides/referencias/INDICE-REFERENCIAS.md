# Reference Files Index

Documentation and complete examples for the Joomla Template Overrides Skill.

---

## Available Files

### 1. SKILL.md (Main File)

**Location**: `/SKILL.md`

Complete documentation of 519 lines covering:

- Introduction and fundamental concepts
- /html/ folder structure
- Component overrides (com_content)
  - Single article
  - Category blog mode (blog_item.php)
  - Category list mode
- Module overrides (mod_login, etc.)
- Alternative layouts
- JLayout and reusable components
- Child templates
- Field overrides (custom fields)
- Template Manager
- Best practices
- Troubleshooting
- Quick reference

**Content**: Theory + integrated code examples

---

### 2. 01-com-content-article-completo.php

**Location**: `/referencias/01-com-content-article-completo.php`

**Type**: Complete code example

**Purpose**: Complete single article override (com_content)

**Template location**: `/templates/cassiopeia/html/com_content/article/default.php`

**Contains**:
- Semantic HTML5 structure (article, header, section)
- Breadcrumbs
- Title with schema.org markup
- Complete metadata (author, date, category)
- Featured image with figcaption
- Main content
- Custom fields (jcfields)
- Tags/labels
- Previous/next navigation
- Plugin outputs

**Implemented changes**:
- Improved semantic HTML
- Accessible custom fields
- Responsive image
- Well-organized metadata
- SEO with schema.org

**Length**: ~250 lines with documentation

**Ideal for**: Customizing single articles

---

### 3. 02-blog-item-personalizado.php

**Location**: `/referencias/02-blog-item-personalizado.php`

**Type**: Complete code example

**Purpose**: Category listing article override (blog_item.php)

**Template location**: `/templates/cassiopeia/html/com_content/category/blog_item.php`

**Contains**:
- Individual article structure in blog
- Responsive featured image
- Category with link
- Title with link
- Compact metadata (author, date, views)
- Introductory text
- Custom fields (summarized, first 2)
- "Read more" button
- Tags (summarized, first 3)

**Implemented changes**:
- Card design
- Single-line metadata
- Limited fields and tags (summarized)
- Improved UX with icons
- Responsive

**Length**: ~200 lines with documentation

**Ideal for**: Customizing blog listings

**Note**: This file is included WITHIN blog.php in a loop

---

### 4. 03-mod-login-avanzado.php

**Location**: `/referencias/03-mod-login-avanzado.php`

**Type**: Complete code example

**Purpose**: Login module override with advanced features

**Template location**: `/templates/cassiopeia/html/mod_login/default.php`

**Contains**:
- Conditional rendering: logged-in vs not logged-in user
- Modern login form
  - HTML5 validation
  - Labels with aria-*
  - Placeholders
  - Autocomplete
- Logged-in user: custom menu
- Remember user (checkbox)
- Error/success messages
- Password recovery link
- Registration link
- Username recovery link
- CSRF tokens

**Implemented changes**:
- Improved accessibility
- Modern validation
- Different UI when logged in
- Configurable parameters
- Fontawesome icons

**Length**: ~200 lines with documentation

**Ideal for**: Customizing login and user UX

---

### 5. 04-jlayout-custom-card.php

**Location**: `/referencias/04-jlayout-custom-card.php`

**Type**: Reusable JLayout example

**Purpose**: Reusable article card component for multiple views

**Template location**: `/templates/cassiopeia/html/layouts/joomla/custom/article-card.php`

**Contains**:
- Reusable card structure
- Image with category badge
- Title with link
- Metadata (author, date)
- Introductory content
- "Read more" button

**Expected variables**:
- title, content, image, link
- category, author, date
- cssClass

**Usage**:
```php
echo JLayoutHelper::render('joomla.custom.article-card', [
    'title' => $item->title,
    'content' => $item->introtext,
    // ... more variables
]);
```

**Advantages**:
- Reuse in blog_item.php, featured.php, modules
- Change design in one place
- Consistency across the entire site

**Length**: ~300 lines (includes documentation and usage examples)

**Ideal for**: Creating reusable components

---

### 6. 05-child-template-config.xml

**Location**: `/referencias/05-child-template-config.xml`

**Type**: XML configuration

**Purpose**: Complete configuration file for child template

**Template location**: `/templates/cassiopeia-child/templateDetails.xml`

**Contains**:
- Template metadata (name, version, author)
- Parent template definition
- Child file list
- Module positions
- Configurable parameters
  - Basic fieldset (logo, colors)
  - Layout fieldset (width, sidebar)
  - Components fieldset (breadcrumbs, titles)
  - Advanced fieldset (cache, compression)
- CSS variables

**Configuration example**:
- Customizable logo
- Site colors
- Page widths
- Show/hide elements
- Cache and compression

**Advantages**:
- Automatically inherits from parent
- Only stores changes
- Parameters without touching code
- Multiple child templates from the same parent

**Length**: ~200 lines with documentation

**Ideal for**: Creating professional child templates

---

### 7. 06-field-override-ejemplo.php

**Location**: `/referencias/06-field-override-ejemplo.php`

**Type**: Complete code example

**Purpose**: Custom fields override with type-based rendering

**Template location**: `/templates/cassiopeia/html/layouts/com_fields/field/render.php`

**Contains**:
- Conditional rendering by field type:
  - text, email, url, integer, decimal
  - textarea, editor
  - radio, checkbox, list
  - calendar, date
  - file, image
- Label with required indicator
- Secure escaping
- Validations

**Alternatives**:
- minimal.php: no label, compact
- card.php: in card format

**How to use**:
1. Copy to `/templates/cassiopeia/html/layouts/com_fields/field/render.php`
2. In backend: Field Edit > Render Options > select layout

**Length**: ~250 lines with documentation

**Ideal for**: Customizing custom field presentation

---

### 8. GUIA-RAPIDA.md

**Location**: `/referencias/GUIA-RAPIDA.md`

**Type**: Quick reference guide

**Purpose**: Quick lookup without needing to read the full SKILL.md

**Contains**:
- Basic steps (4 simple steps)
- Common paths (components, modules, JLayout)
- Useful variables ($this->item, $this->params)
- Security (GOOD vs BAD escaping)
- Alternative layouts
- JLayout - create and use
- Common troubleshooting
- Child template minimum structure
- Custom fields
- Documenting overrides
- Verification checklist
- Useful resources
- Common errors
- Useful commands
- Supported versions

**Format**: Markdown with tables and short code examples

**Length**: ~400 lines

**Ideal for**: Quick reference while working

---

### 9. README.md

**Location**: `/README.md`

**Type**: Documentation of the complete set

**Purpose**: Explain structure and content of the skill

**Contains**:
- General description
- File structure
- Topics covered
- How to use (by level: beginner, intermediate, advanced)
- Features
- Key points
- Common use cases
- External resources
- FAQ (frequently asked questions)

**Length**: ~300 lines

**Ideal for**: Understanding the complete structure

---

## How to Navigate the Files

### By Topic

**I want to customize articles**
- Read: SKILL.md section "Component Overrides - com_content"
- See: `/referencias/01-com-content-article-completo.php`
- Reference: GUIA-RAPIDA.md section "Components"

**I want to modify the blog listing**
- Read: SKILL.md section "Category Blog Mode - blog_item.php"
- See: `/referencias/02-blog-item-personalizado.php`
- Reference: GUIA-RAPIDA.md section "Common Paths"

**I want to customize login**
- Read: SKILL.md section "Module Overrides"
- See: `/referencias/03-mod-login-avanzado.php`
- Reference: GUIA-RAPIDA.md section "Modules"

**I want to create a reusable component**
- Read: SKILL.md section "JLayout"
- See: `/referencias/04-jlayout-custom-card.php`
- Reference: GUIA-RAPIDA.md section "JLayout - Reusable"

**I want to create a child template**
- Read: SKILL.md section "Child Templates"
- See: `/referencias/05-child-template-config.xml`
- Reference: GUIA-RAPIDA.md section "Child Template"

**I want to customize fields**
- Read: SKILL.md section "Field Overrides"
- See: `/referencias/06-field-override-ejemplo.php`
- Reference: GUIA-RAPIDA.md section "Custom Fields"

### By Experience

**Beginner**: GUIA-RAPIDA.md → SKILL.md → Example 01 → Full SKILL.md

**Intermediate**: SKILL.md → Examples 02-03 → Apply to project

**Advanced**: Examples 04-06 → Combine techniques → Create custom solutions

### By Urgency

**Need it now**: GUIA-RAPIDA.md
**Need it in 5 min**: SKILL.md (scan the index)
**Need ready code**: Copy relevant example
**Need to understand well**: Read SKILL.md + see example + do checklist

---

## Directory Structure

```
joomla-template-overrides/
├── SKILL.md                              Main file (519 lines)
├── README.md                             Set description
└── referencias/
    ├── 01-com-content-article-completo.php
    ├── 02-blog-item-personalizado.php
    ├── 03-mod-login-avanzado.php
    ├── 04-jlayout-custom-card.php
    ├── 05-child-template-config.xml
    ├── 06-field-override-ejemplo.php
    ├── GUIA-RAPIDA.md                   This file
    └── INDICE-REFERENCIAS.md            This file
```

---

## File Information

| File | Lines | Type | Purpose |
|------|-------|------|---------|
| SKILL.md | 519 | MD + Code | Complete documentation |
| 01-*.php | 250 | PHP | Single article |
| 02-*.php | 200 | PHP | Blog item |
| 03-*.php | 200 | PHP | Login module |
| 04-*.php | 300 | PHP | Reusable JLayout |
| 05-*.xml | 200 | XML | Child template config |
| 06-*.php | 250 | PHP | Field override |
| GUIA-RAPIDA.md | 400 | MD | Quick reference |
| README.md | 300 | MD | General description |
| INDICE-REFERENCIAS.md | This | MD | This index |

**Total**: ~2,500+ lines of documentation and code

---

## Features of All Files

- Fully commented code
- Practical examples
- Real use cases
- Integrated best practices
- Security (escaping, validation)
- Improved accessibility
- Responsive design
- Compatible with Joomla 5/6

---

## Next Steps

1. **Read**: GUIA-RAPIDA.md to understand the structure
2. **Look**: At the example relevant to your need
3. **Copy**: Code from the example
4. **Adapt**: To your specific project
5. **Test**: In browser and mobile
6. **Document**: Changes made
7. **Version control**: With git

---

## Where to Start?

**First time**: `/referencias/GUIA-RAPIDA.md` + example 01
**Medium experience**: SKILL.md + relevant example
**Advanced user**: Example 04/05/06 + combine techniques

---

**Last updated**: March 2024
**Versions**: Joomla 5.x, 6.x
**Language**: English

**Start now with the quick guide!**
