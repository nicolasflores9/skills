# Joomla Template Overrides - Skill Completo

Sistema exhaustivo para personalizar la salida de componentes, módulos y plugins en Joomla 5/6 sin modificar archivos core.

## Contenido de la Skill

### Archivo Principal
- **SKILL.md** (480 líneas) - Documentación completa con ejemplos integrados

### Archivos de Referencia (exemplos/referencias)

1. **01-com-content-article-completo.php**
   - Override completo de artículo individual
   - Estructura semántica mejorada
   - Campos personalizados incluidos
   - Breadcrumbs, metadatos, tags, navegación

2. **02-blog-item-personalizado.php**
   - Override de artículo en blog de categoría
   - Renderizado de cada artículo en listado
   - Metadatos compactos, imagen responsiva
   - Campos resumidos, tags limitados

3. **03-mod-login-avanzado.php**
   - Override de módulo de login
   - Formulario moderno con validación
   - Usuario logueado vs no logueado
   - Accesibilidad mejorada, enlaces adicionales

4. **04-jlayout-custom-card.php**
   - JLayout reutilizable personalizado
   - Componente de tarjeta de artículo
   - Uso múltiple en diferentes vistas
   - Código comentado con ejemplos de uso

5. **05-child-template-config.xml**
   - Configuración completa de child template
   - Estructura y herencia de parent
   - Parámetros configurables
   - Posiciones de módulos

6. **06-field-override-ejemplo.php**
   - Override de campos personalizados
   - Rendimiento por tipo de campo
   - Layouts alternativos (minimal, card)
   - CSS recomendado

### Guía Rápida
- **GUIA-RAPIDA.md**
  - Pasos básicos para crear overrides
  - Rutas comunes (componentes, módulos, JLayout)
  - Variables útiles
  - Seguridad y escapado
  - Troubleshooting
  - Checklist
  - Comandos útiles

---

## Temas Cubiertos

### Conceptos Fundamentales
- Qué son los template overrides
- Por qué usarlos
- Cómo funciona el sistema
- Jerarquía de carga

### Overrides de Componentes (com_content)
- Artículos individuales
- Categoría modo blog (blog.php, blog_item.php)
- Categoría modo lista
- Otros componentes comunes

### Overrides de Módulos
- Estructura de módulos
- Módulos comunes (login, menu, custom, etc.)
- Crear override paso a paso
- Layouts alternativos

### Overrides de Plugins
- Requisitos para overridear
- Estructura plugin override
- Ejemplo plg_content_pagenavigation

### Layouts Alternativos
- Diferencia vs template override
- Crear para módulos
- Crear para componentes
- Seleccionar en backend

### JLayout - Componentes Reutilizables
- Concepto de JLayout
- Override de layouts joomla/
- Crear personalizado
- JLayoutHelper::render()

### Child Templates
- Concepto y ventajas
- Crear child template
- templateDetails.xml configuración
- Herencia automática

### Field Overrides
- Custom fields layout overrides
- Diferentes rendimientos por tipo
- Crear layout alternativo
- Seleccionar en backend

### Buenas Prácticas
- Documentación en código
- Control de versiones
- Testing post-actualizaciones
- Seguridad y escapado
- Rendimiento

### Troubleshooting
- Override no funciona
- Permisos de archivo
- Problemas de cacheo
- Debugging

---

## Cómo Usar Esta Skill

### Para Principiantes
1. Leer SKILL.md - Conceptos Fundamentales
2. Consultar GUIA-RAPIDA.md - Pasos básicos
3. Examinar ejemplo 01 (com_content/article)
4. Aplicar pasos en tu propio proyecto

### Para Intermedios
1. Revisar ejemplos específicos (blog_item, mod_login)
2. Crear overrides siguiendo estructura
3. Usar ejemplos como template
4. Adaptar a tus necesidades

### Para Avanzados
1. Estudiar JLayout (ejemplo 04)
2. Crear child template (ejemplo 05)
3. Implementar field overrides (ejemplo 06)
4. Combinar técnicas avanzadas

### Para Referencia Rápida
- Usar GUIA-RAPIDA.md
- Copiar estructura de ejemplos
- Buscar rutas de archivos
- Consultar variables útiles

---

## Estructura de Archivos

```
joomla-template-overrides/
├── SKILL.md                              (archivo principal - 480 líneas)
├── README.md                             (este archivo)
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

## Versiones Soportadas

- **Joomla 6.x** ✓
- **Joomla 5.x** ✓
- **Joomla 4.x** ✓ (compatible)

---

## Triggers para Activar la Skill

Preguntas que activan esta skill automáticamente:

- "override template joomla"
- "sobreescribir vista joomla"
- "blog_item.php"
- "com_content override"
- "JLayout"
- "alternative layout"
- "html override joomla"
- "template overrides"
- "child template joomla"
- "personalizar artículo joomla"
- "modificar módulo joomla"
- "campo personalizado joomla"
- "field override"
- "template manager"
- "layout alternativo"

---

## Características Principales

### Ejemplos Completos
- 6 ejemplos totalmente funcionales
- Comentarios extensos
- Casos de uso reales
- Código listo para copiar

### Documentación Clara
- Explicaciones en español
- Estructura lógica
- Visuaización ASCII de directorios
- Tablas comparativas

### Guía Rápida
- Pasos paso a paso
- Rutas de archivos comunes
- Variables útiles
- Comandos útiles

### Buenas Prácticas
- Seguridad y escapado
- Control de versiones
- Testing
- Rendimiento

### Troubleshooting
- Errores comunes
- Soluciones rápidas
- Debugging tips
- Permisos de archivo

---

## Puntos Clave

### La Ruta Override Más Importante
```
ORIGINAL: /components/com_content/views/article/tmpl/default.php
OVERRIDE: /templates/[template]/html/com_content/article/default.php
```

### Validar Siempre
```php
if (!empty($variable)) {
    // usar variable
}
```

### Escapar Siempre
```php
<?php echo htmlspecialchars($variable, ENT_QUOTES, 'UTF-8'); ?>
```

### Documentar Siempre
```php
<?php
/**
 * Override: Descripción
 * CAMBIOS: lista de cambios
 * JOOMLA: versión
 */
```

---

## Casos de Uso Comunes

1. **Personalizar artículos** → Usar ejemplo 01
2. **Modificar listado blog** → Usar ejemplo 02
3. **Cambiar formulario login** → Usar ejemplo 03
4. **Crear componente reutilizable** → Usar ejemplo 04
5. **Crear tema personalizado** → Usar ejemplo 05
6. **Mostrar campos custom** → Usar ejemplo 06

---

## Recursos Externos

- [Joomla Documentation](https://docs.joomla.org/Understanding_Output_Overrides)
- [Joomla Developer](https://developer.joomla.org)
- [Joomla Community Magazine](https://magazine.joomla.org)

---

## Información Técnica

- **Lenguaje**: PHP 8.0+
- **Framework**: Joomla Framework 5/6
- **Enfoque**: Imperatico, con código práctico
- **Líneas SKILL.md**: ~480
- **Ejemplos**: 6 archivos
- **Idioma**: Español

---

## Autor

Documentación compilada de:
- Joomla Official Documentation
- Joomla Community Magazine
- Best practices de la comunidad
- Experiencia práctica

---

## Última Actualización

Marzo 2024 - Joomla 5.x, 6.x compatible

---

## Licencia

Documentación libre para uso educativo y comercial.
Código de ejemplo: Creative Commons.

---

## Preguntas Frecuentes

**P: ¿Se pierden los overrides al actualizar Joomla?**
R: No. Los overrides están en `/templates/` que no se actualizan. Pueden necesitar revisión si los archivos core cambiaron.

**P: ¿Puedo tener múltiples overrides?**
R: Sí. Un override por archivo. Puedes también crear layouts alternativos.

**P: ¿Es seguro usar overrides?**
R: Completamente. Es la forma recomendada de personalizar Joomla.

**P: ¿Qué es mejor: override o layout alternativo?**
R: Depende. Override reemplaza, layout alternativo es seleccionable. Usa según necesites.

**P: ¿Cómo debuggear overrides?**
R: Activar debug en configuration.php, revisar `/logs/error.log`, usar `var_dump()`.

---

**¡Comienza ahora! Lee SKILL.md o consulta GUIA-RAPIDA.md para pasos rápidos.**
