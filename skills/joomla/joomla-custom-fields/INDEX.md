# Resource Index: Custom Fields in Joomla 5/6

## Main File

### SKILL.md (325 lines)
Complete technical guide on Custom Fields in Joomla 5/6. Covers:
- Quick start with code examples
- The 16 available field types
- FieldsHelper API (getFields, render)
- Creation from admin panel
- Database structure (#__fields, #__fields_values)
- Rendering in templates
- Usage in modules
- Integration in custom components
- Field Groups
- Supported contexts
- Validation and filters
- System events
- Best practices
- Troubleshooting

**Internal triggers:** joomla custom field, custom field, FieldsHelper, #__fields, joomla article fields, field group joomla

---

## Reference Files

### references/ejemplos-practicos.php (320 lines)
Complete PHP code with 8 practical ready-to-use examples:

1. **Load fields in a component** - How to extend a model to load Custom Fields
2. **Injection plugin** - Inject fields into custom component forms
3. **Module with Custom Fields** - Complete helper for a module that displays fields
4. **Database queries** - FieldValueRepository with full CRUD methods
5. **Template override** - Render fields with custom styling
6. **Custom validation** - Create JFormRule validation rules
7. **Module view** - HTML template to display fields in a module
8. **Access by name** - Index fields and access directly by name

**Features:**
- Commented and structured code
- Joomla interface implementation
- Dependency injection
- Error handling

### references/base-datos.md (320 lines)
Complete reference on the database structure:

**Tables:**
- #__fields - Field definitions
- #__fields_values - Stored values
- #__fields_groups - Field groups

**Contents:**
- SQL structure of each table
- Field descriptions (id, context, name, type, params, etc.)
- Common query examples (7 typical cases)
- JSON structure in params and fieldparams
- Complete PHP FieldsRepository class
- Performance considerations
- Versioning and migration

**Usefulness:** For developers who need to work directly with the database or understand the internal structure.

### references/casos-uso.md (380 lines)
7 practical real-world use cases:

1. **Image gallery for articles**
   - Repeatable fields and list of images
   - Template rendering

2. **Custom SEO per article**
   - SEO-specific fields
   - Plugin that injects meta tags

3. **Additional information in user registration**
   - Fields in com_users.user
   - Frontend validation plugin

4. **Content typology by category**
   - Different fields per category
   - Dynamic template

5. **Contacts with extended information**
   - Fields in com_contact.contact
   - Display module

6. **User dashboard**
   - Frontend field access
   - Custom profile

7. **REST API with Custom Fields**
   - Expose fields as JSON
   - API integration

**Features:**
- Complete implementation for each case
- HTML, PHP, SQL code
- Documented best practices

### references/faq-troubleshooting.md (290 lines)
Frequently asked questions and problem solving:

**FAQ (11 questions):**
- Loading fields in custom components
- Shared fields between contexts
- Server-side validation
- Storing multiple values
- Automatic Display vs manual rendering
- Migration between Joomla instances
- Dynamic fields from database
- Caching results
- Reordering fields
- And more...

**Troubleshooting (13 problems):**
- Fields not appearing in form
- Values not saving
- Template override not working
- Field appears without value
- Slow fields
- Repeatable field not working
- Access permissions
- Media field not showing image
- Custom validation not executing
- REST API returns "Field not found"
- Upgrade breaks fields
- And more...

**Debug Tools:**
- Inspect loaded fields
- Verify database
- System logs
- Third-party tools

**Deployment Checklist**

---

## Folder Structure

```
/mnt/skills/joomla-custom-fields/
├── SKILL.md                              (Main guide)
├── INDEX.md                              (This file)
└── references/
    ├── ejemplos-practicos.php            (8 code examples)
    ├── base-datos.md                     (DB structure and queries)
    ├── casos-uso.md                      (7 real-world use cases)
    └── faq-troubleshooting.md            (FAQ and troubleshooting)
```

---

## How to Use This Skill

### To Get Started Quickly
1. Read: **SKILL.md** - "Quick Start" section
2. Check: **references/ejemplos-practicos.php** - Example 1

### To Implement a Module
1. Read: **SKILL.md** - "Usage in Modules" section
2. Copy: **references/ejemplos-practicos.php** - Example 3
3. Adapt to your logic

### To Solve Problems
1. Search in: **references/faq-troubleshooting.md**
2. Check: **references/base-datos.md** for queries

### To Apply Complex Cases
1. Find your case in: **references/casos-uso.md**
2. Read the complete implementation
3. Copy the base code
4. Customize for your project

### To Work with the Database
1. Reference: **references/base-datos.md**
2. Copy queries from "Common Query Examples"
3. Adapt to your needs

---

## Covered Contexts

- `com_content.article` - Articles
- `com_content.categories` - Categories
- `com_users.user` - Users
- `com_contact.contact` - Contacts
- Custom components (com_*.*)

---

## Key Concepts Explained

- **FieldsHelper** - Central helper for Custom Fields
- **jcfields** - Array of loaded fields on an element
- **Context** - Identifier for where fields exist
- **Field Group** - Grouping of fields into tabs
- **rawvalue vs value** - Unprocessed value vs rendered HTML
- **#__fields** - Definitions table
- **#__fields_values** - Values table

---

## Technologies

- **PHP 8.0+** - Modern syntax with type hints
- **Joomla 5/6** - CMS Framework
- **MySQL 5.7+** - Database
- **JDatabase API** - Database queries
- **REST API** - Service integration

---

## Experience Levels

- **Beginner:** Read full SKILL.md + Examples 1 and 7
- **Intermediate:** Combine SKILL.md + 2-3 reference cases
- **Advanced:** Work directly with the database + custom plugins

---

## Last Updated

March 2026 - Joomla 5/6

## Recommended Next Topics

- Creating custom field types
- Custom Fields REST API
- Data migration between fields
- Performance optimization for large volumes
- Multilingual custom fields
