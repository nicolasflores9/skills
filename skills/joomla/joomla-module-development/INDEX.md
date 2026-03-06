# Table of Contents - Joomla 5/6 Module Development Skill

## Skill Structure

```
joomla-module-development/
├── SKILL.md                    (334 lines - Main guide)
├── README.md                   (General skill information)
├── INDEX.md                    (This file)
├── references/
│   ├── cheat-sheet.md          (Quick templates and commands)
│   ├── field-types.md          (Complete field reference)
│   └── troubleshooting.md      (Problems, causes, and solutions)
└── examples/
    └── mod_latest_articles_full.php    (Complete functional module)
```

## Detailed Contents of SKILL.md (334 lines)

### Main Sections

1. **Fundamental Concepts** (lines 13-25)
   - What is a module
   - Modern features: PSR-4, DI, Namespaces
   - Relationship with components and plugins

2. **Modern File Structure** (lines 27-44)
   - Folder diagram
   - Description of each directory
   - Naming conventions (modules, classes, methods)

3. **Manifest.xml - Main Configuration** (lines 46-82)
   - Complete and validated XML structure
   - Fields: name, author, version, description
   - PSR-4 namespaces
   - Files, languages, configuration
   - Field types: text, integer, category, list, radio, checkbox

4. **Dependency Injection** (lines 84-111)
   - services/provider.php file
   - Registering Dispatcher and Helper in the DI container
   - Advantages of injection

5. **Dispatcher - Rendering Control** (lines 113-133)
   - Dispatcher class extends AbstractModuleDispatcher
   - getLayoutData() method for data preparation
   - Helper injection

6. **Helper - Business Logic** (lines 135-166)
   - ExampleHelper class
   - Database access
   - Queries with DatabaseInterface
   - getItems() method with filters

7. **Templates - HTML Rendering** (lines 168-201)
   - Template tmpl/default.php
   - Configuration file tmpl/default.xml
   - Variables available in $displayData
   - Safe HTML escaping
   - HTMLHelper usage

8. **Main File** (lines 203-209)
   - mod_ejemplo.php entry point
   - Layout loading
   - Integration with ModuleHelper

9. **Language Files** (lines 211-238)
   - Structure of language/en-GB/
   - .ini and .sys.ini files
   - Naming convention: MOD_[MODULE]_LABEL_[FIELD]

10. **Complete Example: Hello World** (lines 240-261)
    - Minimal functional structure
    - Step-by-step creation and installation
    - ZIP packaging

11. **Differences Joomla 4 → 5 → 6** (lines 263-273)
    - Feature comparison table
    - Compatibility
    - Main changes

12. **Installation Checklist** (lines 275-283)
    - 8 verification points
    - Ensures a functional module

13. **Best Practices** (lines 285-296)
    - 5 key points: Security, Validation, Performance, Testability, Documentation
    - References to cheat-sheet.md file

## Contents of references/cheat-sheet.md

- Folder structure with bash commands
- 5 ready-to-use PHP templates:
  - Minimal manifest.xml
  - Basic Dispatcher
  - Helper with DB
  - Provider.php
- Common fields: text, integer, textarea, list, category, menu, modulelayout, cache
- Data access in templates
- Safe HTML escaping
- Useful commands (logs, cache, packaging)
- Table of 5 common errors
- Quick testing and debugging

## Contents of references/field-types.md

**Text Fields**:
- text, textarea, email, url, password

**Numeric Fields**:
- integer, number

**List Fields**:
- list, radio, checkbox, checkboxes

**Selection Fields**:
- category, article, user, usergroup, menu, menuitem

**Special Fields**:
- sql, modulelayout, spacer, note, hidden

**Date/Time Fields**:
- calendar, text (date)

**Advanced Fields**:
- color, range, editor, subform

Each one with:
- Complete XML syntax
- Available attributes
- Values and options

**Integrated Example**: Configuration with 3 fieldsets (basic, display, advanced)

**PHP Access**: How to obtain parameters in Dispatcher/Helper

## Contents of references/troubleshooting.md

### Installation Problems (4)
- Class not found → verify namespace
- Module file not found → verify main file
- Invalid manifest → validate XML
- Parameters not saving → verify fieldset

### Rendering (3)
- Module not showing → 4-point checklist
- Template not rendering → implement getLayoutData()
- Undefined variable → access from $displayData

### Database (3)
- Query with no results → debug and log
- Special characters → use quoteName()
- Custom table → use #__prefix

### Parameters (2)
- Not saving → correct fieldset structure
- Always returns default → incorrect name

### Security (2)
- XSS vulnerable → htmlspecialchars()
- Unauthorized access → validate state and dates

### Compatibility (2)
- Not working in J6 → update deprecated APIs
- Not appearing in listing → validate manifest

### Performance (3)
- Slow module → enable cache, limit queries
- Optimization → use DB indexes

### Testing (2)
- Test without installing → test.php file
- Detailed logs → config.php debug

## Contents of examples/mod_latest_articles_full.php

Complete latest articles functional module with:

**Included Files**:
1. mod_latest_articles.php (10 lines)
2. manifest.xml (95 lines) - complete with all parameters
3. src/Service/Provider.php (30 lines)
4. src/Dispatcher/Dispatcher.php (25 lines)
5. src/Helper/ArticlesHelper.php (65 lines)
6. tmpl/default.php (60 lines)
7. tmpl/default.xml (10 lines)
8. language/en-GB/mod_latest_articles.ini (26 lines)
9. language/en-GB/mod_latest_articles.sys.ini (2 lines)
10. language/es-ES/mod_latest_articles.ini (26 lines)
11. language/es-ES/mod_latest_articles.sys.ini (2 lines)

**Implemented Features**:
- Complete dependency injection
- Complex queries with JOINs
- Filters by category, state, date
- Sorting: date, title, views
- Parameter validation
- Safe HTML escaping
- Built-in caching
- Internationalization (English and Spanish)
- Descriptive comments

## Skill Statistics

| Metric | Value |
|--------|-------|
| SKILL.md lines | 334 |
| Reference files | 3 |
| Fields covered in field-types | 20+ |
| Code examples | 15+ |
| Problems covered in troubleshooting | 20+ |
| Module example code lines | 400+ |
| Supported languages | 2 (EN, ES) |

## Recommended Learning Path

### Level 1: Fundamentals (1 hour)
1. Read SKILL.md sections 1-3
2. Understand file structure
3. Review Hello World example

### Level 2: Dependency Injection (1.5 hours)
1. Read SKILL.md sections 4-6
2. Understand Service Provider
3. Study Dispatcher and Helper example

### Level 3: Advanced Configuration (1 hour)
1. Read manifest.xml in SKILL.md
2. Consult field-types.md for specific types
3. Adapt parameters to your needs

### Level 4: Complete Development (2 hours)
1. Study mod_latest_articles_full.php
2. Adapt structure to your module
3. Use cheat-sheet.md for rapid development

### Level 5: Troubleshooting (As needed)
1. Consult troubleshooting.md
2. Follow installation checklist
3. Implement suggested solutions

## Trigger Keywords

- joomla module
- create module
- module joomla
- mod_custom
- ModuleDispatcherFactory
- HelperFactory
- tmpl joomla
- manifest.xml
- PSR-4 joomla
- joomla dependency injection

## Future Improvements (Upcoming Updates)

- AJAX module examples
- MVC pattern in modules
- Hooks and events
- Unit testing
- External API integration

---

**Skill Version**: 1.0
**Last Updated**: March 6, 2025
**Maintainer**: Claude Code
**License**: Educational/Reference
