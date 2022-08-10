<?php

namespace Coyote\Model;

use Coyote\ApiModel\MembershipApiModel;

class MembershipModel
{
    private string $id;
    private string $name;
    private string $email;
    private string $organizationId;
    private ?OrganizationModel $organization = null;

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getRole(): string
    {
        return $this->role;
    }
    private string $role;

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
     * @return OrganizationModel|null
     */
    public function getOrganization(): ?OrganizationModel
    {
        return $this->organization;
    }

    public function setOrganisation(array $organizations): void
    {
        $matches = array_filter($organizations, function (OrganizationModel $org): bool {
            return $org->getId() === $this->organizationId;
        });

        if (count($matches) !== 1) {
            return;
        }

        $this->organization = array_shift($matches);
    }

    /**
     * @param MembershipApiModel $model
     */
    public function __construct(MembershipApiModel $model)
    {
        $this->id = $model->id;
        $this->name = join(' ', [$model->attributes->first_name, $model->attributes->last_name]);
        $this->email = $model->attributes->email;
        $this->role = $model->attributes->role;
        $this->organizationId = $model->attributes->organization_id;
    }
}
