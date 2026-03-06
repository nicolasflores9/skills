# Custom Fields in Joomla 5/6 - Complete Skill

Master custom fields in Joomla. Complete technical guide with examples, database reference, and real-world use cases.

## Contents

- **SKILL.md** - Main guide (325 lines, under 500 as required)
- **INDEX.md** - Resource index and navigation guide
- **references/** - 4 complementary reference documents

## Quick Start

Load custom fields in your code:

```php
JLoader::register('FieldsHelper', JPATH_ADMINISTRATOR . '/components/com_fields/helpers/fields.php');
$fields = FieldsHelper::getFields('com_content.article', $article, true);

foreach ($fields as $field) {
    echo $field->label . ': ' . $field->value;
}
```

## Files

### SKILL.md (Main Guide)
- 15 thematic sections
- Commented PHP code
- Quick start included
- Best practices

### Reference Files

1. **ejemplos-practicos.php** (420 lines)
   - 8 ready-to-use examples
   - Components, modules, plugins
   - Commented code

2. **base-datos.md** (383 lines)
   - Table structure
   - Common SQL queries
   - PHP repository class
   - Performance tips

3. **casos-uso.md** (525 lines)
   - 7 real-world cases
   - Galleries, SEO, users, etc.
   - Complete implementation

4. **faq-troubleshooting.md** (402 lines)
   - 11 frequently asked questions
   - 13 common problems
   - Step-by-step solutions
   - Deployment checklist

## How to Use

1. Read **SKILL.md** to understand concepts
2. Check **INDEX.md** for quick navigation
3. Copy examples from **references/**
4. Find your case in **casos-uso.md**
5. Solve problems in **faq-troubleshooting.md**

## Requirements

- Joomla 5/6
- PHP 8.0+
- MySQL 5.7+

## Topics Covered

- Field types (16 total)
- FieldsHelper API
- Creating fields from admin
- Database (#__fields, #__fields_values)
- Rendering in templates
- Usage in modules and components
- Field Groups
- Validation and filters
- System events
- Direct database access
- REST API
- Troubleshooting

## Supported Contexts

- com_content.article (Articles)
- com_content.categories (Categories)
- com_users.user (Users)
- com_contact.contact (Contacts)
- Custom components

## Example Code

```php
// Module with Custom Fields
$article = ModMyModuleHelper::getArticleWithFields(123);
foreach ($article->jcfields as $field) {
    echo $field->label . ': ' . $field->value;
}

// Template override
<?php foreach ($this->item->jcfields as $field): ?>
    <div class="field-<?php echo $field->name; ?>">
        <?php echo $field->value; ?>
    </div>
<?php endforeach; ?>

// Direct query
$db = Factory::getContainer()->get(DatabaseInterface::class);
$query = $db->getQuery(true)
    ->select(['fv.*', 'f.label'])
    ->from('#__fields_values fv')
    ->innerJoin('#__fields f ON fv.field_id = f.id')
    ->where('fv.item_id = ' . $itemId);
$db->setQuery($query);
$values = $db->loadObjectList();
```

## Structure

```
/joomla-custom-fields/
├── SKILL.md                 (Main guide)
├── INDEX.md                 (Index)
├── README.md                (This file)
└── references/
    ├── ejemplos-practicos.php
    ├── base-datos.md
    ├── casos-uso.md
    └── faq-troubleshooting.md
```

## Statistics

- **SKILL.md:** 325 lines
- **References:** 1,730 lines of content
- **Code examples:** 40+ examples
- **Use cases:** 7 complete implementations
- **FAQ:** 11 questions + 13 problems

## Experience Level

- Beginner: Read full SKILL.md
- Intermediate: Follow reference cases
- Advanced: Work with ejemplos-practicos.php

## Skill Triggers

`joomla custom field`, `custom field`, `FieldsHelper`, `#__fields`, `joomla article fields`, `field group joomla`

---

March 2026 - Joomla 5/6
