<?php

namespace Coyote\Request;

use Coyote\ApiModel\OrganizationApiModel;
use Coyote\ApiModel\ResourceRepresentationApiModel;
use Coyote\ApiResponse\GetResourceApiResponse;
use Coyote\ApiResponse\GetResourceRepresentationsApiResponse;
use Coyote\ApiResponse\GetResourcesApiResponse;
use Coyote\InternalApiClient;
use Coyote\Model\RepresentationModel;
use Coyote\Model\ResourceModel;
use JsonMapper\JsonMapperFactory;
use stdClass;

class GetResourceRequest
{
    private const PATH = '/resources/%s';

    private InternalApiClient $client;
    private string $resource_id;

    public function __construct(InternalApiClient $client, string $resource_id)
    {
        $this->client = $client;
        $this->resource_id = $resource_id;
    }

    public function data(): ?ResourceModel {
        $json = $this->client->get(sprintf(self::PATH, $this->resource_id));

        if (is_null($json)) {
            return null;
        }

        return $this->mapResponseToResourceModel($json);
    }

    private function mapResponseToResourceModel(stdClass $json): ?ResourceModel
    {
        $mapper = (new JsonMapperFactory())->bestFit();
        $response = new GetResourceApiResponse();
        $mapper->mapObject($json, $response);

        $organizationApiModel = $this->getOrganizationApiModel($response);
        $representationApiModels = $this->getRepresentationApiModels($response);

        return new ResourceModel($response->data, $organizationApiModel, $representationApiModels);
    }

    private function getOrganizationApiModel(GetResourceApiResponse $response): OrganizationApiModel
    {
        $mapper = (new JsonMapperFactory())->bestFit();

        $organizationApiModel = new OrganizationApiModel();

        /** @var \stdClass[] $organizationApiData */
        $organizationApiData = array_filter($response->included, function ($data) {
            return $data->type === OrganizationApiModel::TYPE;
        });

        $mapper->mapObject(array_shift($organizationApiData), $organizationApiModel);

        return $organizationApiModel;
    }

    /** @return ResourceRepresentationApiModel[]
     */
    private function getRepresentationApiModels(GetResourceApiResponse $response): array
    {
        $mapper = (new JsonMapperFactory())->bestFit();

        return $mapper->mapArray(array_filter($response->included, function ($data) {
            return $data->type === ResourceRepresentationApiModel::TYPE;
        }), new ResourceRepresentationApiModel());
    }
}