<?php

namespace Coyote\ApiModel;

use Coyote\ApiModel\Partial\MembershipAttributes;

class MembershipApiModel
{
    public string $id;
    public string $type;
    public MembershipAttributes $attributes;
}
