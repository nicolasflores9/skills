# Migración Bootstrap 4 → Bootstrap 5.3

## Tabla de contenidos
1. [Equivalencias de clases CSS](#clases-css)
2. [Data attributes](#data-attributes)
3. [Cambios en SCSS/mixins](#scss)
4. [JavaScript sin jQuery](#javascript)
5. [Capa de compatibilidad BS4](#compatibilidad)

## Clases CSS

| Bootstrap 4 | Bootstrap 5 |
|---|---|
| `.ml-3`, `.mr-3` | `.ms-3`, `.me-3` |
| `.pl-3`, `.pr-3` | `.ps-3`, `.pe-3` |
| `.text-left`, `.text-right` | `.text-start`, `.text-end` |
| `.float-left`, `.float-right` | `.float-start`, `.float-end` |
| `.sr-only` | `.visually-hidden` |
| `.font-weight-bold` | `.fw-bold` |
| `.badge-primary` | `.text-bg-primary` |
| `.close` | `.btn-close` |
| `.form-group` | `.mb-3` |
| `.custom-select` | `.form-select` |

## Data attributes

Todos los data-attributes de componentes Bootstrap ahora llevan prefijo `bs`:

| Bootstrap 4 | Bootstrap 5 |
|---|---|
| `data-toggle="modal"` | `data-bs-toggle="modal"` |
| `data-target="#id"` | `data-bs-target="#id"` |
| `data-dismiss="alert"` | `data-bs-dismiss="alert"` |
| `data-toggle="dropdown"` | `data-bs-toggle="dropdown"` |
| `data-toggle="collapse"` | `data-bs-toggle="collapse"` |
| `data-toggle="tooltip"` | `data-bs-toggle="tooltip"` |
| `data-slide="next"` | `data-bs-slide="next"` |
| `data-ride="carousel"` | `data-bs-ride="carousel"` |

## SCSS

| Bootstrap 4 | Bootstrap 5 |
|---|---|
| `theme-color-level($color, $level)` | `shift-color($color, $percentage)` |
| `@include hover-focus()` | `&:hover, &:focus` |
| `media-breakpoint-down(sm)` | `media-breakpoint-down(md)` (desplazamiento de un breakpoint) |

## JavaScript

Bootstrap 5 ya no requiere jQuery. Los componentes se importan directamente:

```javascript
// Antes (BS4 + jQuery)
$('#my-dropdown').dropdown('toggle');

// Ahora (BS5 vanilla)
import Dropdown from 'theme_boost/bootstrap/dropdown';
const dd = new Dropdown('#my-dropdown');
dd.toggle();

// Otros componentes
import Modal from 'theme_boost/bootstrap/modal';
import Tooltip from 'theme_boost/bootstrap/tooltip';
import Collapse from 'theme_boost/bootstrap/collapse';
```

## Compatibilidad

Moodle incluye una capa de compatibilidad BS4 que estará disponible hasta **Moodle 6.0**:

```javascript
import * as BS4compat from 'theme_boost/bs4-compat';
BS4compat.init(document.querySelector('[data-region="my-region"]'));
```

Toda lógica nueva debe escribirse con sintaxis Bootstrap 5.3 para evitar deuda técnica.

### Tabla de deprecaciones

| Elemento | Disponible hasta |
|---|---|
| Clases CSS de Bootstrap 4 | Moodle 6.0 |
| Data-attributes sin prefijo `bs` | Moodle 6.0 |
| Módulos YUI de edición de cursos | Moodle 6.0 |
| `$THEME->javascripts` / `$THEME->javascripts_footer` | Moodle 6.0 (usar AMD) |
