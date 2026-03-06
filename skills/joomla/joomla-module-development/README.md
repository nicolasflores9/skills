# Skill: Desarrollo de Módulos Personalizados en Joomla 5/6

## Descripción

Skill completo sobre desarrollo moderno de módulos en Joomla 5 y 6. Cubre arquitectura moderna, PSR-4, namespaces, inyección de dependencias, manifest.xml, templates y ejemplos prácticos.

## Contenido del Skill

### Archivo Principal
- **SKILL.md** (440 líneas): Guía completa con fundamentos, estructura, inyección de dependencias, configuración XML y templates.

### Archivos de Referencia

#### references/cheat-sheet.md
- Estructura rápida de carpetas
- Templates PHP lista para copiar-pegar
- Campos comunes en manifest.xml
- Ejemplos de acceso a datos
- Escapado seguro de HTML
- Comandos útiles
- Errores comunes y soluciones
- Testing rápido y debugging

#### references/field-types.md
- Referencia completa de todos los tipos de campos
- Atributos disponibles para cada tipo
- Ejemplo completo de configuración con todos los fieldsets
- Acceso a parámetros en PHP

#### references/troubleshooting.md
- Problemas de instalación y soluciones
- Renderizado del módulo
- Base de datos
- Parámetros
- Seguridad (XSS, inyección SQL)
- Compatibilidad entre versiones
- Performance
- Testing

### Ejemplos de Código

#### examples/mod_latest_articles_full.php
Módulo completo funcional con:
- Archivo principal (mod_latest_articles.php)
- manifest.xml con parámetros avanzados
- Service Provider (services/provider.php)
- Dispatcher (src/Dispatcher/Dispatcher.php)
- Helper con queries BD complejas (src/Helper/ArticlesHelper.php)
- Templates con escapado seguro (tmpl/default.php)
- Archivos de idioma en inglés y español

**Características**:
- Inyección de dependencias
- Acceso a base de datos con join
- Parámetros configurables
- Caché integrado
- Validación de datos
- Seguridad implementada
- Internacionalización (i18n)

## Requisitos Previos

- Conocimiento básico de PHP (clases, namespaces)
- Joomla 5 o 6 instalado
- Editor de texto/IDE (VS Code, PhpStorm, etc.)
- Acceso a administrador de Joomla

## Cómo Usar Este Skill

### 1. Lectura Principal
Comenzar por **SKILL.md** para entender:
- Conceptos fundamentales de módulos
- Estructura moderna de archivos
- Namespace PSR-4
- Inyección de dependencias

### 2. Consulta Rápida
Para desarrollo rápido, usar:
- **cheat-sheet.md**: Copiar templates
- **field-types.md**: Buscar tipos de campos
- **troubleshooting.md**: Resolver problemas

### 3. Ejemplo Funcional
Estudiar **mod_latest_articles_full.php**:
- Adaptar estructura a tu módulo
- Copiar patrón de servicios
- Modificar queries según necesidades

## Estructura de Carpetas

```
joomla-module-development/
├── SKILL.md                                    (Guía principal)
├── README.md                                   (Este archivo)
├── references/
│   ├── cheat-sheet.md                         (Templates rápidos)
│   ├── field-types.md                         (Referencia de campos)
│   └── troubleshooting.md                     (Problemas y soluciones)
└── examples/
    └── mod_latest_articles_full.php           (Ejemplo completo funcional)
```

## Contenido por Tema

### Fundamentos
- Qué es un módulo en Joomla
- Diferencia módulos vs componentes vs plugins
- Ciclo de vida
- Convenciones de nombres

### Arquitectura Moderna
- Estructura PSR-4
- Directorios: src/, tmpl/, language/, services/
- Archivos obligatorios vs opcionales
- Buenas prácticas

### Lenguaje PHP
- Namespaces
- Inyección de dependencias (DI)
- Contenedor DI de Joomla
- Service Provider pattern

### Configuración
- manifest.xml completo
- Parámetros y campos
- Validación
- Internacionalización

### Vistas y Rendering
- Templates (tmpl/)
- Layouts (default.xml)
- Variables disponibles
- Escapado y seguridad

### Base de Datos
- Acceso con DatabaseInterface
- Queries con Query Builder
- Joins y condiciones
- Seguridad ante inyección SQL

### Ejemplos Prácticos
- Módulo Hello World
- Módulo con acceso a BD
- Módulo con parámetros avanzados
- Internacionalización multiidioma

## Características Cubiertas

- ✅ PSR-4 Autoloading
- ✅ Inyección de Dependencias
- ✅ Namespaces correcto
- ✅ ModuleDispatcherFactory
- ✅ HelperFactory
- ✅ Parámetros XML avanzados
- ✅ Templates seguras
- ✅ Acceso a base de datos
- ✅ Caché integrado
- ✅ Internacionalización (i18n)
- ✅ Validación de datos
- ✅ Escapado de HTML
- ✅ Compatibilidad J5/J6

## Guía Rápida para Crear un Módulo

1. **Crear estructura**: Usar templates en cheat-sheet.md
2. **Configurar manifest.xml**: Copiar estructura, adaptar nombres
3. **Implementar Service Provider**: Registrar servicios
4. **Crear Dispatcher**: Preparar datos para template
5. **Escribir Helper**: Lógica de negocio
6. **Diseñar Template**: HTML seguro con escapado
7. **Traducir**: Archivos .ini en language/
8. **Empaquetar**: Crear ZIP con la estructura
9. **Instalar**: Subir en Joomla admin
10. **Probar y depurar**: Usar troubleshooting.md

## Diferencias Joomla 4 → 5 → 6

Todo el contenido de este skill es compatible con Joomla 5 y 6. Joomla 4 requiere adaptaciones menores en algunos comandos, pero la arquitectura es similar.

## Recursos Externos

### Documentación Oficial
- https://manual.joomla.org/docs/building-extensions/modules/
- https://docs.joomla.org/Module_development_tutorial_(4.x)
- https://github.com/joomla/joomla-cms

### Estándares PHP
- https://www.php-fig.org/psr/psr-4/ (PSR-4)
- https://www.php-fig.org/ (PHP-FIG)

### Comunidad
- https://forum.joomla.org/ (Foro oficial)
- https://joomla.stackexchange.com/ (StackExchange)

## Información del Skill

- **Creado**: 6 de marzo de 2025
- **Lenguaje**: Español
- **Versiones Joomla**: 5.x, 6.x
- **Nivel**: Intermedio a Avanzado
- **Líneas SKILL.md**: 440 (bajo 500)
- **Archivos de referencia**: 3
- **Ejemplos código**: 1 completo + múltiples snippets

## Trigger Keywords

Este skill se activa con palabras clave como:
- módulo joomla
- crear módulo
- module joomla
- mod_custom
- ModuleDispatcherFactory
- HelperFactory
- tmpl joomla
- manifest joomla
- PSR-4 joomla
- inyección de dependencias

---

**Nota**: Para instalar este skill en Claude Code, descargar la carpeta y usar la función de importar skills.
