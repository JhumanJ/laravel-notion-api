<?php

namespace FiveamCode\LaravelNotionApi\Endpoints;

use FiveamCode\LaravelNotionApi\Entities\Collections\PageCollection;
use FiveamCode\LaravelNotionApi\Notion;
use FiveamCode\LaravelNotionApi\Query\Filters\Filter;
use FiveamCode\LaravelNotionApi\Query\Sorting;
use Illuminate\Support\Collection;

/**
 * Class DataSource
 * @package FiveamCode\LaravelNotionApi\Endpoints
 *
 * Builder for querying a specific data source with filters, sorts, and pagination.
 * For API 2025-09-03 and later.
 */
class DataSource extends Endpoint
{
    /**
     * @var string
     */
    private string $dataSourceId;

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
     * DataSource constructor.
     * @param string $dataSourceId
     * @param Notion $notion
     * @throws \FiveamCode\LaravelNotionApi\Exceptions\HandlingException
     * @throws \FiveamCode\LaravelNotionApi\Exceptions\LaravelNotionAPIException
     */
    public function __construct(string $dataSourceId, Notion $notion)
    {
        $this->dataSourceId = $dataSourceId;

        $this->sorts = new Collection();
        $this->filter = new Collection();
        $this->rawFilter = null;
        $this->rawSort = null;

        parent::__construct($notion);
    }

    /**
     * Query the data source with filters, sorts, and pagination
     *
     * @return PageCollection
     * @throws \FiveamCode\LaravelNotionApi\Exceptions\HandlingException
     * @throws \FiveamCode\LaravelNotionApi\Exceptions\NotionException
     */
    public function query(): PageCollection
    {
        $postData = [];

        if ($this->sorts->isNotEmpty()) {
            $postData['sorts'] = Sorting::sortQuery($this->sorts);
        } elseif ($this->rawSort) {
            $postData['sorts'] = $this->rawSort;
        }

        if ($this->filter->isNotEmpty()) {
            $postData['filter'][$this->filterAggregate] = Filter::filterQuery($this->filter);
        } elseif ($this->rawFilter) {
            $postData['filter'] = $this->rawFilter;
        }

        if ($this->startCursor !== null) {
            $postData['start_cursor'] = (string) $this->startCursor;
        }

        if ($this->pageSize !== null) {
            $postData['page_size'] = $this->pageSize;
        }

        $response = $this
            ->post(
                $this->url(Endpoint::DATA_SOURCES . "/{$this->dataSourceId}/query"),
                $postData
            )
            ->json();

        return new PageCollection($response);
    }

    /**
     * Filter the data source query
     *
     * @param Collection $filter
     * @param string $filterAggregate
     * @return $this
     */
    public function filterBy(Collection $filter, $filterAggregate = 'or'): DataSource
    {
        $this->filter = $filter;
        $this->filterAggregate = $filterAggregate;
        return $this;
    }

    /**
     * Filter the data source query with raw filter array
     *
     * @param array $filter
     * @return $this
     */
    public function filterByRaw(array $filter): DataSource
    {
        $this->rawFilter = $filter;
        return $this;
    }

    /**
     * Sort the data source query
     *
     * @param Collection $sorts
     * @return $this
     */
    public function sortBy(Collection $sorts): DataSource
    {
        $this->sorts = $sorts;
        return $this;
    }

    /**
     * Sort the data source query with raw sort array
     *
     * @param array $sorts
     * @return $this
     */
    public function sortByRaw(array $sorts): DataSource
    {
        $this->rawSort = $sorts;
        return $this;
    }
}
