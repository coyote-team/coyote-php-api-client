<?php

namespace Coyote\ApiModel\Partial;

class ResourceUpdateRelationships
{
    public SingleOrganizationRelationship $organization;
    public RepresentationRelationship $representations;
    public ResourceGroupRelationship $resource_groups;
}
