<?php

namespace FiveamCode\LaravelNotionApi\Entities\Blocks;

/**
 * Class HeadingTwo
 * @package FiveamCode\LaravelNotionApi\Entities\Blocks
 */
class HeadingTwo extends TextBlock
{
    public static function create(array|string $textContent): HeadingTwo
    {
        $headingTwo = new HeadingTwo();
        HeadingTwo::createTextBlock($headingTwo, $textContent);
        return $headingTwo;
    }

    function __construct(array $responseData = null)
    {
        $this->type = "heading_2";
        parent::__construct($responseData);
    }
}
