<?php

namespace FiveamCode\LaravelNotionApi\Endpoints;

/**
 * Class Database
 * @package FiveamCode\LaravelNotionApi\Endpoints
 * @deprecated As of API version 2025-09-03, database queries are no longer supported.
 *             Use DataSource endpoint instead: Notion::dataSource($dataSourceId)->query()
 *             
 * This class is kept for backwards compatibility but provides no functionality.
 * All database operations are now managed through the Databases and DataSources endpoints.
 */
class Database extends Endpoint
{
    /**
     * @deprecated Use Notion::dataSource($dataSourceId)->query() instead
     */
    public function __construct(string $databaseId, \FiveamCode\LaravelNotionApi\Notion $notion)
    {
        parent::__construct($notion);
        trigger_error(
            'Database endpoint is deprecated as of API 2025-09-03. Use DataSource endpoint instead.',
            E_USER_DEPRECATED
        );
    }
}
