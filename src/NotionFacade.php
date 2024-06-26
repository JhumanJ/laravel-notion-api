<?php

namespace FiveamCode\LaravelNotionApi;

use Illuminate\Support\Facades\Facade;


/**
 * Class NotionFacade
 * @package FiveamCode\LaravelNotionApi
 */
class NotionFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return Notion::class;
    }
}
