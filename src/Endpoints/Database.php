<?php

namespace FiveamCode\LaravelNotionApi\Endpoints;

use FiveamCode\LaravelNotionApi\Entities\Collections\PageCollection;
use FiveamCode\LaravelNotionApi\Notion;
use FiveamCode\LaravelNotionApi\Query\Filters\Filter;
use FiveamCode\LaravelNotionApi\Query\Sorting;
use Illuminate\Support\Collection;

/**
 * Class Database
 * @package FiveamCode\LaravelNotionApi\Endpoints
 */
class Database extends Endpoint
{
    /**
     * @var string
     */
    private string $databaseId;

    /**
     * @var Collection
     */
    private Collection $filter;

    private string $filterAggregate = 'or';

    private ?array $rawFilter;

    private ?array $rawSort;

    /**
     * @var Collection
     */
    private Collection $sorts;

    /**
     * Database constructor.
     * @param string $databaseId
     * @param Notion $notion
     * @throws \FiveamCode\LaravelNotionApi\Exceptions\HandlingException
     * @throws \FiveamCode\LaravelNotionApi\Exceptions\LaravelNotionAPIException
     */
    public function __construct(string $databaseId, Notion $notion)
    {
        $this->databaseId = $databaseId;

        $this->sorts = new Collection();
        $this->filter = new Collection();
        $this->rawFilter = null;
        $this->rawSort = null;

        parent::__construct($notion);
    }

    /**
     * @return PageCollection
     * @throws \FiveamCode\LaravelNotionApi\Exceptions\HandlingException
     * @throws \FiveamCode\LaravelNotionApi\Exceptions\NotionException
     */
    // Database::query is no longer supported in 2025-09-03. Use DataSources::query with a data_source_id instead.

    /**
     * @param Collection $filter
     * @return $this
     */
    public function filterBy(Collection $filter, $filterAggregate = 'or'): Database
    {
        $this->filter = $filter;
        $this->filterAggregate = $filterAggregate;
        return $this;
    }

    public function filterByRaw(array $filter)
    {
        $this->rawFilter = $filter;
        return $this;
    }

    /**
     * @param Collection $sorts
     * @return $this
     */
    public function sortBy(Collection $sorts): Database
    {
        $this->sorts = $sorts;
        return $this;
    }

    public function sortByRaw(array $sorts)
    {
        $this->rawSort = $sorts;
        return $this;
    }
}
