<?php

namespace Coyote\ApiPayload;

use Coyote\ApiModel\OrganizationApiModel;
use Coyote\ApiModel\ResourceApiModel;
use Coyote\ApiModel\ResourceRepresentationApiModel;

class ResourceUpdatePayloadApiModel
{
    public ResourceApiModel $data;

    /** @var array<OrganizationApiModel|ResourceRepresentationApiModel> */
    public array $included;
}