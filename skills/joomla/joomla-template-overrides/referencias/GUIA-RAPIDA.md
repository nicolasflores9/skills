# Guía Rápida - Template Overrides en Joomla 5/6

## Pasos Básicos para Crear un Override

### 1. Ubicar el Archivo Original
```bash
# Ejemplo: Artículo individual
/components/com_content/views/article/tmpl/default.php
```

### 2. Crear Estructura en Template
```
/templates/cassiopeia/html/
└── com_content/
    └── article/
        └── default.php  (copia aquí)
```

### 3. Copiar y Modificar
- Copiar archivo original a la ruta override
- Hacer cambios necesarios
- Escapar outputs correctamente
- Documentar cambios en cabecera

### 4. Probar
```bash
# Limpiar caché en backend: System > Clear Cache
# O por línea de comando:
php -r "JFactory::getCache()->clean();"
```

---

## Rutas Comunes

### Componentes

```
ARTÍCULO INDIVIDUAL
Original:  /components/com_content/views/article/tmpl/default.php
Override:  /templates/[t]/html/com_content/article/default.php

CATEGORÍA - BLOG
Original:  /components/com_content/views/category/tmpl/blog.php
Override:  /templates/[t]/html/com_content/category/blog.php

CATEGORÍA - BLOG ITEM
Original:  /components/com_content/views/category/tmpl/blog_item.php
Override:  /templates/[t]/html/com_content/category/blog_item.php

CATEGORÍA - LISTA
Original:  /components/com_content/views/category/tmpl/default.php
Override:  /templates/[t]/html/com_content/category/default.php
```

### Módulos

```
LOGIN
Original:  /modules/mod_login/tmpl/default.php
Override:  /templates/[t]/html/mod_login/default.php

MENÚ
Original:  /modules/mod_menu/tmpl/default.php
Override:  /templates/[t]/html/mod_menu/default.php

CUSTOM
Original:  /modules/mod_custom/tmpl/default.php
Override:  /templates/[t]/html/mod_custom/default.php
```

### JLayout

```
IMAGEN DESTACADA
Original:  /layouts/joomla/content/intro_image.php
Override:  /templates/[t]/html/layouts/joomla/content/intro_image.php

INFO BLOCK
Original:  /layouts/joomla/content/info_block.php
Override:  /templates/[t]/html/layouts/joomla/content/info_block.php
```

---

## Variables Útiles

### Artículos ($this->item)

```php
$this->item->id                    // ID del artículo
$this->item->title                 // Título
$this->item->slug                  // URL slug
$this->item->introtext             // Texto introductorio
$this->item->text                  // Contenido principal
$this->item->images                // JSON con imágenes
$this->item->publish_up            // Fecha publicación
$this->item->author                // Nombre del autor
$this->item->author_email          // Email del autor
$this->item->category_title        // Categoría
$this->item->jcfields              // Campos personalizados (array)
$this->item->tags->itemTags        // Tags/etiquetas
$this->item->link                  // URL del artículo
$this->item->hits                  // Número de visitas
```

### Parámetros ($this->params)

```php
$this->params->get('show_author')          // Mostrar autor
$this->params->get('show_category')        // Mostrar categoría
$this->params->get('show_publish_date')    // Mostrar fecha
$this->params->get('show_hits')            // Mostrar visitas
$this->params->get('show_tags')            // Mostrar tags
```

### Módulos ($this->module, $this->params)

```php
$this->module->id                  // ID del módulo
$this->module->title               // Título del módulo
$this->params->get('key')          // Parámetro específico
```

---

## Seguridad - Escapado

### BUENO ✓
```php
<?php echo htmlspecialchars($item->title, ENT_QUOTES, 'UTF-8'); ?>
<?php echo JHtml::_('string.truncate', $item->text, 100); ?>
<?php echo JRoute::_('index.php?option=com_content&view=article&id=' . $item->id); ?>
<?php echo JText::_('COM_CONTENT_READ_MORE'); ?>
```

### MALO ✗
```php
<?php echo $item->title; ?>
<?php echo $item->introtext; ?>
<?php echo 'index.php?option=com_content&id=' . $item->id; ?>
```

---

## Layouts Alternativos

### Crear Alternativa
```
/templates/cassiopeia/html/mod_login/
├── default.php     (layout original)
└── grid.php        (layout alternativo)
```

### Seleccionar en Backend
1. Ir a Extensions > Modules
2. Editar módulo
3. Tab "Advanced"
4. Layout: seleccionar "grid"

---

## JLayout - Reutilizable

### Crear
```php
// /templates/cassiopeia/html/layouts/joomla/custom/article-card.php
<?php defined('_JEXEC') or die;
$title = $displayData['title'] ?? '';
$content = $displayData['content'] ?? '';
?>
<article class="card">
    <h3><?php echo htmlspecialchars($title); ?></h3>
    <p><?php echo $content; ?></p>
</article>
```

### Usar
```php
<?php
echo JLayoutHelper::render('joomla.custom.article-card', [
    'title' => $this->item->title,
    'content' => $this->item->introtext,
]);
?>
```

---

## Troubleshooting

### Override No Funciona
1. Verificar ruta exacta en `/templates/[activo]/html/`
2. Limpiar caché: System > Clear Cache
3. Verificar permisos: `chmod 755 /templates/cassiopeia/html/`
4. Verificar sintaxis: `php -l archivo.php`
5. Revisar logs: `/logs/error.log`

### Permisos de Archivo
```bash
# Carpetas
chmod 755 /templates/cassiopeia/html/

# Archivos
chmod 644 /templates/cassiopeia/html/com_content/article/default.php
```

### Limpiar Caché
```bash
# Backend: System > Clear Cache
# O línea de comando:
rm -rf cache/*
```

---

## Child Template

### Estructura Mínima
```
/templates/cassiopeia-child/
├── html/
│   └── com_content/article/default.php
└── templateDetails.xml
```

### templateDetails.xml
```xml
<?xml version="1.0" encoding="utf-8"?>
<extension type="template" client="site">
    <name>Cassiopeia Child</name>
    <version>1.0.0</version>
    <parent>cassiopeia</parent>
    <files>
        <folder>html</folder>
    </files>
</extension>
```

---

## Campos Personalizados

### Override Layout
```php
// /templates/cassiopeia/html/layouts/com_fields/field/render.php
<?php defined('_JEXEC') or die;
$field = $displayData['field'] ?? null;
$value = $displayData['value'] ?? null;

if (!$field || !$value) return;
?>

<div class="field">
    <strong><?php echo htmlspecialchars($field->label); ?></strong>
    <div><?php echo $value; ?></div>
</div>
```

### Usar en Artículo
```php
<?php if (!empty($item->jcfields)): ?>
    <?php foreach ($item->jcfields as $field): ?>
        <div><?php echo htmlspecialchars($field->label); ?>:
             <?php echo $field->rawvalue; ?></div>
    <?php endforeach; ?>
<?php endif; ?>
```

---

## Documentar Override

Siempre incluir cabecera:

```php
<?php
/**
 * Override: Nombre descriptivo
 *
 * Vista original: ruta/original.php
 * Componente: com_content
 *
 * CAMBIOS:
 * - Cambio 1
 * - Cambio 2
 *
 * DEPENDENCIAS: campo personalizado 'autor-bio'
 * JOOMLA: 5.0+
 * FECHA: 2024-03-06
 */
defined('_JEXEC') or die;
```

---

## Checklist

- [ ] Ubicar archivo original
- [ ] Crear estructura `/html/` correcta
- [ ] Copiar archivo
- [ ] Realizar modificaciones
- [ ] Escapar outputs
- [ ] Documentar cambios
- [ ] Probar en navegador
- [ ] Limpiar caché
- [ ] Verificar en móvil
- [ ] Usar git/versionado

---

## Recursos Útiles

- [Joomla Docs - Template Overrides](https://docs.joomla.org/Understanding_Output_Overrides)
- Backend: Extensions > Templates > [Template] > Create Overrides
- Debugging: enable debug en configuration.php
- Logs: /logs/error.log

---

## Errores Comunes

**"Override no aparece"**
- Verificar ruta exacta
- Limpiar caché
- Revisar nombre de template activo

**"Variables vacías"**
- Verificar $this->item existe
- Validar antes de usar: `if (!empty($item->field))`
- Debug con `var_dump($item)`

**"Diseño roto"**
- Verificar CSS se carga
- Revisar etiquetas HTML cerradas
- Validar JavaScript no rompe

**"Permisos"**
```bash
chmod -R 755 /templates/cassiopeia/html/
chmod -R 644 /templates/cassiopeia/html/*.php
```

---

## Comandos Útiles

```bash
# Validar sintaxis PHP
php -l /templates/cassiopeia/html/com_content/article/default.php

# Buscar archivos originales
find /components -name "*.php" -path "*/tmpl/*"

# Comparar override con original
diff /components/com_content/views/article/tmpl/default.php \
     /templates/cassiopeia/html/com_content/article/default.php

# Permisos correctos
chmod 755 /templates/cassiopeia/html/
find /templates/cassiopeia/html -type f -exec chmod 644 {} \;
```

---

## Versiones Soportadas

- Joomla 5.0.x
- Joomla 6.0.x

(Completamente compatible con Joomla 4.x también)
