/**
 * COMPLETE EXAMPLE: Latest Articles Module
 * Functional module with database access, caching, and advanced parameters
 */

// ===== mod_latest_articles.php (MAIN FILE)
<?php
defined('_JEXEC') or die;

use Joomla\CMS\Helper\ModuleHelper;

$layout = $params->get('layout', 'default');
$path = ModuleHelper::getLayoutPath('mod_latest_articles', $layout);
require $path;
?>

// ===== manifest.xml
<?xml version="1.0" encoding="UTF-8"?>
<extension type="module" client="site" method="upgrade">
    <name>MOD_LATEST_ARTICLES</name>
    <author>Your Name</author>
    <creationDate>2025-03-06</creationDate>
    <copyright>Copyright 2025</copyright>
    <license>GNU General Public License v2.0</license>
    <authorEmail>info@example.com</authorEmail>
    <version>1.0.0</version>
    <description>MOD_LATEST_ARTICLES_DESC</description>
    <namespace path="src">Joomla\Module\LatestArticles</namespace>

    <files>
        <filename module="mod_latest_articles">mod_latest_articles.php</filename>
        <folder>language</folder>
        <folder>src</folder>
        <folder>services</folder>
        <folder>tmpl</folder>
    </files>

    <languages>
        <language tag="en-GB">language/en-GB/mod_latest_articles.ini</language>
        <language tag="en-GB">language/en-GB/mod_latest_articles.sys.ini</language>
        <language tag="es-ES">language/es-ES/mod_latest_articles.ini</language>
        <language tag="es-ES">language/es-ES/mod_latest_articles.sys.ini</language>
    </languages>

    <config>
        <fields name="params">
            <fieldset name="basic" label="MOD_LATEST_ARTICLES_FIELDSET_BASIC">
                <field name="titulo" type="text"
                    label="MOD_LATEST_ARTICLES_TITULO"
                    description="MOD_LATEST_ARTICLES_TITULO_DESC"
                    default="Latest Articles"
                    size="50" />

                <field name="cantidad" type="integer"
                    label="MOD_LATEST_ARTICLES_CANTIDAD"
                    description="MOD_LATEST_ARTICLES_CANTIDAD_DESC"
                    default="5"
                    min="1"
                    max="50" />

                <field name="categoria" type="category"
                    label="MOD_LATEST_ARTICLES_CATEGORIA"
                    description="MOD_LATEST_ARTICLES_CATEGORIA_DESC"
                    extension="com_content" />

                <field name="mostrar_fecha" type="radio"
                    label="MOD_LATEST_ARTICLES_MOSTRAR_FECHA"
                    default="1"
                    class="btn-group">
                    <option value="1">JNO</option>
                    <option value="1">JYES</option>
                </field>

                <field name="mostrar_autor" type="radio"
                    label="MOD_LATEST_ARTICLES_MOSTRAR_AUTOR"
                    default="1"
                    class="btn-group">
                    <option value="0">JNO</option>
                    <option value="1">JYES</option>
                </field>

                <field name="mostrar_resumen" type="radio"
                    label="MOD_LATEST_ARTICLES_MOSTRAR_RESUMEN"
                    default="1"
                    class="btn-group">
                    <option value="0">JNO</option>
                    <option value="1">JYES</option>
                </field>

                <field name="longitud_resumen" type="integer"
                    label="MOD_LATEST_ARTICLES_LONGITUD_RESUMEN"
                    default="100"
                    min="10"
                    max="500" />
            </fieldset>

            <fieldset name="display" label="MOD_LATEST_ARTICLES_FIELDSET_DISPLAY">
                <field name="orden" type="list"
                    label="MOD_LATEST_ARTICLES_ORDEN"
                    default="fecha"
                    class="input-medium">
                    <option value="fecha">MOD_LATEST_ARTICLES_ORDEN_FECHA</option>
                    <option value="titulo">MOD_LATEST_ARTICLES_ORDEN_TITULO</option>
                    <option value="visitas">MOD_LATEST_ARTICLES_ORDEN_VISITAS</option>
                </field>

                <field name="direccion" type="radio"
                    label="MOD_LATEST_ARTICLES_DIRECCION"
                    default="desc"
                    class="btn-group">
                    <option value="asc">MOD_LATEST_ARTICLES_DIRECCION_ASC</option>
                    <option value="desc">MOD_LATEST_ARTICLES_DIRECCION_DESC</option>
                </field>

                <field name="mostrar_imagen" type="radio"
                    label="MOD_LATEST_ARTICLES_MOSTRAR_IMAGEN"
                    default="1"
                    class="btn-group">
                    <option value="0">JNO</option>
                    <option value="1">JYES</option>
                </field>

                <field name="tamanio_imagen" type="text"
                    label="MOD_LATEST_ARTICLES_TAMANIO_IMAGEN"
                    default="100x100"
                    description="MOD_LATEST_ARTICLES_TAMANIO_IMAGEN_DESC" />
            </fieldset>

            <fieldset name="advanced">
                <field name="layout" type="modulelayout"
                    label="JFIELD_ALT_LAYOUT_LABEL"
                    description="JFIELD_ALT_LAYOUT_DESC" />

                <field name="cache" type="list"
                    label="JFIELD_CACHING_LABEL"
                    default="1">
                    <option value="0">JNO</option>
                    <option value="1">JYES</option>
                </field>

                <field name="cache_time" type="integer"
                    label="JFIELD_CACHE_TIME_LABEL"
                    default="900" />

                <field name="moduleclass_sfx" type="text"
                    label="COM_MODULES_FIELD_MODULECLASS_SFX_LABEL"
                    description="COM_MODULES_FIELD_MODULECLASS_SFX_DESC" />
            </fieldset>
        </fields>
    </config>
</extension>

// ===== src/Service/Provider.php
<?php
namespace Joomla\Module\LatestArticles\Service;

use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Module\LatestArticles\Dispatcher\Dispatcher;
use Joomla\Module\LatestArticles\Helper\ArticlesHelper;

class Provider implements ServiceProviderInterface
{
    public function register(Container $container): void
    {
        $container->set(
            Dispatcher::class,
            function (Container $c) {
                return new Dispatcher(
                    $c->get(ArticlesHelper::class)
                );
            }
        );

        $container->set(
            ArticlesHelper::class,
            function (Container $c) {
                return new ArticlesHelper(
                    $c->get('db'),
                    $c->get('app')
                );
            }
        );
    }
}
?>

// ===== src/Dispatcher/Dispatcher.php
<?php
namespace Joomla\Module\LatestArticles\Dispatcher;

use Joomla\CMS\Dispatcher\AbstractModuleDispatcher;
use Joomla\Module\LatestArticles\Helper\ArticlesHelper;

class Dispatcher extends AbstractModuleDispatcher
{
    private $helper;

    public function __construct(ArticlesHelper $helper)
    {
        $this->helper = $helper;
    }

    protected function getLayoutData(): array
    {
        $data = parent::getLayoutData();

        $params = $this->module->params;
        $cantidad = (int) $params->get('cantidad', 5);
        $categoria = $params->get('categoria', null);
        $orden = $params->get('orden', 'fecha');
        $direccion = $params->get('direccion', 'desc');

        $data['items'] = $this->helper->getArticles([
            'limit' => $cantidad,
            'category_id' => $categoria,
            'order' => $orden,
            'direction' => $direccion
        ]);

        return $data;
    }
}
?>

// ===== src/Helper/ArticlesHelper.php
<?php
namespace Joomla\Module\LatestArticles\Helper;

use Joomla\Database\DatabaseInterface;
use Joomla\CMS\Application\CMSApplicationInterface;

class ArticlesHelper
{
    private $db;
    private $app;

    public function __construct(
        DatabaseInterface $db,
        CMSApplicationInterface $app
    ) {
        $this->db = $db;
        $this->app = $app;
    }

    public function getArticles(array $options = []): array
    {
        $limit = $options['limit'] ?? 5;
        $categoryId = $options['category_id'] ?? null;
        $order = $options['order'] ?? 'fecha';
        $direction = strtoupper($options['direction'] ?? 'DESC');

        $query = $this->db->getQuery(true)
            ->select([
                $this->db->quoteName('a.id'),
                $this->db->quoteName('a.title'),
                $this->db->quoteName('a.introtext'),
                $this->db->quoteName('a.fulltext'),
                $this->db->quoteName('a.publish_up'),
                $this->db->quoteName('a.hits'),
                $this->db->quoteName('a.created_by'),
                $this->db->quoteName('a.images'),
                $this->db->quoteName('a.state'),
                $this->db->quoteName('c.title', 'category'),
                $this->db->quoteName('c.slug', 'category_slug'),
                $this->db->quoteName('u.name', 'author')
            ])
            ->from($this->db->quoteName('#__articles', 'a'))
            ->innerJoin($this->db->quoteName('#__categories', 'c') .
                       ' ON ' . $this->db->quoteName('c.id') . ' = ' .
                       $this->db->quoteName('a.catid'))
            ->leftJoin($this->db->quoteName('#__users', 'u') .
                      ' ON ' . $this->db->quoteName('u.id') . ' = ' .
                      $this->db->quoteName('a.created_by'))
            ->where($this->db->quoteName('a.state') . ' = 1')
            ->where('NOW() >= ' . $this->db->quoteName('a.publish_up'))
            ->where('(NOW() <= ' . $this->db->quoteName('a.publish_down') .
                   ' OR ' . $this->db->quoteName('a.publish_down') . ' = ' .
                   $this->db->quote('0000-00-00 00:00:00') . ')');

        if ($categoryId) {
            $query->where($this->db->quoteName('a.catid') . ' = ' .
                         (int)$categoryId);
        }

        // Sort order
        $orderField = $this->getOrderField($order);
        $query->order($this->db->quoteName($orderField) . ' ' . $direction);

        $query->setLimit($limit);
        $this->db->setQuery($query);

        return $this->db->loadObjectList();
    }

    private function getOrderField($order): string
    {
        switch ($order) {
            case 'titulo':
                return 'a.title';
            case 'visitas':
                return 'a.hits';
            case 'fecha':
            default:
                return 'a.publish_up';
        }
    }
}
?>

// ===== tmpl/default.php
<?php
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Route;

$items = $displayData['items'];
$params = $displayData['params'];
$titulo = $params->get('titulo', 'Latest Articles');
$mostrar_fecha = (bool) $params->get('mostrar_fecha', 1);
$mostrar_autor = (bool) $params->get('mostrar_autor', 1);
$mostrar_resumen = (bool) $params->get('mostrar_resumen', 1);
$longitud_resumen = (int) $params->get('longitud_resumen', 100);
$mostrar_imagen = (bool) $params->get('mostrar_imagen', 1);
?>

<div class="mod-latest-articles">
    <?php if ($titulo): ?>
        <h3 class="mod-latest-articles-title">
            <?php echo htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8'); ?>
        </h3>
    <?php endif; ?>

    <?php if (!empty($items)): ?>
        <ul class="mod-latest-articles-list">
            <?php foreach ($items as $item): ?>
                <li class="mod-latest-articles-item">
                    <a href="<?php echo Route::_('index.php?option=com_content&view=article&id=' . $item->id); ?>">
                        <?php echo htmlspecialchars($item->title, ENT_QUOTES, 'UTF-8'); ?>
                    </a>

                    <?php if ($mostrar_fecha): ?>
                        <span class="mod-latest-articles-date">
                            <?php echo HTMLHelper::_('date', $item->publish_up, 'Y-m-d'); ?>
                        </span>
                    <?php endif; ?>

                    <?php if ($mostrar_autor): ?>
                        <span class="mod-latest-articles-author">
                            <?php echo htmlspecialchars($item->author, ENT_QUOTES, 'UTF-8'); ?>
                        </span>
                    <?php endif; ?>

                    <?php if ($mostrar_resumen && $item->introtext): ?>
                        <p class="mod-latest-articles-summary">
                            <?php
                            $resumen = strip_tags($item->introtext);
                            echo htmlspecialchars(
                                HTMLHelper::_('string.truncate', $resumen, $longitud_resumen),
                                ENT_QUOTES,
                                'UTF-8'
                            );
                            ?>
                        </p>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p class="mod-latest-articles-empty">
            <?php echo 'No articles available'; ?>
        </p>
    <?php endif; ?>
</div>

// ===== tmpl/default.xml
<?xml version="1.0" encoding="UTF-8"?>
<layout title="MOD_LATEST_ARTICLES_LAYOUT_DEFAULT">
    <state>
        <name>MOD_LATEST_ARTICLES_LAYOUT_DEFAULT</name>
        <description>MOD_LATEST_ARTICLES_LAYOUT_DEFAULT_DESC</description>
    </state>
</layout>

// ===== language/en-GB/mod_latest_articles.ini
MOD_LATEST_ARTICLES="Latest Articles Module"
MOD_LATEST_ARTICLES_DESC="Display latest articles with advanced options"
MOD_LATEST_ARTICLES_FIELDSET_BASIC="Basic Settings"
MOD_LATEST_ARTICLES_FIELDSET_DISPLAY="Display Options"
MOD_LATEST_ARTICLES_TITULO="Module Title"
MOD_LATEST_ARTICLES_TITULO_DESC="Title shown in the module"
MOD_LATEST_ARTICLES_CANTIDAD="Number of Articles"
MOD_LATEST_ARTICLES_CANTIDAD_DESC="How many articles to display"
MOD_LATEST_ARTICLES_CATEGORIA="Category"
MOD_LATEST_ARTICLES_CATEGORIA_DESC="Leave empty for all categories"
MOD_LATEST_ARTICLES_MOSTRAR_FECHA="Show Date"
MOD_LATEST_ARTICLES_MOSTRAR_AUTOR="Show Author"
MOD_LATEST_ARTICLES_MOSTRAR_RESUMEN="Show Summary"
MOD_LATEST_ARTICLES_LONGITUD_RESUMEN="Summary Length"
MOD_LATEST_ARTICLES_ORDEN="Sort By"
MOD_LATEST_ARTICLES_ORDEN_FECHA="Publication Date"
MOD_LATEST_ARTICLES_ORDEN_TITULO="Title"
MOD_LATEST_ARTICLES_ORDEN_VISITAS="Views"
MOD_LATEST_ARTICLES_DIRECCION="Sort Direction"
MOD_LATEST_ARTICLES_DIRECCION_ASC="Ascending"
MOD_LATEST_ARTICLES_DIRECCION_DESC="Descending"
MOD_LATEST_ARTICLES_MOSTRAR_IMAGEN="Show Featured Image"
MOD_LATEST_ARTICLES_TAMANIO_IMAGEN="Image Size"
MOD_LATEST_ARTICLES_TAMANIO_IMAGEN_DESC="Format: 100x100"
MOD_LATEST_ARTICLES_LAYOUT_DEFAULT="Default Layout"
MOD_LATEST_ARTICLES_LAYOUT_DEFAULT_DESC="Simple list of latest articles"

// ===== language/en-GB/mod_latest_articles.sys.ini
MOD_LATEST_ARTICLES="Latest Articles Module"
MOD_LATEST_ARTICLES_DESC="Display latest articles with advanced options"

// ===== language/es-ES/mod_latest_articles.ini
MOD_LATEST_ARTICLES="Modulo Ultimos Articulos"
MOD_LATEST_ARTICLES_DESC="Muestra los ultimos articulos con opciones avanzadas"
MOD_LATEST_ARTICLES_FIELDSET_BASIC="Configuracion Basica"
MOD_LATEST_ARTICLES_FIELDSET_DISPLAY="Opciones de Visualizacion"
MOD_LATEST_ARTICLES_TITULO="Titulo del Modulo"
MOD_LATEST_ARTICLES_TITULO_DESC="Titulo mostrado en el modulo"
MOD_LATEST_ARTICLES_CANTIDAD="Cantidad de Articulos"
MOD_LATEST_ARTICLES_CANTIDAD_DESC="Cuantos articulos mostrar"
MOD_LATEST_ARTICLES_CATEGORIA="Categoria"
MOD_LATEST_ARTICLES_CATEGORIA_DESC="Dejar vacio para todas las categorias"
MOD_LATEST_ARTICLES_MOSTRAR_FECHA="Mostrar Fecha"
MOD_LATEST_ARTICLES_MOSTRAR_AUTOR="Mostrar Autor"
MOD_LATEST_ARTICLES_MOSTRAR_RESUMEN="Mostrar Resumen"
MOD_LATEST_ARTICLES_LONGITUD_RESUMEN="Longitud del Resumen"
MOD_LATEST_ARTICLES_ORDEN="Ordenar Por"
MOD_LATEST_ARTICLES_ORDEN_FECHA="Fecha de Publicacion"
MOD_LATEST_ARTICLES_ORDEN_TITULO="Titulo"
MOD_LATEST_ARTICLES_ORDEN_VISITAS="Visitas"
MOD_LATEST_ARTICLES_DIRECCION="Direccion de Orden"
MOD_LATEST_ARTICLES_DIRECCION_ASC="Ascendente"
MOD_LATEST_ARTICLES_DIRECCION_DESC="Descendente"
MOD_LATEST_ARTICLES_MOSTRAR_IMAGEN="Mostrar Imagen Destacada"
MOD_LATEST_ARTICLES_TAMANIO_IMAGEN="Tamanio de Imagen"
MOD_LATEST_ARTICLES_TAMANIO_IMAGEN_DESC="Formato: 100x100"
MOD_LATEST_ARTICLES_LAYOUT_DEFAULT="Layout por Defecto"
MOD_LATEST_ARTICLES_LAYOUT_DEFAULT_DESC="Lista simple de ultimos articulos"

// ===== language/es-ES/mod_latest_articles.sys.ini
MOD_LATEST_ARTICLES="Modulo Ultimos Articulos"
MOD_LATEST_ARTICLES_DESC="Muestra los ultimos articulos con opciones avanzadas"
