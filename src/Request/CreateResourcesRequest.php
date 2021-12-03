<?php

namespace Coyote\Request;

use Coyote\ApiModel\OrganizationApiModel;
use Coyote\ApiModel\ResourceRepresentationApiModel;
use Coyote\ApiResponse\CreateResourceApiResponse;
use Coyote\ApiResponse\CreateResourcesApiResponse;
use Coyote\InternalApiClient;
use Coyote\Model\ResourceModel;
use Coyote\Payload\CreateResourcePayload;
use Coyote\Payload\CreateResourcesPayload;
use JsonMapper\JsonMapperFactory;

class CreateResourcesRequest
{
    private const PATH = '/resources/create';

    private CreateResourcesPayload $payload;
    private InternalApiClient $apiClient;

    public function __construct(InternalApiClient $apiClient, CreateResourcesPayload $payload)
    {
        $this->apiClient = $apiClient;
        $this->payload = $payload;
    }

    /** @return ResourceModel[]|null */
    public function perform(): ?array
    {
        $json = $this->apiClient->post(self::PATH, $this->marshallPayload(), [InternalApiClient::INCLUDE_ORG_ID => true]);

        if (is_null($json)) {
            return null;
        }

        $mapper = (new JsonMapperFactory())->bestFit();
        $response = new CreateResourcesApiResponse();
        $mapper->mapObject($json, $response);

        return null;
//        return new ResourceModel($response->data, null, $representationApiModels);
    }

    private function marshallPayload(): array
    {
        return [
            'resources' => array_map(function (CreateResourcePayload $resource) {
                return [
                    'name' => $resource->name,
                    'source_uri' => $resource->source_uri,
                    'resource_type' => $resource->resource_type,
                    'resource_group_id' => $resource->resource_group_id,
                    'host_uris' => $resource->host_uris,
                    'representations' => is_null($resource->representations)
                        ? null
                        : array_map('get_object_vars', $resource->representations)
                ];
            }, $this->payload->resources)
        ];
    }
}