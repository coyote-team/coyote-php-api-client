<?php

namespace Tests;

use Coyote\ApiHelper\ResourceUpdatePayloadParser;
use Coyote\ApiPayload\ResourceUpdatePayloadApiModel;

class ResourceUpdatePayloadParserTest extends AbstractTestCase
{
    public function testParsingInvalidJsonReturnsNull(): void
    {
        $json = (object) ['foo' => 'bar', 'baz' => 'xuux'];
        $result = ResourceUpdatePayloadParser::parse($json);
        $this->assertNull($result);
    }
}
