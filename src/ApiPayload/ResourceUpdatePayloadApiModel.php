<?php

namespace Coyote\ApiPayload;

use Coyote\ApiModel\OrganizationApiModel;
use Coyote\ApiModel\Partial\ApiMetaData;
use Coyote\ApiModel\Partial\ResourceLinks;
use Coyote\ApiModel\ResourceApiModel;
use Coyote\ApiModel\ResourceRepresentationApiModel;

class ResourceUpdatePayloadApiModel
{
    public ResourceApiModel $data;

    /** @var array<OrganizationApiModel|ResourceRepresentationApiModel> */
    public array $included;

    public ApiMetaData $jsonapi;
}