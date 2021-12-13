<?php

namespace Coyote\ApiResponse;

use Coyote\ApiModel\OrganizationApiModel;
use Coyote\ApiModel\Partial\ApiMetaData;
use Coyote\ApiModel\Partial\ResourceLinks;
use Coyote\ApiModel\ProfileApiModel;

class GetProfileApiResponse
{
    public ProfileApiModel $data;

    /** @var OrganizationApiModel[] */
    public array $included;

    public ResourceLinks $links;
    public ApiMetaData $jsonapi;
}
