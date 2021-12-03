<?php

namespace Coyote\Request;

use Coyote\ApiModel\OrganizationApiModel;
use Coyote\ApiModel\ProfileApiModel;
use Coyote\ApiResponse\GetProfileApiResponse;
use Coyote\ApiResponse\GetResourceGroupsApiResponse;
use Coyote\InternalApiClient;
use Coyote\Model\ProfileModel;
use Coyote\Model\ResourceGroupModel;
use JsonMapper\JsonMapperFactory;
use stdClass;

class GetResourceGroupsRequest
{
    private const PATH = '/resource_groups/';

    private InternalApiClient $client;

    public function __construct(InternalApiClient $client)
    {
        $this->client = $client;
    }

    /** @return ResourceGroupModel[]|null */
    public function data(): ?array {
        $json = $this->client->get(self::PATH, [InternalApiClient::INCLUDE_ORG_ID => true]);

        if (is_null($json)) {
            return null;
        }

        return $this->mapResponseToResourceGroupModels($json);
    }

    /** @return ResourceGroupModel[] */
    private function mapResponseToResourceGroupModels(stdClass $json): array
    {
        $mapper = (new JsonMapperFactory())->bestFit();
        $response = new GetResourceGroupsApiResponse();
        $mapper->mapObject($json, $response);

        return $this->mapResourceGroupApiModelsToResourceGroupModels($response);
    }

    /** @return ResourceGroupModel[] */
    private function mapResourceGroupApiModelsToResourceGroupModels(GetResourceGroupsApiResponse $response): array
    {
        return array_map(function ($apiModel) {
            return new ResourceGroupModel($apiModel);
        }, $response->data);
    }
}