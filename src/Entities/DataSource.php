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
        $this->fillProperties();
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
}


