<?php

namespace Coyote\Traits;

use Coyote\ApiHelper\JsonMapperShapeValidatorMiddleware;
use JsonMapper\JsonMapperFactory;
use JsonMapper\JsonMapperInterface;

trait JsonMapperTrait
{
    public static function mapper(): JsonMapperInterface
    {
        $mapper = (new JsonMapperFactory())->bestFit();
        return $mapper;
    }
}