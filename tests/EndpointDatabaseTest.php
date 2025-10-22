<?php

namespace FiveamCode\LaravelNotionApi\Tests;

use FiveamCode\LaravelNotionApi\Query\Filters\Filter;
use Notion;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use FiveamCode\LaravelNotionApi\Entities\Page;
use FiveamCode\LaravelNotionApi\Query\Sorting;
use FiveamCode\LaravelNotionApi\Endpoints\Database;
use FiveamCode\LaravelNotionApi\Exceptions\NotionException;
use FiveamCode\LaravelNotionApi\Entities\Collections\PageCollection;

/**
 * Class EndpointDatabaseTest
 *
     * Database queries moved to data sources in Notion 2025-09-03.
 *
 * @package FiveamCode\LaravelNotionApi\Tests
 */
class EndpointDatabaseTest extends NotionApiTest
{

    /** @test */
    public function it_returns_a_database_endpoint_instance()
    {
        $endpoint = Notion::database('897e5a76ae524b489fdfe71f5945d1af');

        $this->assertInstanceOf(Database::class, $endpoint);
    }

    /**
     * @dataProvider limitProvider
     */
    public function limitProvider(): array
    {
        return [
            [1],
            [2]
        ];
    }

    // Database query tests removed. Use DataSources endpoint tests instead.
}
