<?php
/**
 * Plugin SP Page Builder - Custom Addon Timeline
 * Archivo: plugins/sppagebuilder/addon_timeline/addon_timeline.php
 *
 * Permite agregar addon personalizado a SP Page Builder con CSS y JS propios
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;

class PlgSppagebuilderAddon_timeline extends JPlugin
{
    /**
     * Renderizar addon
     */
    public function render($addon)
    {
        // Obtener WebAssetManager
        $wa = Factory::getApplication()
              ->getDocument()
              ->getWebAssetManager();

        // Registrar y usar assets del addon
        $this->registerAssets($wa);

        // Obtener datos del addon
        $title = isset($addon->params->title) ? $addon->params->title : 'Timeline';
        $items = isset($addon->params->items) ? $addon->params->items : [];
        $alignment = isset($addon->params->alignment) ? $addon->params->alignment : 'left';

        // Renderizar HTML
        $html = $this->renderHTML($addon, $title, $items, $alignment);

        return $html;
    }

    /**
     * Registrar assets (CSS, JS)
     */
    private function registerAssets($wa)
    {
        $baseUrl = \Joomla\CMS\Uri\Uri::base(true);
        $pluginPath = '/plugins/sppagebuilder/addon_timeline';

        // Registrar CSS
        $wa->registerAndUseStyle(
            'addon-timeline-css',
            $pluginPath . '/css/timeline.css',
            ['version' => 'auto']
        );

        // Registrar JavaScript
        $wa->registerAndUseScript(
            'addon-timeline-js',
            $pluginPath . '/js/timeline.js',
            ['dependencies' => ['jquery']],
            ['defer' => true]
        );

        // Inline config
        $wa->addInlineScript('
            window.TimelineConfig = {
                animated: true,
                speed: 500
            };
        ');
    }

    /**
     * Renderizar HTML del addon
     */
    private function renderHTML($addon, $title, $items, $alignment)
    {
        $html = '<div class="sp-addon-timeline timeline-' . $alignment . '">';

        // Título
        if (!empty($title)) {
            $html .= '<h2 class="timeline-title">' . htmlspecialchars($title, ENT_COMPAT, 'UTF-8') . '</h2>';
        }

        // Items de timeline
        $html .= '<div class="timeline-items">';

        if (!empty($items)) {
            foreach ($items as $index => $item) {
                $date = isset($item->date) ? $item->date : '';
                $title_item = isset($item->title) ? $item->title : '';
                $description = isset($item->description) ? $item->description : '';
                $image = isset($item->image) ? $item->image : '';

                $html .= '<div class="timeline-item" data-index="' . $index . '">';
                $html .= '  <div class="timeline-marker">';
                $html .= '    <span class="marker-dot"></span>';
                $html .= '  </div>';
                $html .= '  <div class="timeline-content">';

                if (!empty($image)) {
                    $html .= '    <img src="' . htmlspecialchars($image, ENT_COMPAT, 'UTF-8')
                          . '" alt="' . htmlspecialchars($title_item, ENT_COMPAT, 'UTF-8')
                          . '" class="timeline-image">';
                }

                $html .= '    <div class="timeline-text">';

                if (!empty($date)) {
                    $html .= '      <span class="timeline-date">' . htmlspecialchars($date, ENT_COMPAT, 'UTF-8') . '</span>';
                }

                if (!empty($title_item)) {
                    $html .= '      <h3 class="timeline-item-title">' . htmlspecialchars($title_item, ENT_COMPAT, 'UTF-8') . '</h3>';
                }

                if (!empty($description)) {
                    $html .= '      <p class="timeline-description">' . $description . '</p>';
                }

                $html .= '    </div>';
                $html .= '  </div>';
                $html .= '</div>';
            }
        }

        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }

    /**
     * Parámetros del addon (para SP Page Builder UI)
     */
    public function getParams()
    {
        return [
            'group' => 'Contenido',
            'text' => 'Timeline',
            'params' => [
                [
                    'name' => 'title',
                    'label' => 'Título',
                    'type' => 'text',
                    'default' => 'Timeline'
                ],
                [
                    'name' => 'items',
                    'label' => 'Items',
                    'type' => 'repeatable',
                    'fields' => [
                        [
                            'name' => 'date',
                            'label' => 'Fecha',
                            'type' => 'text'
                        ],
                        [
                            'name' => 'title',
                            'label' => 'Título',
                            'type' => 'text'
                        ],
                        [
                            'name' => 'description',
                            'label' => 'Descripción',
                            'type' => 'textarea'
                        ],
                        [
                            'name' => 'image',
                            'label' => 'Imagen',
                            'type' => 'media'
                        ]
                    ]
                ],
                [
                    'name' => 'alignment',
                    'label' => 'Alineación',
                    'type' => 'select',
                    'options' => ['left' => 'Izquierda', 'center' => 'Centro', 'right' => 'Derecha'],
                    'default' => 'left'
                ]
            ]
        ];
    }
}
