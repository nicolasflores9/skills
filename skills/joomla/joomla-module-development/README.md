# Skill: Custom Module Development in Joomla 5/6

## Description

Complete skill on modern module development in Joomla 5 and 6. Covers modern architecture, PSR-4, namespaces, dependency injection, manifest.xml, templates, and practical examples.

## Skill Contents

### Main File
- **SKILL.md** (440 lines): Complete guide with fundamentals, structure, dependency injection, XML configuration, and templates.

### Reference Files

#### references/cheat-sheet.md
- Quick folder structure
- Ready-to-copy-paste PHP templates
- Common fields in manifest.xml
- Data access examples
- Safe HTML escaping
- Useful commands
- Common errors and solutions
- Quick testing and debugging

#### references/field-types.md
- Complete reference of all field types
- Available attributes for each type
- Complete configuration example with all fieldsets
- Accessing parameters in PHP

#### references/troubleshooting.md
- Installation problems and solutions
- Module rendering
- Database
- Parameters
- Security (XSS, SQL injection)
- Cross-version compatibility
- Performance
- Testing

### Code Examples

#### examples/mod_latest_articles_full.php
Complete functional module with:
- Main file (mod_latest_articles.php)
- manifest.xml with advanced parameters
- Service Provider (services/provider.php)
- Dispatcher (src/Dispatcher/Dispatcher.php)
- Helper with complex DB queries (src/Helper/ArticlesHelper.php)
- Templates with safe escaping (tmpl/default.php)
- Language files in English and Spanish

**Features**:
- Dependency injection
- Database access with joins
- Configurable parameters
- Built-in caching
- Data validation
- Implemented security
- Internationalization (i18n)

## Prerequisites

- Basic PHP knowledge (classes, namespaces)
- Joomla 5 or 6 installed
- Text editor/IDE (VS Code, PhpStorm, etc.)
- Joomla administrator access

## How to Use This Skill

### 1. Main Reading
Start with **SKILL.md** to understand:
- Module fundamentals
- Modern file structure
- PSR-4 namespaces
- Dependency injection

### 2. Quick Reference
For rapid development, use:
- **cheat-sheet.md**: Copy templates
- **field-types.md**: Look up field types
- **troubleshooting.md**: Solve problems

### 3. Functional Example
Study **mod_latest_articles_full.php**:
- Adapt the structure to your module
- Copy the services pattern
- Modify queries as needed

## Folder Structure

```
joomla-module-development/
├── SKILL.md                                    (Main guide)
├── README.md                                   (This file)
├── references/
│   ├── cheat-sheet.md                         (Quick templates)
│   ├── field-types.md                         (Field reference)
│   └── troubleshooting.md                     (Problems and solutions)
└── examples/
    └── mod_latest_articles_full.php           (Complete functional example)
```

## Content by Topic

### Fundamentals
- What is a module in Joomla
- Difference between modules vs components vs plugins
- Lifecycle
- Naming conventions

### Modern Architecture
- PSR-4 structure
- Directories: src/, tmpl/, language/, services/
- Required vs optional files
- Best practices

### PHP Language
- Namespaces
- Dependency Injection (DI)
- Joomla DI container
- Service Provider pattern

### Configuration
- Complete manifest.xml
- Parameters and fields
- Validation
- Internationalization

### Views and Rendering
- Templates (tmpl/)
- Layouts (default.xml)
- Available variables
- Escaping and security

### Database
- Access with DatabaseInterface
- Queries with Query Builder
- Joins and conditions
- SQL injection security

### Practical Examples
- Hello World module
- Module with DB access
- Module with advanced parameters
- Multi-language internationalization

## Features Covered

- ✅ PSR-4 Autoloading
- ✅ Dependency Injection
- ✅ Correct Namespaces
- ✅ ModuleDispatcherFactory
- ✅ HelperFactory
- ✅ Advanced XML Parameters
- ✅ Secure Templates
- ✅ Database Access
- ✅ Built-in Caching
- ✅ Internationalization (i18n)
- ✅ Data Validation
- ✅ HTML Escaping
- ✅ J5/J6 Compatibility

## Quick Guide to Create a Module

1. **Create structure**: Use templates in cheat-sheet.md
2. **Configure manifest.xml**: Copy structure, adapt names
3. **Implement Service Provider**: Register services
4. **Create Dispatcher**: Prepare data for template
5. **Write Helper**: Business logic
6. **Design Template**: Secure HTML with escaping
7. **Translate**: .ini files in language/
8. **Package**: Create ZIP with the structure
9. **Install**: Upload in Joomla admin
10. **Test and debug**: Use troubleshooting.md

## Differences Joomla 4 → 5 → 6

All content in this skill is compatible with Joomla 5 and 6. Joomla 4 requires minor adaptations in some commands, but the architecture is similar.

## External Resources

### Official Documentation
- https://manual.joomla.org/docs/building-extensions/modules/
- https://docs.joomla.org/Module_development_tutorial_(4.x)
- https://github.com/joomla/joomla-cms

### PHP Standards
- https://www.php-fig.org/psr/psr-4/ (PSR-4)
- https://www.php-fig.org/ (PHP-FIG)

### Community
- https://forum.joomla.org/ (Official forum)
- https://joomla.stackexchange.com/ (StackExchange)

## Skill Information

- **Created**: March 6, 2025
- **Language**: English
- **Joomla Versions**: 5.x, 6.x
- **Level**: Intermediate to Advanced
- **SKILL.md Lines**: 440 (under 500)
- **Reference files**: 3
- **Code examples**: 1 complete + multiple snippets

## Trigger Keywords

This skill is activated by keywords such as:
- joomla module
- create module
- module joomla
- mod_custom
- ModuleDispatcherFactory
- HelperFactory
- tmpl joomla
- manifest joomla
- PSR-4 joomla
- dependency injection

---

**Note**: To install this skill in Claude Code, download the folder and use the import skills function.
