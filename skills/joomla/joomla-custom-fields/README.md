# Custom Fields en Joomla 5/6 - Skill Completo

Domina los campos personalizados en Joomla. Guía técnica completa con ejemplos, referencia de BD y casos de uso reales.

## Contenido

- **SKILL.md** - Guía principal (325 líneas, bajo 500 como requerido)
- **INDEX.md** - Índice de recursos y guía de navegación
- **references/** - 4 documentos de referencia complementarios

## Inicio Rápido

Carga campos personalizados en tu código:

```php
JLoader::register('FieldsHelper', JPATH_ADMINISTRATOR . '/components/com_fields/helpers/fields.php');
$fields = FieldsHelper::getFields('com_content.article', $article, true);

foreach ($fields as $field) {
    echo $field->label . ': ' . $field->value;
}
```

## Archivos

### SKILL.md (Guía Principal)
- 15 secciones temáticas
- Código PHP comentado
- Inicio rápido incluido
- Mejores prácticas

### Archivos de Referencia

1. **ejemplos-practicos.php** (420 líneas)
   - 8 ejemplos listos para usar
   - Componentes, módulos, plugins
   - Código comentado

2. **base-datos.md** (383 líneas)
   - Estructura de tablas
   - Consultas SQL comunes
   - Clase PHP de repositorio
   - Performance tips

3. **casos-uso.md** (525 líneas)
   - 7 casos del mundo real
   - Galerías, SEO, usuarios, etc.
   - Implementación completa

4. **faq-troubleshooting.md** (402 líneas)
   - 11 preguntas frecuentes
   - 13 problemas comunes
   - Soluciones paso a paso
   - Checklist de deployment

## Cómo Usar

1. Lee **SKILL.md** para entender conceptos
2. Consulta **INDEX.md** para navegación rápida
3. Copia ejemplos de **referencias/**
4. Busca tu caso en **casos-uso.md**
5. Resuelve problemas en **faq-troubleshooting.md**

## Requisitos

- Joomla 5/6
- PHP 8.0+
- MySQL 5.7+

## Topics Cubiertos

✓ Tipos de campos (16 total)
✓ API FieldsHelper
✓ Crear campos desde admin
✓ Base de datos (#__fields, #__fields_values)
✓ Renderizado en templates
✓ Uso en módulos y componentes
✓ Field Groups (grupos)
✓ Validación y filtros
✓ Eventos del sistema
✓ Acceso directo a BD
✓ REST API
✓ Troubleshooting

## Contextos Soportados

- com_content.article (Artículos)
- com_content.categories (Categorías)
- com_users.user (Usuarios)
- com_contact.contact (Contactos)
- Componentes personalizados

## Código Ejemplo

```php
// Módulo con Custom Fields
$article = ModMiModuloHelper::getArticleWithFields(123);
foreach ($article->jcfields as $field) {
    echo $field->label . ': ' . $field->value;
}

// Template override
<?php foreach ($this->item->jcfields as $field): ?>
    <div class="field-<?php echo $field->name; ?>">
        <?php echo $field->value; ?>
    </div>
<?php endforeach; ?>

// Consulta directa
$db = Factory::getContainer()->get(DatabaseInterface::class);
$query = $db->getQuery(true)
    ->select(['fv.*', 'f.label'])
    ->from('#__fields_values fv')
    ->innerJoin('#__fields f ON fv.field_id = f.id')
    ->where('fv.item_id = ' . $itemId);
$db->setQuery($query);
$values = $db->loadObjectList();
```

## Estructura

```
/joomla-custom-fields/
├── SKILL.md                 (Guía principal)
├── INDEX.md                 (Índice)
├── README.md                (Este archivo)
└── references/
    ├── ejemplos-practicos.php
    ├── base-datos.md
    ├── casos-uso.md
    └── faq-troubleshooting.md
```

## Estadísticas

- **SKILL.md:** 325 líneas
- **References:** 1,730 líneas de contenido
- **Ejemplos de código:** 40+ ejemplos
- **Casos de uso:** 7 implementaciones completas
- **FAQ:** 11 preguntas + 13 problemas

## Nivel de Experiencia

- Principiante: Lee SKILL.md completo
- Intermedio: Sigue casos de referencias/
- Avanzado: Trabaja con ejemplos-practicos.php

## Triggers del Skill

`campo personalizado joomla`, `custom field`, `FieldsHelper`, `#__fields`, `campos artículos joomla`, `field group joomla`

---

Marzo 2026 - Joomla 5/6
