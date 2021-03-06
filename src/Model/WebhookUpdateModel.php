<?php

namespace Coyote\Model;

use Coyote\ApiModel\OrganizationApiModel;
use Coyote\ApiModel\ResourceApiModel;
use Coyote\ApiModel\ResourceGroupApiModel;
use Coyote\ApiModel\ResourceRepresentationApiModel;
use Coyote\ApiModel\WebhookUpdateApiModel;

class WebhookUpdateModel
{
    private string $id;
    private ?string $canonical_id;
    private string $name;
    private string $type;
    private string $source_uri;

    /** @var ResourceGroupModel[] */
    private array $resourceGroups;

    private ?OrganizationModel $organization;

    /** @var RepresentationModel[] */
    private array $representations;

    /**
     * @param WebhookUpdateApiModel $model
     * @param OrganizationApiModel|null $organizationApiModel
     * @param array<ResourceRepresentationApiModel> $representations
     * @param array<ResourceGroupApiModel> $resourceGroups
     */
    public function __construct(
        WebhookUpdateApiModel $model,
        ?OrganizationApiModel $organizationApiModel,
        array                 $representations,
        array                 $resourceGroups
    ) {
        $this->id = $model->id;
        $this->canonical_id = $model->attributes->canonical_id;
        $this->name = $model->attributes->name;
        $this->type = $model->attributes->resource_type;
        $this->source_uri = $model->attributes->source_uri;
        $this->organization = null;

        if (!is_null($organizationApiModel)) {
            $this->organization = new OrganizationModel($organizationApiModel);
        }

        $this->representations = array_map(function ($resourceRepresentationApiModel) {
            return new RepresentationModel($resourceRepresentationApiModel);
        }, $representations);

        $this->resourceGroups = array_map(function ($resourceGroupApiModel) {
            return new ResourceGroupModel($resourceGroupApiModel);
        }, $resourceGroups);
    }

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
    public function getCanonicalId(): ?string
    {
        return $this->canonical_id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getSourceUri(): string
    {
        return $this->source_uri;
    }

    /**
     * @return OrganizationModel|null
     */
    public function getOrganization(): ?OrganizationModel
    {
        return $this->organization;
    }

    /**
     * @return ResourceGroupModel[]
     */
    public function getResourceGroups(): array
    {
        return $this->resourceGroups;
    }

    /**
     * @return RepresentationModel[]
     */
    public function getRepresentations(): array
    {
        return $this->representations;
    }

    public function getTopRepresentationByMetum(string $metum): ?RepresentationModel
    {
        $byMetum = array_filter($this->representations, function (RepresentationModel $r) use ($metum): bool {
            return $r->getMetum() === $metum;
        });

        uasort($byMetum, function (RepresentationModel $a, RepresentationModel $b): int {
            $aO = $a->getOrdinality();
            $bO = $b->getOrdinality();

            if ($aO < $bO) {
                return -1;
            }

            if ($aO > $bO) {
                return 1;
            }

             return 0;
        });

        return array_pop($byMetum);
    }
}
