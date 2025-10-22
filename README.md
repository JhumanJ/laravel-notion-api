<h1 align="center"> Laravel Notion API</h1>
<h2 align="center"> Effortless Notion integrations with Laravel</h2>

<p align="center">
<img src="https://5amco.de/images/5am.png" width="200" height="200">
</p>

[![Latest Version on Packagist](https://img.shields.io/packagist/v/fiveam-code/laravel-notion-api.svg?style=flat-square)](https://packagist.org/packages/fiveam-code/laravel-notion-api)
[![Total Downloads](https://img.shields.io/packagist/dt/fiveam-code/laravel-notion-api.svg?style=flat-square)](https://packagist.org/packages/fiveam-code/laravel-notion-api)

[comment]: <> (![GitHub Actions]&#40;https://github.com/fiveam-code/laravel-notion-api/actions/workflows/main.yml/badge.svg&#41;)

This package provides a simple and crisp way to access the Notion API endpoints, query data and update existing entries.

> Note: This package targets Notion API version 2025-09-03 and supports the new data source model. Databases are structures (create, update, retrieve). Content queries and page creation within a database now target data sources. See Notion’s upgrade guide: https://developers.notion.com/docs/upgrade-guide-2025-09-03.

## Installation

You can install the package via composer:

```bash
composer require fiveam-code/laravel-notion-api
```

### Upgrading from Previous Versions

This version introduces **breaking changes** for Notion API 2025-09-03. If you're upgrading from an older version, please review the [MIGRATION.md](MIGRATION.md) file for detailed upgrade instructions.

### Authorization

The Notion API requires an access token and a Notion integration, [the Notion documentation](https://developers.notion.com/docs/getting-started#before-we-begin) explains how this works. It's important to grant access to the integration within your Notion account to enable the API access.

Add your Notion API token to your `.env` file:

```
NOTION_API_TOKEN="$YOUR_ACCESS_TOKEN"
```

## Usage

Head over to the [Documentation](https://5amco.de/docs) of this package.

### 🔥 Code Examples to jumpstart your Notion API Project

#### Basic Setup (+ example)

```php
use FiveamCode\LaravelNotionApi\Notion;

# Access through Facade (token has to be set in .env)
\Notion::databases()->find($databaseId);

# Custom instantiation (necessary if you want to access more than one NotionApi integration)
$notion = new Notion($apiToken, $apiVersion); // version-default is 'v1' (Notion-Version 2025-09-03)
$notion->databases()->find($databaseId);
```

#### Fetch Page Information

```php
// Returns a specific page
\Notion::pages()->find($yourPageId);
```

#### Search

```php
// Returns pages and data sources of your workspace
\Notion::search($searchText)
        ->query()
        ->asCollection();

// Only data sources
\Notion::search($searchText)
        ->onlyDataSources()
        ->query()
        ->asCollection();
```

#### Query Data Source

```php
// Queries a specific data source and returns a collection of pages
$sortings = new Collection();
$filters = new Collection();

$sortings
  ->add(Sorting::propertySort('Ordered', 'ascending'));
$sortings
  ->add(Sorting::timestampSort('created_time', 'ascending'));

$filters
  ->add(Filter::textFilter('title', ['contains' => 'new']));
// or
$filters
  ->add(Filter::rawFilter('Tags', ['multi_select' => ['contains' => 'great']]));

// Retrieve database and pick a data source id
$db = \Notion::databases()->find($yourDatabaseId);
$dataSourceId = $db->getFirstDataSourceId();

// Build request body similar to old query payload
$body = [
  'filter' => ['or' => Filter::filterQuery($filters)],
  'sorts' => Sorting::sortQuery($sortings),
  'page_size' => 5,
];

\Notion::dataSources()
      ->query($dataSourceId, $body)
      ->asCollection();
```

### Testing

```bash
vendor/bin/phpunit tests
```

## Support

If you use this package in one of your projects or just want to support our development, consider becoming a [Patreon](https://www.patreon.com/bePatron?u=56662485)!

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email hello@dianaweb.dev instead of using the issue tracker.

## Used By

- Julien Nahum created [notionforms.io](https://notionforms.io) with [laravel-notion-api](https://github.com/5am-code/laravel-notion-api), which allows you to easily create custom forms, based on your selected database within notion.
- [GitHub Notion Sync](https://githubnotionsync.com/), a service by [Beyond Code](https://beyondco.de) to sync the issues of multiple GitHub repositories into a Notion database

Using this package in your project? Open a PR to add it in this section!

## Credits

- [Diana Scharf](https://github.com/mechelon)
- [Johannes Güntner](https://github.com/johguentner)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
