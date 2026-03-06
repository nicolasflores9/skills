# Índice de Archivos de Referencia

Documentación y ejemplos completos para la Skill de Joomla Template Overrides.

---

## Archivos Disponibles

### 1. SKILL.md (Archivo Principal)

**Ubicación**: `/SKILL.md`

Documentación completa de 519 líneas cubriendo:

- Introducción y conceptos fundamentales
- Estructura de carpetas /html/
- Overrides de componentes (com_content)
  - Artículo individual
  - Categoría modo blog (blog_item.php)
  - Categoría modo lista
- Overrides de módulos (mod_login, etc.)
- Layouts alternativos
- JLayout y componentes reutilizables
- Child templates
- Field overrides (campos personalizados)
- Template Manager
- Buenas prácticas
- Troubleshooting
- Referencia rápida

**Contenido**: Teoría + ejemplos integrados de código

---

### 2. 01-com-content-article-completo.php

**Ubicación**: `/referencias/01-com-content-article-completo.php`

**Tipo**: Ejemplo de código completo

**Propósito**: Override completo de artículo individual (com_content)

**Ubicación en template**: `/templates/cassiopeia/html/com_content/article/default.php`

**Contiene**:
- Estructura semántica HTML5 (article, header, section)
- Breadcrumbs
- Título con schema.org markup
- Metadatos completos (autor, fecha, categoría)
- Imagen destacada con figcaption
- Contenido principal
- Campos personalizados (jcfields)
- Tags/etiquetas
- Navegación anterior/siguiente
- Plugin outputs

**Cambios implementados**:
- HTML semántico mejorado
- Campos personalizados accesibles
- Imagen responsiva
- Metadatos bien organizados
- SEO con schema.org

**Longitud**: ~250 líneas con documentación

**Ideal para**: Personalizar artículos individuales

---

### 3. 02-blog-item-personalizado.php

**Ubicación**: `/referencias/02-blog-item-personalizado.php`

**Tipo**: Ejemplo de código completo

**Propósito**: Override de artículo en listado de categoría (blog_item.php)

**Ubicación en template**: `/templates/cassiopeia/html/com_content/category/blog_item.php`

**Contiene**:
- Estructura de artículo individual en blog
- Imagen destacada responsiva
- Categoría con link
- Título con link
- Metadatos compactos (autor, fecha, visitas)
- Texto introductorio
- Campos personalizados (resumido, primeros 2)
- Botón "Leer más"
- Tags (resumido, primeros 3)

**Cambios implementados**:
- Diseño de tarjeta
- Metadatos en una línea
- Campos y tags limitados (resumido)
- UX mejorada con iconos
- Responsivo

**Longitud**: ~200 líneas con documentación

**Ideal para**: Personalizar listados de blog

**Nota**: Este archivo se incluye DENTRO de blog.php en un loop

---

### 4. 03-mod-login-avanzado.php

**Ubicación**: `/referencias/03-mod-login-avanzado.php`

**Tipo**: Ejemplo de código completo

**Propósito**: Override de módulo de login con funcionalidades avanzadas

**Ubicación en template**: `/templates/cassiopeia/html/mod_login/default.php`

**Contiene**:
- Renderizado condicional: usuario logueado vs no logueado
- Formulario de login moderno
  - Validación HTML5
  - Labels con aria-*
  - Placeholders
  - Autocomplete
- Usuario logueado: menú personalizado
- Recordar usuario (checkbox)
- Mensajes de error/éxito
- Enlace recuperar contraseña
- Enlace registro
- Enlace recuperar usuario
- Tokens CSRF

**Cambios implementados**:
- Accesibilidad mejorada
- Validación moderna
- UI diferente si está logueado
- Parámetros configurables
- Iconos fontawesome

**Longitud**: ~200 líneas con documentación

**Ideal para**: Personalizar login y UX de usuario

---

### 5. 04-jlayout-custom-card.php

**Ubicación**: `/referencias/04-jlayout-custom-card.php`

**Tipo**: Ejemplo de JLayout reutilizable

**Propósito**: Componente de tarjeta de artículo reutilizable en múltiples vistas

**Ubicación en template**: `/templates/cassiopeia/html/layouts/joomla/custom/article-card.php`

**Contiene**:
- Estructura reutilizable de tarjeta
- Imagen con badge de categoría
- Título con link
- Metadatos (autor, fecha)
- Contenido introductorio
- Botón "Leer más"

**Variables esperadas**:
- title, content, image, link
- category, author, date
- cssClass

**Uso**:
```php
echo JLayoutHelper::render('joomla.custom.article-card', [
    'title' => $item->title,
    'content' => $item->introtext,
    // ... más variables
]);
```

**Ventajas**:
- Reutilizar en blog_item.php, featured.php, módulos
- Cambiar diseño en un solo lugar
- Consistencia en todo el sitio

**Longitud**: ~300 líneas (incluye documentación y ejemplos de uso)

**Ideal para**: Crear componentes reutilizables

---

### 6. 05-child-template-config.xml

**Ubicación**: `/referencias/05-child-template-config.xml`

**Tipo**: Configuración XML

**Propósito**: Archivo de configuración completo para child template

**Ubicación en template**: `/templates/cassiopeia-child/templateDetails.xml`

**Contiene**:
- Metadata del template (nombre, versión, autor)
- Definición de parent template
- Lista de archivos del child
- Posiciones de módulos
- Parámetros configurables
  - Fieldset básico (logo, colores)
  - Fieldset layout (ancho, sidebar)
  - Fieldset componentes (breadcrumbs, títulos)
  - Fieldset avanzado (caché, compresión)
- Variables CSS

**Configuración de ejemplo**:
- Logo personalizable
- Colores del sitio
- Anchos de página
- Mostrar/ocultar elementos
- Caché y compresión

**Ventajas**:
- Hereda automáticamente de parent
- Solo almacena cambios
- Parámetros sin tocar código
- Múltiples child templates del mismo parent

**Longitud**: ~200 líneas con documentación

**Ideal para**: Crear child templates profesionales

---

### 7. 06-field-override-ejemplo.php

**Ubicación**: `/referencias/06-field-override-ejemplo.php`

**Tipo**: Ejemplo de código completo

**Propósito**: Override de campos personalizados con renderizado por tipo

**Ubicación en template**: `/templates/cassiopeia/html/layouts/com_fields/field/render.php`

**Contiene**:
- Renderizado condicional por tipo de campo:
  - text, email, url, integer, decimal
  - textarea, editor
  - radio, checkbox, list
  - calendar, date
  - file, image
- Etiqueta con indicador requerido
- Escapado seguro
- Validaciones

**Alternativas**:
- minimal.php: sin etiqueta, compacto
- card.php: en formato tarjeta

**Cómo usar**:
1. Copiar a `/templates/cassiopeia/html/layouts/com_fields/field/render.php`
2. En backend: Field Edit > Render Options > seleccionar layout

**Longitud**: ~250 líneas con documentación

**Ideal para**: Personalizar presentación de custom fields

---

### 8. GUIA-RAPIDA.md

**Ubicación**: `/referencias/GUIA-RAPIDA.md`

**Tipo**: Guía de referencia rápida

**Propósito**: Consulta rápida sin necesidad de leer SKILL.md completo

**Contiene**:
- Pasos básicos (4 pasos simples)
- Rutas comunes (componentes, módulos, JLayout)
- Variables útiles ($this->item, $this->params)
- Seguridad (escapado BUENO vs MALO)
- Layouts alternativos
- JLayout - crear y usar
- Troubleshooting común
- Child template estructura mínima
- Campos personalizados
- Documentar override
- Checklist de verificación
- Recursos útiles
- Errores comunes
- Comandos útiles
- Versiones soportadas

**Formato**: Markdown con tablas y ejemplos código cortos

**Longitud**: ~400 líneas

**Ideal para**: Referencia rápida mientras trabajas

---

### 9. README.md

**Ubicación**: `/README.md`

**Tipo**: Documentación del conjunto completo

**Propósito**: Explicar estructura y contenido de la skill

**Contiene**:
- Descripción general
- Estructura de archivos
- Temas cubiertos
- Cómo usar (por nivel: principiante, intermedio, avanzado)
- Características
- Puntos clave
- Casos de uso comunes
- Recursos externos
- FAQ (preguntas frecuentes)

**Longitud**: ~300 líneas

**Ideal para**: Entender la estructura completa

---

## Cómo Navegar los Archivos

### Por Tema

**Quiero personalizar artículos**
- Leer: SKILL.md sección "Overrides de Componentes - com_content"
- Ver: `/referencias/01-com-content-article-completo.php`
- Referencia: GUIA-RAPIDA.md sección "Componentes"

**Quiero modificar el listado de blog**
- Leer: SKILL.md sección "Categoría Modo Blog - blog_item.php"
- Ver: `/referencias/02-blog-item-personalizado.php`
- Referencia: GUIA-RAPIDA.md sección "Rutas Comunes"

**Quiero personalizar login**
- Leer: SKILL.md sección "Overrides de Módulos"
- Ver: `/referencias/03-mod-login-avanzado.php`
- Referencia: GUIA-RAPIDA.md sección "Módulos"

**Quiero crear componente reutilizable**
- Leer: SKILL.md sección "JLayout"
- Ver: `/referencias/04-jlayout-custom-card.php`
- Referencia: GUIA-RAPIDA.md sección "JLayout - Reutilizable"

**Quiero crear child template**
- Leer: SKILL.md sección "Child Templates"
- Ver: `/referencias/05-child-template-config.xml`
- Referencia: GUIA-RAPIDA.md sección "Child Template"

**Quiero personalizar campos**
- Leer: SKILL.md sección "Field Overrides"
- Ver: `/referencias/06-field-override-ejemplo.php`
- Referencia: GUIA-RAPIDA.md sección "Campos Personalizados"

### Por Experiencia

**Principiante**: GUIA-RAPIDA.md → SKILL.md → Ejemplo 01 → SKILL.md completo

**Intermedio**: SKILL.md → Ejemplos 02-03 → Aplicar en proyecto

**Avanzado**: Ejemplos 04-06 → Combinar técnicas → Crear soluciones personalizadas

### Por Urgencia

**Necesito ya**: GUIA-RAPIDA.md
**Necesito en 5 min**: SKILL.md (escanear índice)
**Necesito código listo**: Copiar ejemplo relevante
**Necesito entender bien**: Leer SKILL.md + ver ejemplo + hacer checklist

---

## Estructura de Directorios

```
joomla-template-overrides/
├── SKILL.md                              Archivo principal (519 líneas)
├── README.md                             Descripción del conjunto
└── referencias/
    ├── 01-com-content-article-completo.php
    ├── 02-blog-item-personalizado.php
    ├── 03-mod-login-avanzado.php
    ├── 04-jlayout-custom-card.php
    ├── 05-child-template-config.xml
    ├── 06-field-override-ejemplo.php
    ├── GUIA-RAPIDA.md                   Este archivo
    └── INDICE-REFERENCIAS.md            Este archivo
```

---

## Información de Archivos

| Archivo | Líneas | Tipo | Propósito |
|---------|--------|------|-----------|
| SKILL.md | 519 | MD + Código | Documentación completa |
| 01-*.php | 250 | PHP | Artículo individual |
| 02-*.php | 200 | PHP | Blog item |
| 03-*.php | 200 | PHP | Módulo login |
| 04-*.php | 300 | PHP | JLayout reutilizable |
| 05-*.xml | 200 | XML | Child template config |
| 06-*.php | 250 | PHP | Field override |
| GUIA-RAPIDA.md | 400 | MD | Referencia rápida |
| README.md | 300 | MD | Descripción general |
| INDICE-REFERENCIAS.md | Este | MD | Este índice |

**Total**: ~2,500+ líneas de documentación y código

---

## Características de Todos los Archivos

✓ Código completamente comentado
✓ Ejemplos prácticos
✓ Casos de uso reales
✓ Buenas prácticas integradas
✓ Seguridad (escapado, validación)
✓ Accesibilidad mejorada
✓ Responsive design
✓ Compatible con Joomla 5/6

---

## Próximos Pasos

1. **Lee**: GUIA-RAPIDA.md para entender estructura
2. **Mira**: Ejemplo relevante a tu necesidad
3. **Copia**: Código del ejemplo
4. **Adapta**: A tu proyecto específico
5. **Testea**: En navegador y mobile
6. **Documenta**: Cambios realizados
7. **Controla versiones**: Con git

---

## ¿Dónde Empezar?

**Por primera vez**: `/referencias/GUIA-RAPIDA.md` + ejemplo 01
**Experiencia media**: SKILL.md + ejemplo relevante
**Usuario avanzado**: Ejemplo 04/05/06 + combinar técnicas

---

**Última actualización**: Marzo 2024
**Versiones**: Joomla 5.x, 6.x
**Idioma**: Español

**¡Empieza ahora con la guía rápida!**
