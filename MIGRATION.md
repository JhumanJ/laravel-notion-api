# Migration Guide: Notion API 2025-09-03 (Data Sources)

This guide explains the breaking changes introduced in this package version that align with [Notion API 2025-09-03](https://developers.notion.com/docs/upgrade-guide-2025-09-03) and the new **data source model**.

## Overview

Notion API 2025-09-03 introduces **multi-source databases**—a single database can now contain multiple linked data sources. This is a **breaking change** that affects how you:

1. Query databases (now via data sources)
2. Create pages (now with data_source_id parents)
3. Search workspaces (now returns data_source objects)

This package now **requires version 2025-09-03 or newer** and provides full support for the data source model.

---

## What Changed

### 1. **Notion-Version Header**

**Before:** Could use any API version  
**Now:** Defaults to and enforces `2025-09-03` minimum

```php
// ✅ Works (2025-09-03 or newer)
$notion = new Notion($token, 'v1', '2025-09-03');

// ❌ Throws HandlingException (older than 2025-09-03)
$notion = new Notion($token, 'v1', '2022-06-28');
```

**Config default** (`config/laravel-notion-api.php`):
```php
'version_header' => [
    'v1' => '2025-09-03'  // Default enforced version
]
```

---

### 2. **Databases: Structure-Only Operations**

Databases are now **metadata containers** only. Only three operations are supported:

#### Create a Database
```php
// NEW: Create database with a parent page
$database = \Notion::databases()->create([
    'parent' => ['type' => 'page_id', 'page_id' => $pageId],
    'title' => [['text' => ['content' => 'My Database']]],
    'properties' => [
        'Name' => ['title' => []],
        'Status' => ['select' => ['options' => []]]
    ]
]);
```

#### Retrieve a Database
```php
// ✅ Still works (to fetch structure and data sources)
$db = \Notion::databases()->find($databaseId);

// Get all data sources in this database
$dataSources = $db->getDataSources();  // Collection

// Get first data source (common case)
$dataSourceId = $db->getFirstDataSourceId();  // string|null
```

#### Update a Database
```php
// NEW: Update database title or properties
$database = \Notion::databases()->update($databaseId, [
    'title' => [['text' => ['content' => 'Updated Title']]],
    'properties' => [
        'Priority' => ['number' => []]
    ]
]);
```

#### ❌ Removed: List Databases
```php
// NO LONGER WORKS
\Notion::databases()->all();  // Method removed
```

---

### 3. **Data Sources: New Query Endpoint**

**Before:** Queried databases directly  
**Now:** Query data sources within a database

#### Retrieve a Data Source
```php
// NEW: Get a specific data source
$dataSource = \Notion::dataSources()->find($dataSourceId);

echo $dataSource->getName();        // "My Task Tracker"
echo $dataSource->getObjectType();  // "data_source"
```

#### Query a Data Source
```php
// NEW: Query pages within a data source
$db = \Notion::databases()->find($databaseId);
$dataSourceId = $db->getFirstDataSourceId();

// Build your filters/sorts (same as before)
$body = [
    'filter' => [
        'or' => [
            ['property' => 'Status', 'select' => ['equals' => 'In Progress']]
        ]
    ],
    'sorts' => [
        ['property' => 'Created', 'direction' => 'descending']
    ],
    'page_size' => 10
];

// Query the data source
$pages = \Notion::dataSources()
    ->query($dataSourceId, $body)
    ->asCollection();
```

---

### 4. **Page Creation: Data Source Parents**

**Before:** Pages were created with `database_id` parent  
**Now:** Pages are created with `data_source_id` parent

#### Create Page in Data Source
```php
// OLD (no longer works)
$page = \Notion::pages()->createInDatabase($databaseId, $page);

// NEW (required)
$db = \Notion::databases()->find($databaseId);
$dataSourceId = $db->getFirstDataSourceId();

$page = new Page();
$page->setTitle('Name', 'My New Page');

$createdPage = \Notion::pages()->createInDataSource($dataSourceId, $page);
```

#### Create Page in Another Page
```php
// ✅ Still works (unchanged)
$page = \Notion::pages()->createInPage($parentPageId, $page);
```

---

### 5. **Search: Data Source Results**

**Before:** Returned databases and pages  
**Now:** Returns data sources and pages

#### Search Everything
```php
// Returns Collection with DataSource and Page entities
$results = \Notion::search('tasks')
    ->query()
    ->asCollection();

foreach ($results as $result) {
    if ($result instanceof \FiveamCode\LaravelNotionApi\Entities\DataSource) {
        echo "Data Source: " . $result->getName();
    } elseif ($result instanceof \FiveamCode\LaravelNotionApi\Entities\Page) {
        echo "Page: " . $result->getTitle();
    }
}
```

#### Search Only Data Sources
```php
// NEW: Filter for data sources only
$dataSources = \Notion::search('projects')
    ->onlyDataSources()
    ->query()
    ->asCollection();
```

#### Search Only Pages
```php
// ✅ Still works (unchanged)
$pages = \Notion::search('tasks')
    ->onlyPages()
    ->query()
    ->asCollection();
```

---

### 6. **Relation Properties: Data Source IDs**

When reading database schemas (property definitions), relation properties now include both `database_id` and `data_source_id`.

```php
$db = \Notion::databases()->find($databaseId);
$relationProp = $db->getProperty('Related Items');

// NEW helpers
$targetDatabaseId = $relationProp->getTargetDatabaseId();    // "abc-123-..."
$targetDataSourceId = $relationProp->getTargetDataSourceId(); // "def-456-..."

// Raw content still available
$raw = $relationProp->getRawContent(); 
// ['database_id' => '...', 'data_source_id' => '...']
```

---

## Migration Checklist

### Step 1: Update Your .env (if needed)
No changes required—the package defaults to 2025-09-03 automatically.

### Step 2: Upgrade Package Version
```bash
composer update fiveam-code/laravel-notion-api
```

### Step 3: Find & Replace Database Queries

**Pattern 1: Discover data source IDs**
```php
// BEFORE
$db = Notion::databases()->find($databaseId);
// Use $db directly for queries

// AFTER
$db = Notion::databases()->find($databaseId);
$dataSourceId = $db->getFirstDataSourceId(); // ← Add this
```

**Pattern 2: Query operations**
```php
// BEFORE
Notion::database($databaseId)
    ->filterBy($filters)
    ->sortBy($sorts)
    ->query();

// AFTER
$db = Notion::databases()->find($databaseId);
$dataSourceId = $db->getFirstDataSourceId();

Notion::dataSources()
    ->query($dataSourceId, [
        'filter' => ['or' => Filter::filterQuery($filters)],
        'sorts' => Sorting::sortQuery($sorts),
    ]);
```

**Pattern 3: Page creation**
```php
// BEFORE
Notion::pages()->createInDatabase($databaseId, $page);

// AFTER
$db = Notion::databases()->find($databaseId);
$dataSourceId = $db->getFirstDataSourceId();
Notion::pages()->createInDataSource($dataSourceId, $page);
```

**Pattern 4: List databases**
```php
// BEFORE
Notion::databases()->all();

// AFTER
// Use Search API instead
Notion::search()
    ->onlyDataSources()
    ->query()
    ->asCollection();
```

### Step 4: Test Your Application
```bash
vendor/bin/phpunit tests
```

---

## New API: Complete Reference

### Endpoints

#### Databases
```php
// Find
$db = \Notion::databases()->find($id);

// Create
$db = \Notion::databases()->create([...]);

// Update
$db = \Notion::databases()->update($id, [...]);
```

#### Data Sources (NEW)
```php
// Find
$ds = \Notion::dataSources()->find($id);

// Query
$pages = \Notion::dataSources()->query($id, [...])->asCollection();
```

#### Pages
```php
// Create in data source (changed)
\Notion::pages()->createInDataSource($dataSourceId, $page);

// Create in page (unchanged)
\Notion::pages()->createInPage($pageId, $page);
```

#### Search
```php
// All entities
\Notion::search()->query()->asCollection();

// Only data sources (new)
\Notion::search()->onlyDataSources()->query()->asCollection();

// Only pages (unchanged)
\Notion::search()->onlyPages()->query()->asCollection();
```

### Entities

#### Database
```php
$db->getId();
$db->getTitle();
$db->getObjectType();          // "database"
$db->getProperties();           // Collection
$db->getProperty($key);
$db->getDataSources();          // Collection (NEW)
$db->getFirstDataSourceId();    // string|null (NEW)
```

#### DataSource (NEW)
```php
$ds->getId();
$ds->getName();
$ds->getObjectType();           // "data_source"
$ds->getProperties();
$ds->getProperty($key);
```

#### Page (unchanged)
```php
$page->getId();
$page->getTitle();
// ... (all existing methods work)
```

---

## FAQ

### Q: How do I handle multi-source databases?

```php
$db = \Notion::databases()->find($databaseId);
$dataSources = $db->getDataSources();  // Collection

foreach ($dataSources as $ds) {
    $results = \Notion::dataSources()
        ->query($ds['id'], $queryBody)
        ->asCollection();
    
    // Process results
}
```

### Q: What if a database has only one data source?

Use `getFirstDataSourceId()` for convenience:

```php
$dataSourceId = $db->getFirstDataSourceId();
if ($dataSourceId) {
    // Query this data source
}
```

### Q: Can I still use old API versions?

No. This package requires 2025-09-03 or newer. If you need older versions, pin to a previous package release.

### Q: How do I migrate from the old package?

Follow the checklist above. The main changes are:
1. Add `$db->getFirstDataSourceId()` step before queries
2. Replace `Notion::database()->query()` with `Notion::dataSources()->query()`
3. Replace `createInDatabase()` with `createInDataSource()`

### Q: Where's the Database::query() method?

It's been removed in favor of data source queries. Use `Notion::dataSources()->query()` instead.

---

## Resources

- [Notion API 2025-09-03 Upgrade Guide](https://developers.notion.com/docs/upgrade-guide-2025-09-03)
- [Notion API FAQs: Version 2025-09-03](https://developers.notion.com/docs/upgrade-faqs-2025-09-03)
- [Package Documentation](https://5amco.de/docs)

---

## Need Help?

- Check this migration guide
- Review the [README.md](README.md) examples
- Open an issue on [GitHub](https://github.com/fiveam-code/laravel-notion-api)

