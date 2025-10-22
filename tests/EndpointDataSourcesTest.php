<?php

namespace FiveamCode\LaravelNotionApi\Tests;

use Notion;
use Illuminate\Support\Facades\Http;
use FiveamCode\LaravelNotionApi\Entities\DataSource;
use FiveamCode\LaravelNotionApi\Entities\Collections\PageCollection;

/**
 * Class EndpointDataSourcesTest
 *
 * Tests for the new data sources endpoints (Notion API 2025-09-03).
 * @see https://developers.notion.com/docs/upgrade-guide-2025-09-03
 *
 * @package FiveamCode\LaravelNotionApi\Tests
 */
class EndpointDataSourcesTest extends NotionApiTest
{
    /** @test */
    public function it_retrieves_a_data_source()
    {
        Http::fake([
            'https://api.notion.com/v1/data_sources/a42a62ed-9b51-4b98-9dea-ea6d091bc508'
            => Http::response(
                json_decode(file_get_contents('tests/stubs/endpoints/data_sources/response_specific_200.json'), true),
                200,
                ['Headers']
            )
        ]);

        $dataSource = Notion::dataSources()->find('a42a62ed-9b51-4b98-9dea-ea6d091bc508');

        $this->assertInstanceOf(DataSource::class, $dataSource);
        $this->assertSame('My Task Tracker', $dataSource->getName());
        $this->assertSame('data_source', $dataSource->getObjectType());
    }

    /** @test */
    public function it_retrieves_data_source_parent_information()
    {
        Http::fake([
            'https://api.notion.com/v1/data_sources/a42a62ed-9b51-4b98-9dea-ea6d091bc508'
            => Http::response(
                json_decode(file_get_contents('tests/stubs/endpoints/data_sources/response_specific_200.json'), true),
                200,
                ['Headers']
            )
        ]);

        $dataSource = Notion::dataSources()->find('a42a62ed-9b51-4b98-9dea-ea6d091bc508');

        // Test parent (database) information
        $parent = $dataSource->getParent();
        $this->assertIsArray($parent);
        $this->assertSame('database_id', $parent['type']);
        $this->assertSame('2945f481-4007-80b4-88ae-e038000c8ee3', $parent['database_id']);
        $this->assertSame('2945f481-4007-80b4-88ae-e038000c8ee3', $dataSource->getParentDatabaseId());

        // Test database_parent (page) information
        $databaseParent = $dataSource->getDatabaseParent();
        $this->assertIsArray($databaseParent);
        $this->assertSame('page_id', $databaseParent['type']);
        $this->assertSame('2945f481-4007-80da-85cf-fe2b331d476a', $databaseParent['page_id']);
        $this->assertSame('2945f481-4007-80da-85cf-fe2b331d476a', $dataSource->getDatabaseParentPageId());
    }

    /** @test */
    public function it_retrieves_data_source_icon_description_and_metadata()
    {
        Http::fake([
            'https://api.notion.com/v1/data_sources/a42a62ed-9b51-4b98-9dea-ea6d091bc508'
            => Http::response(
                json_decode(file_get_contents('tests/stubs/endpoints/data_sources/response_specific_200.json'), true),
                200,
                ['Headers']
            )
        ]);

        $dataSource = Notion::dataSources()->find('a42a62ed-9b51-4b98-9dea-ea6d091bc508');

        // Test icon
        $this->assertSame('emoji', $dataSource->getIconType());
        $this->assertSame('✅', $dataSource->getIcon());

        // Test description
        $this->assertSame('A data source to track tasks', $dataSource->getDescription());
        $this->assertIsArray($dataSource->getRawDescription());
        $this->assertCount(1, $dataSource->getRawDescription());

        // Test status fields
        $this->assertFalse($dataSource->isArchived());
        $this->assertFalse($dataSource->isInTrash());
    }

    /** @test */
    public function it_queries_a_data_source_and_returns_pages()
    {
        Http::fake([
            'https://api.notion.com/v1/data_sources/a42a62ed-9b51-4b98-9dea-ea6d091bc508/query*'
            => Http::response(
                json_decode(file_get_contents('tests/stubs/endpoints/data_sources/response_query_200.json'), true),
                200,
                ['Headers']
            )
        ]);

        $result = Notion::dataSources()->query('a42a62ed-9b51-4b98-9dea-ea6d091bc508', [
            'page_size' => 10,
        ]);

        $this->assertInstanceOf(PageCollection::class, $result);
        $collection = $result->asCollection();
        $this->assertIsIterable($collection);
        $this->assertCount(1, $collection);
    }

    /** @test */
    public function it_throws_exception_on_data_source_not_found()
    {
        Http::fake([
            'https://api.notion.com/v1/data_sources/nonexistent-id'
            => Http::response(
                json_decode('{"object":"error","status":404,"code":"object_not_found"}', true),
                404,
                ['Headers']
            )
        ]);

        $this->expectException(\Illuminate\Http\Client\RequestException::class);
        $this->expectExceptionMessage('HTTP request returned status code 404');

        Notion::dataSources()->find('nonexistent-id');
    }
}
