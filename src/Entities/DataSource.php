<?php

namespace FiveamCode\LaravelNotionApi\Entities;

use FiveamCode\LaravelNotionApi\Entities\Properties\Property;
use FiveamCode\LaravelNotionApi\Exceptions\HandlingException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class DataSource extends Entity
{
    /**
     * @var string
     */
    protected string $objectType = '';

    /**
     * @var string
     */
    protected string $name = '';

    /**
     * @var string
     */
    private string $icon = '';

    /**
     * @var string
     */
    private string $iconType = '';

    /**
     * @var string
     */
    protected string $description = '';

    /**
     * @var array
     */
    protected array $rawDescription = [];

    /**
     * @var bool
     */
    protected bool $archived = false;

    /**
     * @var bool
     */
    protected bool $inTrash = false;

    /**
     * @var array
     */
    protected array $rawProperties = [];

    /**
     * @var array
     */
    protected array $propertyKeys = [];

    /**
     * @var array
     */
    protected array $propertyMap = [];

    /**
     * @var Collection
     */
    protected Collection $properties;

    /**
     * @var array
     */
    protected array $parent = [];

    /**
     * @var array
     */
    protected array $databaseParent = [];

    /**
     * @param array $responseData
     * @throws HandlingException
     */
    protected function setResponseData(array $responseData): void
    {
        parent::setResponseData($responseData);
        if ($responseData['object'] !== 'data_source') {
            throw HandlingException::instance('invalid json-array: the given object is not a data_source');
        }
        $this->fillFromRaw();
    }

    private function fillFromRaw(): void
    {
        $this->fillId();
        $this->fillObjectType();
        $this->fillName();
        $this->fillIcon();
        $this->fillDescription();
        $this->fillArchived();
        $this->fillInTrash();
        $this->fillProperties();
        $this->fillParent();
        $this->fillDatabaseParent();
    }

    private function fillObjectType(): void
    {
        if (Arr::exists($this->responseData, 'object')) {
            $this->objectType = $this->responseData['object'];
        }
    }

    private function fillName(): void
    {
        if (Arr::exists($this->responseData, 'name')) {
            $this->name = (string) $this->responseData['name'];
        } elseif (Arr::exists($this->responseData, 'title') && is_array($this->responseData['title'])) {
            // Handle title field from search API results (array of rich text objects)
            $this->name = Arr::first($this->responseData['title'], null, ['plain_text' => ''])['plain_text'];
        }
    }

    private function fillIcon(): void
    {
        if (Arr::exists($this->responseData, 'icon') && $this->responseData['icon'] != null) {
            $this->iconType = $this->responseData['icon']['type'];
            if (Arr::exists($this->responseData['icon'], 'emoji')) {
                $this->icon = $this->responseData['icon']['emoji'];
            } elseif (Arr::exists($this->responseData['icon'], 'file')) {
                $this->icon = $this->responseData['icon']['file']['url'];
            } elseif (Arr::exists($this->responseData['icon'], 'external')) {
                $this->icon = $this->responseData['icon']['external']['url'];
            }
        }
    }

    private function fillDescription(): void
    {
        if (Arr::exists($this->responseData, 'description') && is_array($this->responseData['description'])) {
            $this->description = Arr::first($this->responseData['description'], null, ['plain_text' => ''])['plain_text'];
            $this->rawDescription = $this->responseData['description'];
        }
    }

    private function fillArchived(): void
    {
        if (Arr::exists($this->responseData, 'archived')) {
            $this->archived = (bool) $this->responseData['archived'];
        }
    }

    private function fillInTrash(): void
    {
        if (Arr::exists($this->responseData, 'in_trash')) {
            $this->inTrash = (bool) $this->responseData['in_trash'];
        }
    }

    private function fillProperties(): void
    {
        if (Arr::exists($this->responseData, 'properties')) {
            $this->rawProperties = $this->responseData['properties'];
            $this->propertyKeys = array_keys($this->rawProperties);
            $this->properties = new Collection();

            foreach ($this->rawProperties as $propertyKey => $propertyContent) {
                $propertyObj = Property::fromResponse($propertyKey, $propertyContent);
                $this->properties->add($propertyObj);
                $this->propertyMap[$propertyKey] = $propertyObj;
            }
        }
    }

    private function fillParent(): void
    {
        if (Arr::exists($this->responseData, 'parent') && is_array($this->responseData['parent'])) {
            $this->parent = $this->responseData['parent'];
        }
    }

    private function fillDatabaseParent(): void
    {
        if (Arr::exists($this->responseData, 'database_parent') && is_array($this->responseData['database_parent'])) {
            $this->databaseParent = $this->responseData['database_parent'];
        }
    }

    public function getObjectType(): string
    {
        return $this->objectType;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getProperties(): Collection
    {
        return $this->properties;
    }

    public function getProperty(string $propertyKey): ?Properties\Property
    {
        if (!isset($this->propertyMap[$propertyKey])) {
            return null;
        }
        return $this->propertyMap[$propertyKey];
    }

    /**
     * Get the parent database information
     *
     * @return array Parent object containing 'type' and 'database_id'
     */
    public function getParent(): array
    {
        return $this->parent;
    }

    /**
     * Get the database's parent (grandparent) information
     *
     * @return array Parent object containing 'type' and 'page_id'
     */
    public function getDatabaseParent(): array
    {
        return $this->databaseParent;
    }

    /**
     * Get the parent database ID
     *
     * @return string|null The database ID if parent exists and has database_id
     */
    public function getParentDatabaseId(): ?string
    {
        return $this->parent['database_id'] ?? null;
    }

    /**
     * Get the database's parent page ID
     *
     * @return string|null The page ID if database_parent exists and has page_id
     */
    public function getDatabaseParentPageId(): ?string
    {
        return $this->databaseParent['page_id'] ?? null;
    }

    /**
     * Get the icon
     *
     * @return string Icon emoji or URL
     */
    public function getIcon(): string
    {
        return $this->icon;
    }

    /**
     * Get the icon type
     *
     * @return string Icon type (emoji, file, or external)
     */
    public function getIconType(): string
    {
        return $this->iconType;
    }

    /**
     * Get the description
     *
     * @return string Description text
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Get the raw description (rich text array)
     *
     * @return array Raw description rich text objects
     */
    public function getRawDescription(): array
    {
        return $this->rawDescription;
    }

    /**
     * Check if data source is archived
     *
     * @return bool
     */
    public function isArchived(): bool
    {
        return $this->archived;
    }

    /**
     * Check if data source is in trash
     *
     * @return bool
     */
    public function isInTrash(): bool
    {
        return $this->inTrash;
    }
}


