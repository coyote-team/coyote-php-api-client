<?php

namespace Coyote\ModelHelper;

use Coyote\ApiModel\OrganizationApiModel;
use Coyote\ApiModel\ResourceGroupApiModel;
use Coyote\ApiModel\ResourceRepresentationApiModel;
use Coyote\ApiPayload\ResourceUpdatePayloadApiModel;
use Coyote\ApiResponse\CreateResourceApiResponse;
use Coyote\Model\ResourceModel;
use Coyote\Model\ResourceUpdateModel;
use JsonMapper\JsonMapperFactory;
use stdClass;

class ResourceModelHelper
{
    // TODO: These two functions can probably be simplified by using an Interface

    public static function mapCreateResourceResponseToResourceModel(
        CreateResourceApiResponse $response
    ): ResourceModel {
        $organizationApiModel = self::getOrganizationApiModel($response->included);
        $representationApiModels = self::getRepresentationApiModels($response->included);

        return new ResourceModel($response->data, $organizationApiModel, $representationApiModels);
    }

    public static function mapResourceUpdatePayloadApiModelToResourceUpdateModel(
        ResourceUpdatePayloadApiModel $update
    ): ResourceUpdateModel
    {
        $organizationApiModel = self::getOrganizationApiModel($update->included);
        $representationApiModels = self::getRepresentationApiModels($update->included);
        $resourceGroupModels = self::getResourceGroupApiModels($update->included);

        return new ResourceUpdateModel(
            $update->data,
            $organizationApiModel,
            $representationApiModels,
            $resourceGroupModels
        );
    }

    private static function getOrganizationApiModel(array $included): OrganizationApiModel
    {
        $mapper = (new JsonMapperFactory())->bestFit();

        $organizationApiModel = new OrganizationApiModel();

        /** @var stdClass[] $organizationApiData */
        $organizationApiData = array_filter($included, function ($data) {
            return $data->type === OrganizationApiModel::TYPE;
        });

        $data = array_shift($organizationApiData) ?? new stdClass();

        $mapper->mapObject($data, $organizationApiModel);

        return $organizationApiModel;
    }


    /** @return ResourceRepresentationApiModel[]
     */
    private static function getRepresentationApiModels(array $included): array
    {
        $mapper = (new JsonMapperFactory())->bestFit();

        return $mapper->mapArray(array_filter($included, function ($data) {
            return $data->type === ResourceRepresentationApiModel::TYPE;
        }), new ResourceRepresentationApiModel());
    }

    /** @return ResourceGroupApiModel[]
     */
    private static function getResourceGroupApiModels(array $included): array
    {
        $mapper = (new JsonMapperFactory())->bestFit();

        return $mapper->mapArray(array_filter($included, function ($data) {
            return $data->type === ResourceGroupApiModel::TYPE;
        }), new ResourceGroupApiModel());
    }

}
