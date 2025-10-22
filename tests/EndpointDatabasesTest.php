<?php

namespace FiveamCode\LaravelNotionApi\Tests;

use Notion;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use FiveamCode\LaravelNotionApi\Entities\Database;
use FiveamCode\LaravelNotionApi\Exceptions\NotionException;

/**
 * Class EndpointDatabaseTest
 *
 * The fake API responses are based on Notions documentation.
 * @see https://developers.notion.com/reference/get-databases
 *
 * @package FiveamCode\LaravelNotionApi\Tests
 */
class EndpointDatabasesTest extends NotionApiTest
{
    // Databases::all() method removed in 2025-09-03 (list databases endpoint is deprecated)

    /** @test */
    public function it_returns_database_entity_with_filled_properties()
    {
        // successful /v1/databases/DATABASE_DOES_EXIST
        Http::fake([
            'https://api.notion.com/v1/databases/668d797c-76fa-4934-9b05-ad288df2d136'
            => Http::response(
                json_decode(file_get_contents('tests/stubs/endpoints/databases/response_specific_200.json'), true),
                200,
                ['Headers']
            )
        ]);

        $databaseResult = Notion::databases()->find('668d797c-76fa-4934-9b05-ad288df2d136');

        $this->assertInstanceOf(Database::class, $databaseResult);

        // check properties
        $this->assertSame('Grocery List', $databaseResult->getTitle());
        $this->assertSame('database', $databaseResult->getObjectType());

        $this->assertCount(1, $databaseResult->getRawTitle());
        $this->assertCount(12, $databaseResult->getRawProperties());

        $this->assertInstanceOf(Carbon::class, $databaseResult->getCreatedTime());
        $this->assertInstanceOf(Carbon::class, $databaseResult->getLastEditedTime());
    }

    /** @test */
    public function it_throws_a_notion_exception_not_found()
    {
        // failing /v1/databases/DATABASE_DOES_NOT_EXIST
        Http::fake([
            'https://api.notion.com/v1/databases/b55c9c91-384d-452b-81db-d1ef79372b79'
            => Http::response(
                json_decode(file_get_contents('tests/stubs/endpoints/databases/response_specific_404.json'), true),
                404,
                ['Headers']
            )
        ]);

        $this->expectException(\Illuminate\Http\Client\RequestException::class);
        $this->expectExceptionMessage('HTTP request returned status code 404');

        Notion::databases()->find('b55c9c91-384d-452b-81db-d1ef79372b79');
    }
}
