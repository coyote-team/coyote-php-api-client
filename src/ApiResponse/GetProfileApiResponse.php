<?php

namespace Coyote\ApiResponse;

use Coyote\ApiModel\OrganizationApiModel;
use Coyote\ApiModel\Partial\ApiMetaData;
use Coyote\ApiModel\ProfileApiModel;
use stdClass;

class GetProfileApiResponse
{
    public ProfileApiModel $data;

    /** @var OrganizationApiModel[] */
    public array $included;

    public ApiMetaData $jsonapi;
}
