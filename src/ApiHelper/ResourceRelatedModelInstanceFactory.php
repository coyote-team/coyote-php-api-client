<?php

namespace Coyote\ApiHelper;

use Coyote\ApiModel\OrganizationApiModel;
use Coyote\ApiModel\ResourceRepresentationApiModel;
use RuntimeException;
use stdClass;

class ResourceRelatedModelInstanceFactory
{
    public function __invoke(stdClass $data): ResourceRepresentationApiModel|OrganizationApiModel
    {
        switch ($data->type) {
            case OrganizationApiModel::TYPE:
                return new OrganizationApiModel();
            case ResourceRepresentationApiModel::TYPE:
                return new ResourceRepresentationApiModel();
            default:
                throw new RuntimeException("Unable to create resource related model for type $data->type");
        }
    }
}
