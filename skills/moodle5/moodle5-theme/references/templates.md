# Sistema de Templates Mustache

## Tabla de contenidos
1. [Sintaxis Mustache en Moodle](#sintaxis)
2. [Helpers propios de Moodle](#helpers)
3. [Bloques y herencia de templates](#bloques)
4. [Cómo sobreescribir templates](#sobreescribir)
5. [Buenas prácticas](#buenas-prácticas)
6. [Renderizado desde JavaScript](#renderizado-js)

## Sintaxis

Los templates residen en `templates/` de cada componente y se identifican por nombre Frankenstyle: `mod_lesson/timer` → `mod/lesson/templates/timer.mustache`.

```mustache
{{variable}}          {{! Escapado HTML }}
{{{variable}}}        {{! Sin escapar — solo para contenido ya procesado con format_text() }}
{{#variable}}...{{/variable}}   {{! Condicional / bucle }}
{{^variable}}...{{/variable}}   {{! Sección invertida (si variable es falsy/vacía) }}
{{> core/loading}}              {{! Partial — incluir otro template }}
```

## Helpers

Moodle extiende Mustache con helpers fundamentales:

```mustache
{{! Cadenas de idioma — equivale a get_string() }}
{{#str}} helloworld, mod_greeting {{/str}}
{{#str}} backto, core, {{name}} {{/str}}

{{! Iconos pix }}
{{#pix}} t/edit, core, Editar esta sección {{/pix}}

{{! JavaScript diferido al footer }}
{{#js}}
require(['theme_boost/form-display-errors'], function(module) {
    module.enhance({{#quote}}{{element.id}}{{/quote}});
});
{{/js}}

{{! Formateo de fechas con zona horaria del usuario }}
{{#userdate}} {{time}}, {{#str}} strftimedate, core_langconfig {{/str}} {{/userdate}}

{{! Truncar texto preservando palabras }}
{{#shortentext}} 15, {{{description}}} {{/shortentext}}

{{! IDs únicos para hooks JS }}
<div id="{{uniqid}}-mi-widget"></div>
```

El helper `{{#quote}}` es necesario cuando se pasan valores no escalares al helper `{{#str}}`:

```mustache
{{#str}} counteditems, core, { "count": {{count}}, "items": {{#quote}} {{itemname}} {{/quote}} } {{/str}}
```

## Bloques

Moodle habilita el pragma BLOCKS de Mustache para herencia de templates:

```mustache
{{! Template padre: tool_demo/section }}
<section>
    <h1>{{$heading}} Encabezado por defecto {{/heading}}</h1>
    <div>{{$content}} Contenido por defecto {{/content}}</div>
</section>

{{! Template hijo que extiende al padre }}
{{< tool_demo/section}}
    {{$heading}} Últimas noticias {{/heading}}
    {{$content}} Contenido personalizado {{/content}}
{{/ tool_demo/section}}
```

Usado extensivamente en el core (ej: `core/notification_error` extiende `core/notification_base`).

## Sobreescribir

Para sobreescribir un template, crear el archivo con la misma estructura dentro de `templates/` del theme, usando el nombre del componente como subdirectorio:

| Template original | Ruta de override en el theme |
|---|---|
| `lib/templates/modal.mustache` | `theme/mytheme/templates/core/modal.mustache` |
| `blocks/myoverview/templates/view-summary.mustache` | `theme/mytheme/templates/block_myoverview/view-summary.mustache` |
| `theme/boost/templates/navbar.mustache` | `theme/mytheme/templates/theme_boost/navbar.mustache` |
| `mod/wiki/templates/ratingui.mustache` | `theme/mytheme/templates/mod_wiki/ratingui.mustache` |

Los templates están cacheados agresivamente. Activar Theme Designer Mode o usar `$CFG->cachetemplates = false` durante el desarrollo.

### Boilerplate obligatorio

Todo template debe incluir licencia GPL, anotación `@template`, descripción de variables y ejemplo JSON:

```mustache
{{!
    This file is part of Moodle - http://moodle.org/

    Moodle is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    @template  theme_mytheme/hero_section
    @context   {
        "title": "Bienvenido",
        "subtitle": "Plataforma de aprendizaje",
        "hasimage": true,
        "imageurl": "https://example.com/hero.jpg"
    }
}}
<div class="theme-mytheme-hero-section" data-region="hero-section">
    <h1>{{title}}</h1>
    <p>{{subtitle}}</p>
    {{#hasimage}}
    <img src="{{imageurl}}" alt="" class="img-fluid">
    {{/hasimage}}
</div>
```

## Buenas prácticas

- **Un único nodo raíz** por template, con clase que coincida con el nombre del template
- **Data-attributes** para hooks de JS, no clases ni IDs: `data-region="hero-section"`
- **Clases de Bootstrap** directamente en los templates
- **Nunca** reutilizar nombres de helpers (`str`, `js`, `pix`, `quote`) como nombres de variables — fallarán silenciosamente
- Arrays PHP sin clave `[0]` o con huecos no son iterables en Mustache → usar `array_values()`
- Para testear arrays no vacíos, agregar un flag booleano como `hasusers` al contexto
- Activar `$CFG->debugtemplateinfo = true` para ver qué template se renderiza en cada sección (comentarios HTML)

## Renderizado JS

```javascript
import Templates from 'core/templates';
import {exception as displayException} from 'core/notification';

const context = { title: 'Bienvenido', subtitle: 'A nuestra plataforma' };
Templates.renderForPromise('theme_mytheme/hero_section', context)
    .then(({html, js}) => {
        Templates.appendNodeContents('.hero-container', html, js);
    })
    .catch((error) => displayException(error));
```
