<?php

namespace Coyote\ApiHelper;

use Coyote\ApiModel\OrganizationApiModel;
use Coyote\ApiModel\ResourceRepresentationApiModel;

class ResourceRelatedModelInstanceFactory
{
    public function __invoke(\stdClass $data)
    {
        switch ($data->type) {
            case OrganizationApiModel::TYPE:
                return new OrganizationApiModel();
            case ResourceRepresentationApiModel::TYPE:
                return new ResourceRepresentationApiModel();
            default:
                throw new \RuntimeException("Unable to create resource related model for type {$data->type}");
        }
    }
}