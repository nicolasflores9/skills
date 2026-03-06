# Reference Files: Joomla 5/6 Plugins

This folder contains reference documentation and additional examples to complement the main skill content.

## Available Files

### 1. REFERENCIA_RAPIDA.md
**Purpose:** Quick reference during development
**Contents:**
- Plugin creation checklist
- Minimal copyable structure
- Main events table
- Common services (DB, User, App, etc.)
- Parameter examples
- Validation and filtering
- SQL queries
- Quick debugging
- Common errors and solutions
- Reference tables

**Ideal for:** Quickly finding how to do something specific

### 2. SNIPPETS.md
**Purpose:** Ready-to-copy-and-paste code
**Contents:**
- Minimal plugin template (manifest + provider + Extension)
- Accessing common services (DB, user, application, cache)
- Common event patterns
- Input validation and filtering
- Error handling
- Internationalization
- Plugin configuration
- Advanced queries
- Dependency injection
- Implementation checklist

**Ideal for:** Quickly copying base code and adapting it

### 3. EJEMPLOS_AVANZADOS.md
**Purpose:** Complete examples of real plugins
**Contents:**
- System Plugin with Logger
- User Plugin with Email
- Plugin with advanced DI
- Content Plugin with typed Event Classes
- Plugin with Validation
- System Plugin with Multiple Events

**Ideal for:** Learning advanced patterns with functional examples

### 4. TROUBLESHOOTING.md
**Purpose:** Problem resolution
**Contents:**
- Installation problems
- Class/namespace errors
- Event problems
- Configuration and translations
- Performance
- Security
- Compatibility
- Effective debugging

**Ideal for:** When something is not working and you need solutions

## Recommended Usage Flow

### New to Joomla plugins?
1. Read SKILL.md (main content)
2. Consult REFERENCIA_RAPIDA.md for structure
3. Copy a template from SNIPPETS.md
4. Adapt according to your use case

### Need to create something specific?
1. Open REFERENCIA_RAPIDA.md for the events table
2. Look for a similar pattern in SNIPPETS.md
3. If you need something more advanced, check EJEMPLOS_AVANZADOS.md

### Having a problem?
1. Open TROUBLESHOOTING.md
2. Find your symptom
3. Apply the suggested solutions
4. Check SNIPPETS.md if you need correct code

### Want to learn advanced patterns?
1. Read relevant sections in SKILL.md
2. Study EJEMPLOS_AVANZADOS.md
3. Adapt to your use case

## Naming Conventions

### Plugin Groups
- **system** - System events
- **content** - Content/article events
- **user** - User events
- **editor** - Editor events
- **installer** - Installation events

### Naming
```
plg_[group]_[name]

Examples:
- plg_system_helloworld
- plg_content_shortcodes
- plg_user_email
```

### Namespaces
```
MyCompany\Plugin\[Group]\[Name]

Examples:
- MyCompany\Plugin\System\Helloworld
- MyCompany\Plugin\Content\Shortcodes
- MyCompany\Plugin\User\Email
```

## Events by Category

### System Events (onXxx)
onAfterInitialise, onAfterRoute, onAfterDispatch, onBeforeRender, onBeforeCompileHead, onAfterRender

### Content Events (onXxx)
onContentPrepare, onContentAfterTitle, onContentBeforeSave, onContentAfterSave, onContentBeforeDelete, onContentAfterDelete, onContentChangeState

### User Events (onXxx)
onUserBeforeSave, onUserAfterSave, onUserBeforeDelete, onUserAfterDelete, onUserLogin, onUserLogout

## Recommended Structure

```
plg_group_name/
├── manifest.xml              # Configuration and installation
├── services/
│   └── provider.php          # Dependency injection
├── src/
│   ├── Extension/
│   │   └── Name.php         # Main class
│   ├── Event/               # Optional: Custom event classes
│   └── Helper/              # Optional: Helper classes
└── language/
    ├── en-GB/
    │   ├── plg_group_name.ini
    │   └── plg_group_name.sys.ini
    └── es-ES/
        ├── plg_group_name.ini
        └── plg_group_name.sys.ini
```

## Quick Checklist

To create a new plugin in 5 minutes:

1. [ ] Create folder: `plugins/[group]/[name]/`
2. [ ] Copy manifest.xml template from SNIPPETS.md
3. [ ] Copy services/provider.php template
4. [ ] Copy Extension class template
5. [ ] Create .ini files in language/en-GB/
6. [ ] Change namespace in 3 files (manifest, provider, Extension)
7. [ ] Control Panel > Extensions > Plugins > Enable
8. [ ] Verify in logs/joomla.log

## External Resources

- [Official Joomla Manual](https://manual.joomla.org/)
- [Plugin Documentation](https://docs.joomla.org/Plugin)
- [Joomla API Documentation](https://api.joomla.org/)
- [Community Forum](https://forum.joomla.org/)

## Supported Versions

This documentation covers:
- Joomla 5.0+
- Joomla 6.0+ (when available)

Specific features:
- SubscriberInterface: Joomla 4.4+
- Event Classes: Joomla 5.2+
- PSR-4 Namespaces: Joomla 4.0+

## Version History

**v1.0.0** - March 6, 2025
- Complete initial documentation
- 5 reference files
- Joomla 5/6 coverage
- 80+ code examples

## Contributions

If you find errors or have suggestions, please:
1. Report the issue
2. Provide code examples
3. Suggest improvements

---

**Last updated:** March 6, 2025
**Documentation version:** 1.0.0
**Focus:** Joomla 5/6 - Modern Patterns
