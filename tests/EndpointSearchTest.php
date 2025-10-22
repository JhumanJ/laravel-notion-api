<?php

namespace FiveamCode\LaravelNotionApi\Tests;

use Notion;
use Illuminate\Support\Facades\Http;
use FiveamCode\LaravelNotionApi\Entities\Page;
use FiveamCode\LaravelNotionApi\Entities\DataSource;
use FiveamCode\LaravelNotionApi\Exceptions\NotionException;
use FiveamCode\LaravelNotionApi\Entities\Collections\EntityCollection;

/**
 * Class EndpointSearchTest
 *
 * The fake API responses are based on Notions documentation.
 * @see https://developers.notion.com/reference/post-search
 *
 * @package FiveamCode\LaravelNotionApi\Tests
 */
class EndpointSearchTest extends NotionApiTest
{

    /** @test */
    public function it_throws_a_notion_exception_bad_request()
    {
        // failing /v1/search
        Http::fake([
            'https://api.notion.com/v1/search'
            => Http::response(
                json_decode('{}', true),
                400,
                ['Headers']
            )
        ]);

        $this->expectException(\Illuminate\Http\Client\RequestException::class);
        $this->expectExceptionMessage('HTTP request returned status code 400');

        Notion::search()->query();
    }

    /** @test */
    public function it_returns_all_pages_and_data_sources_of_the_workspace_as_collection_with_entity_objects()
    {
        // successful /v1/search
        Http::fake([
            'https://api.notion.com/v1/search'
            => Http::response(
                json_decode(file_get_contents('tests/stubs/endpoints/search/response_all_200.json'), true),
                200,
                ['Headers']
            )
        ]);

        $searchResult = Notion::search()->query();
        $entityCollection = $searchResult->asCollection();
        $this->assertInstanceOf(EntityCollection::class, $searchResult);
        $this->assertIsIterable($entityCollection);
        $this->assertCount(2, $entityCollection);

        $dataSource = $entityCollection[0];
        $page = $entityCollection[1];

        $this->assertInstanceOf(DataSource::class, $dataSource);
        $this->assertInstanceOf(Page::class, $page);
    }

    /** @test */
    public function it_returns_only_pages_of_the_workspace_as_collection_with_entity_objects()
    {
        // successful /v1/search
        Http::fake([
            'https://api.notion.com/v1/search'
            => Http::response(
                json_decode(file_get_contents('tests/stubs/endpoints/search/response_pages_200.json'), true),
                200,
                ['Headers']
            )
        ]);

        $searchResult = Notion::search()->onlyPages()->query();
        $entityCollection = $searchResult->asCollection();
        $this->assertInstanceOf(EntityCollection::class, $searchResult);
        $this->assertIsIterable($entityCollection);
        $this->assertCount(1, $entityCollection);

        $page = $entityCollection->first();

        $this->assertInstanceOf(Page::class, $page);
    }


    /** @test */
    public function it_returns_only_data_sources_of_the_workspace_as_collection_with_entity_objects()
    {
        // successful /v1/search
        Http::fake([
            'https://api.notion.com/v1/search'
            => Http::response(
                json_decode(file_get_contents('tests/stubs/endpoints/search/response_databases_200.json'), true),
                200,
                ['Headers']
            )
        ]);

        $searchResult = Notion::search()->onlyDataSources()->query();
        $entityCollection = $searchResult->asCollection();
        $this->assertInstanceOf(EntityCollection::class, $searchResult);
        $this->assertIsIterable($entityCollection);
        $this->assertCount(1, $entityCollection);

        $dataSource = $entityCollection->first();

        $this->assertInstanceOf(DataSource::class, $dataSource);
    }
}
