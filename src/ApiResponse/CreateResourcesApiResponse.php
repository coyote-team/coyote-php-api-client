<?php

namespace Coyote\ApiResponse;

use Coyote\ApiModel\Partial\ApiMetaData;
use Coyote\ApiModel\ResourceApiModel;

class CreateResourcesApiResponse
{
    /** @var ResourceApiModel[] */
    public array $data;

    public ApiMetaData $jsonapi;
}