<?php

namespace Coyote\Model;

use Coyote\ApiModel\OrganizationApiModel;
use Coyote\ApiModel\ProfileApiModel;

class ProfileModel
{
    private string $id;

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return OrganizationModel[]
     */
    public function getOrganizations(): array
    {
        return $this->organizations;
    }

    /**
     * @return MembershipModel[]
     */
    public function getMemberships(): array
    {
        return $this->memberships;
    }

    private string $name;

    /** @var array<OrganizationModel> */
    private array $organizations;

    /**
     * @param ProfileApiModel $model
     * @param array<OrganizationApiModel> $organizationApiModels
     */
    public function __construct(ProfileApiModel $model, array $organizationApiModels)
    {
        $this->id = $model->id;
        $this->name = join(' ', [$model->attributes->first_name, $model->attributes->last_name]);
        $this->organizations = $this->mapOrganizationApiModelsToOrganizationModels($organizationApiModels);
    }

    /**
     * @param array<OrganizationApiModel> $apiModels
     * @return array<OrganizationModel>
     */
    private function mapOrganizationApiModelsToOrganizationModels(array $apiModels): array
    {
        return array_map(function ($apiModel) {
            return new OrganizationModel($apiModel);
        }, $apiModels);
    }
}
