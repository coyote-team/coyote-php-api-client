<?php

namespace Coyote\ApiPayload;

use Coyote\ApiModel\OrganizationApiModel;
use Coyote\ApiModel\ResourceGroupApiModel;
use Coyote\ApiModel\ResourceRepresentationApiModel;
use Coyote\ApiModel\ResourceUpdateApiModel;

class ResourceUpdatePayloadApiModel
{
    public ResourceUpdateApiModel $data;

    /** @var array<OrganizationApiModel|ResourceRepresentationApiModel|ResourceGroupApiModel> */
    public array $included;
}