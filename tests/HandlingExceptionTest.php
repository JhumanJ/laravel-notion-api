<?php

namespace FiveamCode\LaravelNotionApi\Tests;

use PHPUnit\Framework\TestCase;
use FiveamCode\LaravelNotionApi\Exceptions\HandlingException;

/**
 * Class HandlingExceptionTest
 * @package FiveamCode\LaravelNotionApi\Tests
 */
class HandlingExceptionTest extends TestCase
{


    /** @test */
    public function it_returns_a_handling_exception_instance_with_payload()
    {
        $wrapperException = HandlingException::instance('An error occured.', ['foo' => 'bar']);

        $this->assertInstanceOf(
            HandlingException::class,
            $wrapperException
        );

        $this->assertNotEmpty($wrapperException->getPayload());

    }

    /** @test */
    public function it_returns_a_handling_exception_instance_without_payload()
    {
        $wrapperException = HandlingException::instance('An error occured.');

        $this->assertInstanceOf(
            HandlingException::class,
            $wrapperException
        );

        $this->assertEmpty($wrapperException->getPayload());
    }
}