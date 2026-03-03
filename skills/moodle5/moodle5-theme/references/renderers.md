# Renderers y componentes de output

## Tabla de contenidos
1. [Patrón renderable + renderer + template](#patrón)
2. [Crear un componente de output personalizado](#componente-personalizado)
3. [Sobreescribir renderers del core](#override-core)
4. [Sobreescribir renderers de plugins](#override-plugins)
5. [Interface named_templatable](#named-templatable)

## Patrón

El sistema de renderers conecta datos PHP con templates Mustache:

1. **Renderable**: Clase de datos que implementa `renderable` + `templatable`
2. **Renderer**: Invoca `export_for_template()` y `render_from_template()`
3. **Template Mustache**: La vista

## Componente personalizado

```php
// classes/output/hero_section.php
namespace theme_mytheme\output;

use renderable;
use renderer_base;
use templatable;
use stdClass;

class hero_section implements renderable, templatable {
    private string $title;
    private string $subtitle;
    private ?string $imageurl;

    public function __construct(string $title, string $subtitle, ?string $imageurl = null) {
        $this->title = $title;
        $this->subtitle = $subtitle;
        $this->imageurl = $imageurl;
    }

    /**
     * Retorna solo tipos simples: arrays, stdClass, bool, int, float, string.
     * Nunca objetos complejos.
     */
    public function export_for_template(renderer_base $output): stdClass {
        $data = new stdClass();
        $data->title = $this->title;
        $data->subtitle = $this->subtitle;
        $data->hasimage = !empty($this->imageurl);
        $data->imageurl = $this->imageurl;
        return $data;
    }
}
```

Uso desde un layout:
```php
$hero = new \theme_mytheme\output\hero_section('Bienvenido', 'Plataforma de aprendizaje', $imageurl);
echo $OUTPUT->render($hero);
```

## Override core

Requiere `$THEME->rendererfactory = 'theme_overridden_renderer_factory'` en `config.php`.

```php
// classes/output/core_renderer.php
namespace theme_mytheme\output;

defined('MOODLE_INTERNAL') || die();

class core_renderer extends \core_renderer {

    public function heading($text, $level = 2, $classes = 'main', $id = null) {
        $content = \html_writer::start_tag('div', ['class' => 'custom-heading-wrapper']);
        $content .= parent::heading($text, $level, $classes, $id);
        $content .= \html_writer::end_tag('div');
        return $content;
    }
}
```

Regla importante: dentro de renderers, usar siempre `$this->output` y `$this->page` en lugar de los globales `$OUTPUT` o `$PAGE`.

## Override plugins

La clase debe extender el renderer original:

```php
// classes/output/mod_quiz_renderer.php
namespace theme_mytheme\output;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/quiz/renderer.php');

class mod_quiz_renderer extends \mod_quiz_renderer {
    // Override de métodos específicos
}
```

## Named templatable

Si un renderable implementa `named_templatable` con `get_template_name()`, no se necesita un método explícito en el renderer — Moodle enruta automáticamente al template correcto:

```php
class hero_section implements renderable, named_templatable {
    // ...

    public function get_template_name(renderer_base $renderer): string {
        return 'theme_mytheme/hero_section';
    }
}
```
