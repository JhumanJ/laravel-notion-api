<?php

namespace FiveamCode\LaravelNotionApi\Endpoints;

use FiveamCode\LaravelNotionApi\Entities\Collections\DatabaseCollection;
use FiveamCode\LaravelNotionApi\Entities\Database;
use FiveamCode\LaravelNotionApi\Exceptions\HandlingException;
use FiveamCode\LaravelNotionApi\Exceptions\NotionException;


/**
 * Class Databases
 *
 * This endpoint is not recommended by Notion anymore.
 * Use the search() endpoint instead.
 *
 * @package FiveamCode\LaravelNotionApi\Endpoints
 */
class Databases extends Endpoint implements EndpointInterface
{
    // The List databases endpoint is deprecated and removed in this package for 2025-09-03

    /**
     * Retrieve a database
     * url: https://api.notion.com/{version}/databases/{database_id}
     * notion-api-docs: https://developers.notion.com/reference/retrieve-a-database 
     *
     * @param string $databaseId
     * @return Database
     * @throws HandlingException
     * @throws NotionException
     */
    public function find(string $databaseId): Database
    {
        $result = $this
            ->getJson($this->url(Endpoint::DATABASES . "/{$databaseId}"));

        return new Database($result);
    }

    /**
     * Create a database
     * url: https://api.notion.com/{version}/databases
     * notion-api-docs: https://developers.notion.com/reference/create-a-database
     *
     * @param array $body
     * @return Database
     * @throws HandlingException
     * @throws NotionException
     */
    public function create(array $body): Database
    {
        $result = $this->post($this->url(Endpoint::DATABASES), $body)->json();
        return new Database($result);
    }

    /**
     * Update a database
     * url: https://api.notion.com/{version}/databases/{database_id}
     * notion-api-docs: https://developers.notion.com/reference/update-a-database
     *
     * @param string $databaseId
     * @param array $body
     * @return Database
     * @throws HandlingException
     * @throws NotionException
     */
    public function update(string $databaseId, array $body): Database
    {
        $result = $this->patch($this->url(Endpoint::DATABASES . "/{$databaseId}"), $body)->json();
        return new Database($result);
    }
}
