<?php

namespace Coyote\ApiHelper;

use Coyote\ApiPayload\ResourceUpdatePayloadApiModel;
use JsonMapper\JsonMapperFactory;

class ResourceUpdatePayloadParser
{
    public static function parse(\stdClass $payload): ?ResourceUpdatePayloadApiModel
    {
        $mapper = (new JsonMapperFactory())->bestFit();

        /** @return ResourceUpdatePayloadApiModel|null */
        return $mapper->mapObject($payload, (new ResourceUpdatePayloadApiModel()));
    }
}