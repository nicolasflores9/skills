---
name: joomla-template-overrides
description: Domina el sistema de overrides de templates en Joomla 5/6. Personaliza vistas de componentes (com_content, artículos, categorías), módulos, plugins, crea layouts alternativos, usa JLayout para componentes reutilizables, implementa child templates y field overrides. Incluye ejemplos completos de blog_item.php, article/default.php, mod_login, alternativas de layout, buenas prácticas y troubleshooting.
---

# Joomla Template Overrides - Sistema Completo

## Introducción

Los template overrides permiten personalizar la salida de componentes, módulos y plugins sin modificar archivos core. Se almacenan en `/templates/[nombre]/html/` y se cargan automáticamente en lugar de los archivos originales.

**Cubre**: Joomla 5.x, 6.x | **Requisitos**: Conocimiento básico de PHP, estructura de carpetas Joomla

---

## Conceptos Fundamentales

### ¿Por qué usar Overrides?

- Personalizar presentación sin modificar core
- Mantener funcionalidad al actualizar Joomla
- Reutilizar código entre vistas
- Separar lógica de presentación

### Jerarquía de Carga

Joomla busca archivos en este orden:
1. `/templates/[activo]/html/[ruta]` → Override personalizado
2. `/[componente]/views/[vista]/tmpl/` → Archivo original

---

## Estructura de Carpetas /html/

```
/templates/cassiopeia/html/
├── com_content/              # Componentes (com_)
│   ├── article/
│   │   └── default.php
│   └── category/
│       ├── blog.php
│       ├── blog_item.php
│       └── default.php
├── mod_login/                # Módulos (mod_)
│   ├── default.php
│   └── slim.php
├── plg_content_pagenavigation/ # Plugins (plg_)
│   └── default.php
└── layouts/                  # JLayout reutilizables
    └── joomla/
        └── content/
            ├── intro_image.php
            └── info_block.php
```

**Convención de Nombres**:
- `com_[componente]` → componentes
- `mod_[modulo]` → módulos
- `plg_[grupo]_[plugin]` → plugins

---

## Overrides de Componentes - com_content

### Artículo Individual (article/default.php)

**Ubicación Original**: `/components/com_content/views/article/tmpl/default.php`
**Override**: `/templates/cassiopeia/html/com_content/article/default.php`

Variables disponibles:
- `$this->item` → objeto del artículo
- `$this->params` → parámetros
- `$this->item->jcfields` → campos personalizados

```php
<?php defined('_JEXEC') or die; ?>
<article class="article-container">
    <header class="article-header">
        <h1><?php echo htmlspecialchars($this->item->title); ?></h1>
    </header>

    <section class="article-body">
        <?php if (!empty($this->item->images)): ?>
            <?php echo JLayoutHelper::render('joomla.content.intro_image',
                ['item' => $this->item]); ?>
        <?php endif; ?>

        <?php echo $this->item->text; ?>
    </section>

    <?php if (!empty($this->item->jcfields)): ?>
        <section class="article-custom-fields">
            <?php foreach ($this->item->jcfields as $field): ?>
                <div class="field-<?php echo htmlspecialchars($field->type); ?>">
                    <strong><?php echo htmlspecialchars($field->label); ?></strong>
                    <?php echo $field->rawvalue; ?>
                </div>
            <?php endforeach; ?>
        </section>
    <?php endif; ?>
</article>
```

### Categoría Modo Blog - blog_item.php

**Ubicación**: `/templates/cassiopeia/html/com_content/category/blog_item.php`

Render cada artículo en el blog:

```php
<?php defined('_JEXEC') or die;
$item = $this->item; ?>

<article class="blog-item">
    <h2 class="item-title">
        <?php echo JHtml::_('link', JRoute::_($item->link),
            htmlspecialchars($item->title)); ?>
    </h2>

    <?php if (!empty($item->images)): ?>
        <?php echo JLayoutHelper::render('joomla.content.intro_image',
            ['item' => $item]); ?>
    <?php endif; ?>

    <div class="item-content">
        <?php echo $item->introtext; ?>
    </div>

    <a href="<?php echo JRoute::_($item->link); ?>" class="read-more">
        Leer más
    </a>
</article>
```

### Categoría Modo Lista - default.php

**Ubicación**: `/templates/cassiopeia/html/com_content/category/default.php`

Contenedor principal con tabla de artículos.

---

## Overrides de Módulos

### Estructura

Ubicación: `/templates/[template]/html/mod_[modulo]/[layout].php`

Módulos comunes:
- `mod_login` → formulario login
- `mod_menu` → menús
- `mod_custom` → contenido personalizado
- `mod_articles_latest` → artículos recientes
- `mod_breadcrumbs` → migas de pan

### Ejemplo: mod_login Override

**Ubicación**: `/templates/cassiopeia/html/mod_login/default.php`

```php
<?php defined('_JEXEC') or die;
$params = $this->params; ?>

<form action="<?php echo JRoute::_('index.php'); ?>" method="post" class="login-form">
    <div class="form-group">
        <label for="login-username">Usuario</label>
        <input type="text" name="username" id="login-username"
               class="form-control" required>
    </div>

    <div class="form-group">
        <label for="login-password">Contraseña</label>
        <input type="password" name="password" id="login-password"
               class="form-control" required>
    </div>

    <button type="submit" class="btn btn-primary">Entrar</button>

    <input type="hidden" name="option" value="com_users">
    <input type="hidden" name="task" value="user.login">
    <input type="hidden" name="return" value="<?php echo base64_encode(JUri::current()); ?>">
    <?php echo JHtml::_('form.token'); ?>
</form>
```

---

## Layouts Alternativos

Variaciónes seleccionables sin reemplazar completamente la vista.

### Diferencia vs Template Override

| Aspecto | Override | Layout Alternativo |
|---------|----------|-------------------|
| Aplicación | Automática sitio completo | Seleccionable por módulo |
| Archivo | default.php (reemplaza) | Nombre único (ej: grid.php) |
| Uso | Reemplaza vista original | Opción junto a original |

### Crear Layout Alternativo

**Para módulos**: Múltiples archivos en `/html/mod_[modulo]/`

```php
// /templates/cassiopeia/html/mod_login/grid.php
// Layout alternativo en grid para mod_login
<?php defined('_JEXEC') or die; ?>
<div class="login-grid">
    <!-- estructura en grid -->
</div>
```

**Para componentes**: En menú seleccionar "Alternative Layout"

Reglas de naming:
- No usar underscores
- Nombres descriptivos: `grid.php`, `minimal.php`, `card.php`
- `default.php` = layout original

---

## JLayout - Componentes Reutilizables

Sistema para crear fragmentos reutilizables.

**Ubicación de layouts Joomla**: `/layouts/joomla/[grupo]/[layout].php`

**Override**: `/templates/cassiopeia/html/layouts/joomla/[grupo]/[layout].php`

### Crear Layout Personalizado

```php
// /templates/cassiopeia/html/layouts/joomla/custom/article-card.php
<?php defined('_JEXEC') or die;
$title = $displayData['title'] ?? '';
$content = $displayData['content'] ?? '';
$image = $displayData['image'] ?? '';
$link = $displayData['link'] ?? '#';
?>

<article class="card">
    <?php if ($image): ?>
        <figure class="card-image">
            <img src="<?php echo htmlspecialchars($image); ?>"
                 alt="<?php echo htmlspecialchars($title); ?>">
        </figure>
    <?php endif; ?>

    <div class="card-body">
        <h3><?php echo htmlspecialchars($title); ?></h3>
        <div class="card-content">
            <?php echo $content; ?>
        </div>
        <a href="<?php echo htmlspecialchars($link); ?>" class="card-link">
            Ver más
        </a>
    </div>
</article>
```

### Uso en blog_item.php

```php
<?php echo JLayoutHelper::render('joomla.custom.article-card', [
    'title' => $this->item->title,
    'content' => $this->item->introtext,
    'image' => json_decode($this->item->images)->image_intro ?? '',
    'link' => JRoute::_($this->item->link),
]); ?>
```

---

## Child Templates

Template que hereda de uno padre, solo almacenando cambios.

### Crear Child Template

**Estructura**:
```
/templates/cassiopeia-child/
├── html/
│   └── com_content/article/default.php  (override personalizado)
├── css/
│   └── custom.css
└── templateDetails.xml
```

**templateDetails.xml**:
```xml
<?xml version="1.0" encoding="utf-8"?>
<extension type="template" client="site">
    <name>Cassiopeia Child</name>
    <version>1.0.0</version>
    <description>Child template basado en Cassiopeia</description>
    <parent>cassiopeia</parent>

    <files>
        <folder>html</folder>
        <folder>css</folder>
        <filename>templateDetails.xml</filename>
    </files>

    <positions>
        <position>header</position>
        <position>sidebar</position>
        <position>footer</position>
    </positions>
</extension>
```

**Ventajas**:
- Hereda automáticamente archivos no personalizados
- Solo almacena archivos modificados
- Facilita mantenimiento y actualizaciones
- Permite múltiples variaciones del mismo padre

---

## Field Overrides - Campos Personalizados

Override de cómo se muestran custom fields.

**Ubicación**: `/templates/[template]/html/layouts/com_fields/field/[layout].php`

```php
// /templates/cassiopeia/html/layouts/com_fields/field/render.php
<?php defined('_JEXEC') or die;
$field = $displayData['field'] ?? null;
$value = $displayData['value'] ?? null;

if (!$field || !$value) return;
?>

<div class="field-container" data-field-id="<?php echo (int)$field->id; ?>">
    <label class="field-label">
        <?php echo htmlspecialchars($field->label); ?>
    </label>
    <div class="field-value">
        <?php echo $value; ?>
    </div>
</div>
```

Seleccionar en backend: Field Edit > Render Options > Layout

---

## Template Manager - Crear Overrides

Backend: Extensions > Templates > [Template] > Create Overrides

Ventajas:
- Interfaz visual intuitiva
- Copia automáticamente archivos
- No requiere búsqueda manual
- Asegura estructura correcta

---

## Buenas Prácticas

### Documentación en Código

```php
<?php
/**
 * Override: Artículo personalizado
 *
 * Componente: com_content
 * Vista original: article/tmpl/default.php
 *
 * CAMBIOS:
 * - Estructura semántica mejorada
 * - Agregados campos personalizados
 * - Reordenados metadatos
 *
 * DEPENDENCIAS: Campo 'autor-bio' personalizado
 * JOOMLA: 5.0+
 * FECHA: 2024-03-06
 */
defined('_JEXEC') or die;
```

### Seguridad - Escapado

```php
// BUENO: Escapar outputs
<?php echo htmlspecialchars($item->title, ENT_QUOTES, 'UTF-8'); ?>
<?php echo JHtml::_('string.truncate', $item->text, 100); ?>

// BUENO: URLs con JRoute
<?php echo JRoute::_('index.php?option=com_content&view=article&id=' . $item->id); ?>

// MALO: Output sin escape
<?php echo $item->title; ?>
```

### Testing Post-Actualización

```bash
# Comparar overrides con core files
diff -u /components/com_content/views/article/tmpl/default.php \
         /templates/cassiopeia/html/com_content/article/default.php

# Backup overrides antes de actualizar
cp -r templates/cassiopeia/html templates/cassiopeia/html.backup
```

### Evitar Overrides Innecesarios

- Override solo si modificas la vista
- Usa layouts alternativos para variaciones
- Usa JLayout para componentes reutilizables
- Mantén control de cambios (git, documentación)

---

## Troubleshooting

### Override No Funciona

1. Verificar ruta correcta en `/templates/[activo]/html/`
2. Limpiar caché (System > Clear Cache)
3. Verificar permisos de archivos (755 carpetas, 644 archivos)
4. Verificar sintaxis PHP (php -l archivo.php)
5. Revisar error logs en `/logs/`

### Permisos de Archivo

```bash
# Carpetas: lectura+ejecución
chmod 755 /templates/cassiopeia/html/

# Archivos: lectura
chmod 644 /templates/cassiopeia/html/com_content/article/default.php
```

### Problemas de Cacheo

- Limpiar caché en backend: System > Clear Cache
- Opción en template: Style Edit > Caching
- Verificar router cache en configuration.php

---

## Caso Práctico: Artículos Featured

**Objetivo**: Mostrar artículos destacados en formato card

**Pasos**:
1. Crear override: `com_content/article/featured.php`
2. Crear JLayout: `layouts/joomla/custom/featured-card.php`
3. Usar en override con JLayoutHelper::render()
4. Asignar CSS personalizado

Ver ejemplos completos en `/referencias/`

---

## Referencia Rápida

### Rutas Comunes

| Elemento | Original | Override |
|----------|----------|----------|
| Artículo | `com_content/views/article/tmpl/default.php` | `html/com_content/article/default.php` |
| Blog item | `com_content/views/category/tmpl/blog_item.php` | `html/com_content/category/blog_item.php` |
| Categoría lista | `com_content/views/category/tmpl/default.php` | `html/com_content/category/default.php` |
| Módulo login | `modules/mod_login/tmpl/default.php` | `html/mod_login/default.php` |
| JLayout imagen | `layouts/joomla/content/intro_image.php` | `html/layouts/joomla/content/intro_image.php` |
| Plugin nav | `plugins/content/pagenavigation/tmpl/default.php` | `html/plg_content_pagenavigation/default.php` |

### Checklist - Crear Override

- [ ] Ubicar archivo original en core
- [ ] Crear estructura `/html/` en template
- [ ] Copiar archivo a ubicación override
- [ ] Realizar modificaciones
- [ ] Escapar outputs correctamente
- [ ] Documentar cambios en cabecera
- [ ] Probar en navegador
- [ ] Limpiar caché
- [ ] Verificar en diferentes resoluciones
- [ ] Usar Template Manager para verificar estructura

### Variables Útiles

```php
// Artículos
$this->item->id
$this->item->title
$this->item->introtext
$this->item->text
$this->item->images (JSON)
$this->item->jcfields (campos personalizados)

// Parámetros
$this->params->get('show_author')
$this->params->get('show_category')

// Módulos
$this->module->id
$this->module->title
$this->params

// JLayout
$displayData (array de datos pasados)
```

---

## Recursos Adicionales

- [Joomla Documentation - Template Overrides](https://docs.joomla.org/Understanding_Output_Overrides)
- [Template Manager en Backend](Extensions > Templates)
- [Joomla Magazine - Child Templates](https://magazine.joomla.org)
- [Debugging - developer.joomla.org](https://developer.joomla.org)
