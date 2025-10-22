<?php

namespace FiveamCode\LaravelNotionApi\Entities;

use DateTime;
use FiveamCode\LaravelNotionApi\Exceptions\HandlingException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;


/**
 * Class Database
 * @package FiveamCode\LaravelNotionApi\Entities
 */
class Database extends Entity
{
    /**
     * @var string
     */
    protected string $title = '';

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
    private string $cover = '';

    /**
     * @var string
     */
    private string $coverType = '';

    /**
     * @var string
     */
    private string $url;

    /**
     * @var string
     */
    protected string $objectType = '';

    /**
     * @var array
     */
    protected array $rawTitle = [];

    /**
     * @var Collection
     */
    protected Collection $dataSources;

    /**
     * @var DateTime
     */
    protected DateTime $createdTime;

    /**
     * @var DateTime
     */
    protected DateTime $lastEditedTime;

    /**
     * @var array
     */
    protected array $parent = [];

    /**
     * @var bool
     */
    protected bool $isInline = false;

    /**
     * @var bool
     */
    protected bool $archived = false;

    /**
     * @var bool
     */
    protected bool $inTrash = false;

    /**
     * @var string|null
     */
    protected ?string $publicUrl = null;


    /**
     * 
     */
    protected function setResponseData(array $responseData): void
    {
        parent::setResponseData($responseData);
        if ($responseData['object'] !== 'database')
            throw HandlingException::instance('invalid json-array: the given object is not a database');
        $this->fillFromRaw();
    }

    /**
     *
     */
    private function fillFromRaw()
    {
        $this->fillId();
        $this->fillIcon();
        $this->fillCover();
        $this->fillTitle();
        $this->fillObjectType();
        $this->fillDataSources();
        $this->fillDatabaseUrl();
        $this->fillCreatedTime();
        $this->fillLastEditedTime();
        $this->fillParent();
        $this->fillIsInline();
        $this->fillArchived();
        $this->fillInTrash();
        $this->fillPublicUrl();
    }

    /**
     *
     */
    private function fillTitle(): void
    {
        if (Arr::exists($this->responseData, 'title') && is_array($this->responseData['title'])) {
            $this->title = Arr::first($this->responseData['title'], null, ['plain_text' => ''])['plain_text'];
            $this->rawTitle = $this->responseData['title'];
        }
    }

    /**
     *
     */
    private function fillDatabaseUrl(): void
    {
        if (Arr::exists($this->responseData, 'url')) {
            $this->url = $this->responseData['url'];
        }
    }

      /**
     *
     */
    private function fillIcon(): void
    {
        if (Arr::exists($this->responseData, 'icon') && $this->responseData['icon'] != null) {
            $this->iconType = $this->responseData['icon']['type'];
            if(Arr::exists($this->responseData['icon'], 'emoji')){
                $this->icon = $this->responseData['icon']['emoji'];
            }
            else if(Arr::exists($this->responseData['icon'], 'file')){
                $this->icon = $this->responseData['icon']['file']['url'];
            }
            else if(Arr::exists($this->responseData['icon'], 'external')){
                $this->icon = $this->responseData['icon']['external']['url'];
            }
        }
    }

     /**
     *
     */
    private function fillCover(): void
    {
        if (Arr::exists($this->responseData, 'cover') && $this->responseData['cover'] != null) {
            $this->coverType = $this->responseData['cover']['type'];
            if(Arr::exists($this->responseData['cover'], 'file')){
                $this->cover = $this->responseData['cover']['file']['url'];
            }
            else if(Arr::exists($this->responseData['cover'], 'external')){
                $this->cover = $this->responseData['cover']['external']['url'];
            }
        }
    }

    /**
     *
     */
    private function fillObjectType(): void
    {
        if (Arr::exists($this->responseData, 'object')) {
            $this->objectType = $this->responseData['object'];
        }
    }

    /**
     * Capture data_sources array from database response (2025-09-03)
     */
    private function fillDataSources(): void
    {
        $this->dataSources = new Collection();
        if (Arr::exists($this->responseData, 'data_sources') && is_array($this->responseData['data_sources'])) {
            foreach ($this->responseData['data_sources'] as $ds) {
                $this->dataSources->add($ds);
            }
        }
    }

    /**
     * Fill parent (page) information
     */
    private function fillParent(): void
    {
        if (Arr::exists($this->responseData, 'parent') && is_array($this->responseData['parent'])) {
            $this->parent = $this->responseData['parent'];
        }
    }

    /**
     * Fill is_inline status
     */
    private function fillIsInline(): void
    {
        if (Arr::exists($this->responseData, 'is_inline')) {
            $this->isInline = (bool) $this->responseData['is_inline'];
        }
    }

    /**
     * Fill archived status
     */
    private function fillArchived(): void
    {
        if (Arr::exists($this->responseData, 'archived')) {
            $this->archived = (bool) $this->responseData['archived'];
        }
    }

    /**
     * Fill in_trash status
     */
    private function fillInTrash(): void
    {
        if (Arr::exists($this->responseData, 'in_trash')) {
            $this->inTrash = (bool) $this->responseData['in_trash'];
        }
    }

    /**
     * Fill public_url
     */
    private function fillPublicUrl(): void
    {
        if (Arr::exists($this->responseData, 'public_url') && $this->responseData['public_url'] !== null) {
            $this->publicUrl = (string) $this->responseData['public_url'];
        }
    }

    /**
     * @return Collection
     */
    public function getDataSources(): Collection
    {
        return $this->dataSources;
    }

    /**
     * @return string|null
     */
    public function getFirstDataSourceId(): ?string
    {
        $first = $this->dataSources->first();
        return $first['id'] ?? null;
    }

    /**
     * @return string
     */
    public function getObjectType(): string
    {
        return $this->objectType;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getIcon(): string
    {
        return $this->icon;
    }

    /**
     * @return string
     */
    public function getIconType(): string
    {
        return $this->iconType;
    }

        /**
     * @return string
     */
    public function getCover(): string
    {
        return $this->cover;
    }

    /**
     * @return string
     */
    public function getCoverType(): string
    {
        return $this->coverType;
    }

    /**
     * @return array
     */
    public function getRawTitle(): array
    {
        return $this->rawTitle;
    }

    /**
     * @return DateTime
     */
    public function getCreatedTime(): DateTime
    {
        return $this->createdTime;
    }

    /**
     * @return array
     */
    public function getLastEditedTime(): DateTime
    {
        return $this->lastEditedTime;
    }

    /**
     * Get the parent information (typically a page)
     *
     * @return array Parent object containing 'type' and 'page_id'
     */
    public function getParent(): array
    {
        return $this->parent;
    }

    /**
     * Get the parent page ID
     *
     * @return string|null The page ID if parent exists and has page_id
     */
    public function getParentPageId(): ?string
    {
        return $this->parent['page_id'] ?? null;
    }

    /**
     * Check if database is displayed inline
     *
     * @return bool True if inline, false if full page
     */
    public function isInline(): bool
    {
        return $this->isInline;
    }

    /**
     * Check if database is archived
     *
     * @return bool
     */
    public function isArchived(): bool
    {
        return $this->archived;
    }

    /**
     * Check if database is in trash
     *
     * @return bool
     */
    public function isInTrash(): bool
    {
        return $this->inTrash;
    }

    /**
     * Get the public URL if database has been published
     *
     * @return string|null The public URL or null if not published
     */
    public function getPublicUrl(): ?string
    {
        return $this->publicUrl;
    }
}
