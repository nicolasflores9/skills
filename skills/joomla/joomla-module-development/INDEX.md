# Índice de Contenidos - Skill Desarrollo de Módulos Joomla 5/6

## Estructura del Skill

```
joomla-module-development/
├── SKILL.md                    (334 líneas - Guía principal)
├── README.md                   (Información general del skill)
├── INDEX.md                    (Este archivo)
├── references/
│   ├── cheat-sheet.md          (Templates rápidos y comandos)
│   ├── field-types.md          (Referencia completa de campos)
│   └── troubleshooting.md      (Problemas, causas y soluciones)
└── examples/
    └── mod_latest_articles_full.php    (Módulo completo funcional)
```

## Contenido Detallado de SKILL.md (334 líneas)

### Secciones Principales

1. **Conceptos Fundamentales** (líneas 13-25)
   - Qué es un módulo
   - Características modernas: PSR-4, DI, Namespaces
   - Relación con componentes y plugins

2. **Estructura de Archivos Moderna** (líneas 27-44)
   - Diagrama de carpetas
   - Descripción de cada directorio
   - Convenciones de nombres (módulos, clases, métodos)

3. **Manifest.xml - Configuración Principal** (líneas 46-82)
   - Estructura XML completa y validada
   - Campos: nombre, autor, versión, descripción
   - Namespaces PSR-4
   - Archivos, idiomas, configuración
   - Tipos de campos: text, integer, category, list, radio, checkbox

4. **Inyección de Dependencias** (líneas 84-111)
   - Archivo services/provider.php
   - Registro de Dispatcher y Helper en el contenedor DI
   - Ventajas de la inyección

5. **Dispatcher - Control del Renderizado** (líneas 113-133)
   - Clase Dispatcher extiende AbstractModuleDispatcher
   - Método getLayoutData() preparación de datos
   - Inyección del Helper

6. **Helper - Lógica de Negocio** (líneas 135-166)
   - Clase ExampleHelper
   - Acceso a base de datos
   - Queries con DatabaseInterface
   - Método getItems() con filtros

7. **Templates - Renderizado HTML** (líneas 168-201)
   - Template tmpl/default.php
   - Archivo de configuración tmpl/default.xml
   - Variables disponibles en $displayData
   - Escapado seguro de HTML
   - Uso de HTMLHelper

8. **Archivo Principal** (líneas 203-209)
   - mod_ejemplo.php punto de entrada
   - Carga del layout
   - Integración con ModuleHelper

9. **Archivos de Idioma** (líneas 211-238)
   - Estructura de language/en-GB/
   - Archivos .ini y .sys.ini
   - Convención de nombres: MOD_[MODULO]_LABEL_[CAMPO]

10. **Ejemplo Completo: Hello World** (líneas 240-261)
    - Estructura mínima funcional
    - Paso a paso para crear e instalar
    - Empaquetado en ZIP

11. **Diferencias Joomla 4 → 5 → 6** (líneas 263-273)
    - Tabla comparativa de características
    - Compatibilidad
    - Cambios principales

12. **Checklist de Instalación** (líneas 275-283)
    - 8 puntos de verificación
    - Garantiza módulo funcional

13. **Mejores Prácticas** (líneas 285-296)
    - 5 puntos clave: Seguridad, Validación, Performance, Testabilidad, Documentación
    - Referencias a archivo cheat-sheet.md

## Contenido de references/cheat-sheet.md

- Estructura de carpetas con comandos bash
- 5 templates PHP lista para copiar-pegar:
  - manifest.xml minimal
  - Dispatcher básico
  - Helper con BD
  - Provider.php
- Campos comunes: text, integer, textarea, list, category, menu, modulelayout, cache
- Acceso a datos en templates
- Escapado seguro en HTML
- Comandos útiles (logs, caché, empaquetado)
- Tabla de 5 errores comunes
- Testing rápido y debugging

## Contenido de references/field-types.md

**Campos de Texto**:
- text, textarea, email, url, password

**Campos Numéricos**:
- integer, number

**Campos de Lista**:
- list, radio, checkbox, checkboxes

**Campos de Selección**:
- category, article, user, usergroup, menu, menuitem

**Campos Especiales**:
- sql, modulelayout, spacer, note, hidden

**Campos de Fecha/Hora**:
- calendar, text (date)

**Campos Avanzados**:
- color, range, editor, subform

Cada uno con:
- Sintaxis XML completa
- Atributos disponibles
- Valores y opciones

**Ejemplo Integrado**: Configuración con 3 fieldsets (basic, display, advanced)

**Acceso en PHP**: Cómo obtener parámetros en Dispatcher/Helper

## Contenido de references/troubleshooting.md

### Problemas de Instalación (4)
- Class not found → verificar namespace
- Module file not found → verificar archivo principal
- Invalid manifest → validar XML
- Parámetros no se guardan → verificar fieldset

### Renderizado (3)
- Módulo no aparece → checklist 4 puntos
- Template no renderiza → implementar getLayoutData()
- Undefined variable → acceder desde $displayData

### Base de Datos (3)
- Query sin resultados → debug y log
- Caracteres especiales → usar quoteName()
- Tabla personalizada → usar #__prefix

### Parámetros (2)
- No se guardan → estructura fieldset correcta
- Always devuelve default → nombre incorrecto

### Seguridad (2)
- XSS vulnerable → htmlspecialchars()
- Acceso no autorizado → validar estado y fechas

### Compatibilidad (2)
- No funciona en J6 → actualizar APIs deprecated
- No aparece en listado → validar manifest

### Performance (3)
- Módulo lento → habilitar caché, limitar queries
- Optimización → usar indexes en BD

### Testing (2)
- Testear sin instalar → archivo test.php
- Logs detallados → config.php debug

## Contenido de examples/mod_latest_articles_full.php

Módulo funcional de artículos recientes con:

**Archivos incluidos**:
1. mod_latest_articles.php (10 líneas)
2. manifest.xml (95 líneas) - completo con todos los parámetros
3. src/Service/Provider.php (30 líneas)
4. src/Dispatcher/Dispatcher.php (25 líneas)
5. src/Helper/ArticlesHelper.php (65 líneas)
6. tmpl/default.php (60 líneas)
7. tmpl/default.xml (10 líneas)
8. language/en-GB/mod_latest_articles.ini (26 líneas)
9. language/en-GB/mod_latest_articles.sys.ini (2 líneas)
10. language/es-ES/mod_latest_articles.ini (26 líneas)
11. language/es-ES/mod_latest_articles.sys.ini (2 líneas)

**Características implementadas**:
- Inyección de dependencias completa
- Queries complejas con JOINs
- Filtros por categoría, estado, fecha
- Ordenamiento: fecha, título, visitas
- Validación de parámetros
- Escapado seguro en HTML
- Caché integrado
- Internacionalización (inglés y español)
- Comments descriptivos

## Estadísticas del Skill

| Métrica | Valor |
|---------|-------|
| Líneas SKILL.md | 334 |
| Archivos de referencia | 3 |
| Campos cubiertos en field-types | 20+ |
| Ejemplos de código | 15+ |
| Problemas cubiertos en troubleshooting | 20+ |
| Líneas código ejemplo modulo | 400+ |
| Idiomas soportados | 2 (EN, ES) |

## Flujo de Aprendizaje Recomendado

### Nivel 1: Fundamentos (1 hora)
1. Leer SKILL.md secciones 1-3
2. Entender estructura de archivos
3. Revisar ejemplo Hello World

### Nivel 2: Inyección de Dependencias (1.5 horas)
1. Leer SKILL.md secciones 4-6
2. Comprender Service Provider
3. Estudiar ejemplo Dispatcher y Helper

### Nivel 3: Configuración Avanzada (1 hora)
1. Leer manifest.xml en SKILL.md
2. Consultar field-types.md para tipos específicos
3. Adaptar parámetros a tu necesidad

### Nivel 4: Desarrollo Completo (2 horas)
1. Estudiar mod_latest_articles_full.php
2. Adaptar estructura a tu módulo
3. Usar cheat-sheet.md para desarrollo rápido

### Nivel 5: Solución de Problemas (Según necesidad)
1. Consultar troubleshooting.md
2. Seguir checklist de instalación
3. Implementar soluciones sugeridas

## Palabras Clave Trigger

- módulo joomla
- crear módulo
- module joomla
- mod_custom
- ModuleDispatcherFactory
- HelperFactory
- tmpl joomla
- manifest.xml
- PSR-4 joomla
- inyección de dependencias joomla

## Próximas Mejoras (Futuros Updates)

- Ejemplos de módulos con AJAX
- Patrón MVC en módulos
- Hooks y eventos
- Testing unitario
- Integración con APIs externas

---

**Versión del Skill**: 1.0
**Última actualización**: 6 de marzo de 2025
**Mantenedor**: Claude Code
**Licencia**: Educativa/Referencia

