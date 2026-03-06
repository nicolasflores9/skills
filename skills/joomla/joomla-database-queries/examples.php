<?php
/**
 * Ejemplos Funcionales Completos
 * Joomla 5/6 - Consultas a Base de Datos
 *
 * Archivo con ejemplos de código listo para usar
 * Copia y pega directo en tu proyecto
 */

use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\ParameterType;

// ============================================================================
// EJEMPLO 1: Obtener la Instancia de Base de Datos
// ============================================================================

class MyModel
{
    // En modelos - usar getDatabase()
    public function getArticles()
    {
        $db = $this->getDatabase(); // En un modelo de Joomla
        // ... rest of code
    }
}

// En otros contextos - usar Container
function getDatabase()
{
    return Factory::getContainer()->get(DatabaseInterface::class);
}

// ============================================================================
// EJEMPLO 2: SELECT Simple - Artículos Publicados
// ============================================================================

function getPublishedArticles()
{
    $db = Factory::getContainer()->get(DatabaseInterface::class);

    $query = $db->getQuery(true)
        ->select($db->quoteName(['id', 'title', 'introtext', 'created']))
        ->from($db->quoteName('#__content'))
        ->where($db->quoteName('state') . ' = :state')
        ->bind(':state', 1, ParameterType::INTEGER)
        ->order($db->quoteName('created') . ' DESC');

    $db->setQuery($query);
    return $db->loadObjectList();
}

// ============================================================================
// EJEMPLO 3: SELECT con WHERE Múltiple
// ============================================================================

function getArticlesByCategory($categoryId, $state = 1)
{
    $db = Factory::getContainer()->get(DatabaseInterface::class);

    $query = $db->getQuery(true)
        ->select('*')
        ->from($db->quoteName('#__content'))
        ->where($db->quoteName('state') . ' = :state')
        ->where($db->quoteName('catid') . ' = :catid')
        ->bind(':state', $state, ParameterType::INTEGER)
        ->bind(':catid', $categoryId, ParameterType::INTEGER)
        ->order($db->quoteName('created') . ' DESC');

    $db->setQuery($query);
    return $db->loadObjectList();
}

// ============================================================================
// EJEMPLO 4: JOIN - Content + Categories + Users
// ============================================================================

function getArticlesWithMetadata()
{
    $db = Factory::getContainer()->get(DatabaseInterface::class);

    $query = $db->getQuery(true)
        ->select([
            'c.id',
            'c.title',
            'c.introtext',
            'c.created',
            'cat.title AS category_title',
            'u.name AS author_name'
        ])
        ->from($db->quoteName('#__content', 'c'))
        ->leftJoin(
            $db->quoteName('#__categories', 'cat') . ' ON ' .
            $db->quoteName('c.catid') . ' = ' . $db->quoteName('cat.id')
        )
        ->innerJoin(
            $db->quoteName('#__users', 'u') . ' ON ' .
            $db->quoteName('c.created_by') . ' = ' . $db->quoteName('u.id')
        )
        ->where($db->quoteName('c.state') . ' = :state')
        ->bind(':state', 1, ParameterType::INTEGER)
        ->order($db->quoteName('c.created') . ' DESC');

    $db->setQuery($query);
    return $db->loadObjectList();
}

// ============================================================================
// EJEMPLO 5: Búsqueda Avanzada con LIKE
// ============================================================================

function searchArticles($searchTerm, $categoryId = null)
{
    $db = Factory::getContainer()->get(DatabaseInterface::class);

    $query = $db->getQuery(true)
        ->select(['id', 'title', 'introtext', 'created'])
        ->from($db->quoteName('#__content'))
        ->where(
            $db->quoteName('title') . ' LIKE :search OR ' .
            $db->quoteName('introtext') . ' LIKE :search'
        )
        ->where($db->quoteName('state') . ' = :state')
        ->bind(':search', '%' . $searchTerm . '%', ParameterType::STRING)
        ->bind(':state', 1, ParameterType::INTEGER);

    if ($categoryId) {
        $query->where($db->quoteName('catid') . ' = :catid')
              ->bind(':catid', $categoryId, ParameterType::INTEGER);
    }

    $query->order($db->quoteName('created') . ' DESC');

    $db->setQuery($query);
    return $db->loadObjectList();
}

// ============================================================================
// EJEMPLO 6: Paginación Completa
// ============================================================================

function getPaginatedArticles($page = 1, $limit = 10)
{
    $db = Factory::getContainer()->get(DatabaseInterface::class);
    $offset = ($page - 1) * $limit;

    // Query para obtener artículos
    $query = $db->getQuery(true)
        ->select('*')
        ->from($db->quoteName('#__content'))
        ->where($db->quoteName('state') . ' = :state')
        ->bind(':state', 1, ParameterType::INTEGER)
        ->order($db->quoteName('created') . ' DESC')
        ->setLimit($limit, $offset);

    $db->setQuery($query);
    $articles = $db->loadObjectList();

    // Query para obtener total
    $countQuery = $db->getQuery(true)
        ->select('COUNT(*)')
        ->from($db->quoteName('#__content'))
        ->where($db->quoteName('state') . ' = :state')
        ->bind(':state', 1, ParameterType::INTEGER);

    $db->setQuery($countQuery);
    $total = $db->loadResult();

    return [
        'articles' => $articles,
        'total' => $total,
        'page' => $page,
        'limit' => $limit,
        'pages' => ceil($total / $limit)
    ];
}

// ============================================================================
// EJEMPLO 7: JOIN con Campos Personalizados
// ============================================================================

function getArticleWithCustomFields($articleId, $fieldId = 5)
{
    $db = Factory::getContainer()->get(DatabaseInterface::class);

    $query = $db->getQuery(true)
        ->select([
            'c.id',
            'c.title',
            'c.introtext',
            'c.fulltext',
            'fv.value AS custom_field_value'
        ])
        ->from($db->quoteName('#__content', 'c'))
        ->leftJoin(
            $db->quoteName('#__fields_values', 'fv') . ' ON ' .
            $db->quoteName('c.id') . ' = ' . $db->quoteName('fv.item_id') . ' AND ' .
            $db->quoteName('fv.field_id') . ' = :field_id'
        )
        ->where($db->quoteName('c.id') . ' = :id')
        ->bind(':id', $articleId, ParameterType::INTEGER)
        ->bind(':field_id', $fieldId, ParameterType::INTEGER);

    $db->setQuery($query);
    return $db->loadObject();
}

// ============================================================================
// EJEMPLO 8: INSERT - Crear Nuevo Artículo
// ============================================================================

function createArticle($title, $introtext, $categoryId, $userId)
{
    $db = Factory::getContainer()->get(DatabaseInterface::class);

    $query = $db->getQuery(true)
        ->insert($db->quoteName('#__content'))
        ->columns([
            $db->quoteName('title'),
            $db->quoteName('introtext'),
            $db->quoteName('state'),
            $db->quoteName('catid'),
            $db->quoteName('created'),
            $db->quoteName('created_by'),
            $db->quoteName('access')
        ])
        ->values(':title, :introtext, :state, :catid, :created, :created_by, :access')
        ->bind(':title', $title, ParameterType::STRING)
        ->bind(':introtext', $introtext, ParameterType::STRING)
        ->bind(':state', 1, ParameterType::INTEGER)
        ->bind(':catid', $categoryId, ParameterType::INTEGER)
        ->bind(':created', date('Y-m-d H:i:s'), ParameterType::STRING)
        ->bind(':created_by', $userId, ParameterType::INTEGER)
        ->bind(':access', 1, ParameterType::INTEGER);

    $db->setQuery($query);

    try {
        $db->execute();
        return $db->insertid();
    } catch (\Exception $e) {
        error_log('Error creating article: ' . $e->getMessage());
        return false;
    }
}

// ============================================================================
// EJEMPLO 9: UPDATE - Actualizar Artículo
// ============================================================================

function updateArticle($articleId, $title, $introtext, $state)
{
    $db = Factory::getContainer()->get(DatabaseInterface::class);

    $query = $db->getQuery(true)
        ->update($db->quoteName('#__content'))
        ->set([
            $db->quoteName('title') . ' = :title',
            $db->quoteName('introtext') . ' = :introtext',
            $db->quoteName('state') . ' = :state',
            $db->quoteName('modified') . ' = :modified'
        ])
        ->where($db->quoteName('id') . ' = :id')
        ->bind(':title', $title, ParameterType::STRING)
        ->bind(':introtext', $introtext, ParameterType::STRING)
        ->bind(':state', $state, ParameterType::INTEGER)
        ->bind(':modified', date('Y-m-d H:i:s'), ParameterType::STRING)
        ->bind(':id', $articleId, ParameterType::INTEGER);

    $db->setQuery($query);

    try {
        return $db->execute();
    } catch (\Exception $e) {
        error_log('Error updating article: ' . $e->getMessage());
        return false;
    }
}

// ============================================================================
// EJEMPLO 10: DELETE - Eliminar Artículo
// ============================================================================

function deleteArticle($articleId)
{
    $db = Factory::getContainer()->get(DatabaseInterface::class);

    // Primero eliminar campos personalizados
    $query1 = $db->getQuery(true)
        ->delete($db->quoteName('#__fields_values'))
        ->where($db->quoteName('item_id') . ' = :item_id')
        ->bind(':item_id', $articleId, ParameterType::INTEGER);

    $db->setQuery($query1);

    try {
        $db->execute();
    } catch (\Exception $e) {
        error_log('Error deleting custom fields: ' . $e->getMessage());
    }

    // Luego eliminar el artículo
    $query2 = $db->getQuery(true)
        ->delete($db->quoteName('#__content'))
        ->where($db->quoteName('id') . ' = :id')
        ->bind(':id', $articleId, ParameterType::INTEGER);

    $db->setQuery($query2);

    try {
        return $db->execute();
    } catch (\Exception $e) {
        error_log('Error deleting article: ' . $e->getMessage());
        return false;
    }
}

// ============================================================================
// EJEMPLO 11: Filtrado por Rango de Fechas
// ============================================================================

function getArticlesByDateRange($startDate, $endDate)
{
    $db = Factory::getContainer()->get(DatabaseInterface::class);

    $query = $db->getQuery(true)
        ->select('*')
        ->from($db->quoteName('#__content'))
        ->where(
            $db->quoteName('created') . ' >= :start_date AND ' .
            $db->quoteName('created') . ' <= :end_date'
        )
        ->where($db->quoteName('state') . ' = :state')
        ->bind(':start_date', $startDate . ' 00:00:00', ParameterType::STRING)
        ->bind(':end_date', $endDate . ' 23:59:59', ParameterType::STRING)
        ->bind(':state', 1, ParameterType::INTEGER)
        ->order($db->quoteName('created') . ' DESC');

    $db->setQuery($query);
    return $db->loadObjectList();
}

// ============================================================================
// EJEMPLO 12: Búsqueda en Array IN
// ============================================================================

function getArticlesByIds($ids)
{
    $db = Factory::getContainer()->get(DatabaseInterface::class);

    $query = $db->getQuery(true)
        ->select(['id', 'title', 'state'])
        ->from($db->quoteName('#__content'));

    // Usar bindArray para valores IN
    $placeholders = $query->bindArray($ids);
    $query->where($db->quoteName('id') . ' IN (' . implode(',', $placeholders) . ')');

    $db->setQuery($query);
    return $db->loadObjectList();
}

// ============================================================================
// EJEMPLO 13: Conteo y Estadísticas
// ============================================================================

function getArticleStats()
{
    $db = Factory::getContainer()->get(DatabaseInterface::class);

    $query = $db->getQuery(true)
        ->select([
            'cat.title AS category',
            'COUNT(*) AS article_count',
            'MAX(c.created) AS last_created'
        ])
        ->from($db->quoteName('#__content', 'c'))
        ->leftJoin(
            $db->quoteName('#__categories', 'cat') . ' ON ' .
            $db->quoteName('c.catid') . ' = ' . $db->quoteName('cat.id')
        )
        ->where($db->quoteName('c.state') . ' = :state')
        ->bind(':state', 1, ParameterType::INTEGER)
        ->group($db->quoteName('cat.id'))
        ->group($db->quoteName('cat.title'))
        ->order('article_count DESC');

    $db->setQuery($query);
    return $db->loadObjectList();
}

// ============================================================================
// EJEMPLO 14: INSERT Múltiple
// ============================================================================

function createMultipleArticles($articles)
{
    $db = Factory::getContainer()->get(DatabaseInterface::class);

    $query = $db->getQuery(true)
        ->insert($db->quoteName('#__content'))
        ->columns(['title', 'introtext', 'state', 'catid', 'created', 'created_by']);

    foreach ($articles as $i => $article) {
        $query->values(
            ':title' . $i . ', :introtext' . $i . ', :state' . $i . ', ' .
            ':catid' . $i . ', :created' . $i . ', :created_by' . $i
        );
        $query->bind(':title' . $i, $article['title'], ParameterType::STRING);
        $query->bind(':introtext' . $i, $article['introtext'], ParameterType::STRING);
        $query->bind(':state' . $i, 1, ParameterType::INTEGER);
        $query->bind(':catid' . $i, $article['catid'], ParameterType::INTEGER);
        $query->bind(':created' . $i, date('Y-m-d H:i:s'), ParameterType::STRING);
        $query->bind(':created_by' . $i, $article['created_by'], ParameterType::INTEGER);
    }

    $db->setQuery($query);

    try {
        return $db->execute();
    } catch (\Exception $e) {
        error_log('Error creating articles: ' . $e->getMessage());
        return false;
    }
}

// ============================================================================
// EJEMPLO 15: Usar insertObject() para Mayor Simplicidad
// ============================================================================

function createArticleSimple($data)
{
    $db = Factory::getContainer()->get(DatabaseInterface::class);

    $article = new \stdClass();
    $article->title = $data['title'];
    $article->introtext = $data['introtext'];
    $article->fulltext = $data['fulltext'] ?? '';
    $article->state = 1;
    $article->catid = $data['category_id'];
    $article->created = date('Y-m-d H:i:s');
    $article->created_by = Factory::getUser()->id;
    $article->access = 1;

    try {
        $db->insertObject('#__content', $article, 'id');
        return $article->id;
    } catch (\Exception $e) {
        error_log('Error: ' . $e->getMessage());
        return false;
    }
}

// ============================================================================
// NOTAS DE SEGURIDAD
// ============================================================================

/**
 * IMPORTANTE - REGLAS DE ORO:
 *
 * 1. SIEMPRE usa quoteName() para identificadores:
 *    - $db->quoteName('title')
 *    - $db->quoteName('#__content')
 *
 * 2. SIEMPRE usa bind() para valores:
 *    ->bind(':param', $value, ParameterType::TYPE)
 *
 * 3. NUNCA concatenes valores directamente:
 *    - MAL: ->where("title = '$title'")
 *    - BIEN: ->where($db->quoteName('title') . ' = :title')
 *           ->bind(':title', $title, ParameterType::STRING)
 *
 * 4. Especifica ParameterType:
 *    - STRING, INTEGER, FLOAT, BOOLEAN, NULL
 *
 * 5. Valida entrada antes de usar:
 *    $id = (int) $_GET['id'];
 *    $search = htmlspecialchars($search);
 *
 * 6. Maneja errores con try-catch
 */

?>
