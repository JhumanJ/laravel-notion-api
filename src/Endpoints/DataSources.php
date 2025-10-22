<?php

namespace FiveamCode\LaravelNotionApi\Endpoints;

use FiveamCode\LaravelNotionApi\Entities\Collections\PageCollection;
use FiveamCode\LaravelNotionApi\Entities\DataSource;
use FiveamCode\LaravelNotionApi\Entities\Entity;
use FiveamCode\LaravelNotionApi\Exceptions\HandlingException;
use FiveamCode\LaravelNotionApi\Exceptions\NotionException;

class DataSources extends Endpoint implements EndpointInterface
{
    /**
     * Retrieve a data source
     * url: https://api.notion.com/{version}/data_sources/{data_source_id}
     *
     * @param string $id
     * @return Entity
     * @throws HandlingException
     * @throws NotionException
     */
    public function find(string $id): Entity
    {
        $result = $this->getJson($this->url(self::DATA_SOURCES . "/{$id}"));
        return new DataSource($result);
    }

    /**
     * Query a data source
     * url: https://api.notion.com/{version}/data_sources/{data_source_id}/query
     * notion-api-docs: see upgrade guide for data source queries
     *
     * @param string $dataSourceId
     * @param array $body
     * @return PageCollection
     * @throws HandlingException
     * @throws NotionException
     */
    public function query(string $dataSourceId, array $body = []): PageCollection
    {
        $response = $this->post(
            $this->url(self::DATA_SOURCES . "/{$dataSourceId}/query"),
            $body
        )->json();

        return new PageCollection($response);
    }
}


