<?php

namespace Coyote\Request;

use Coyote\ApiModel\OrganizationApiModel;
use Coyote\ApiModel\ResourceRepresentationApiModel;
use Coyote\ApiResponse\CreateResourceApiResponse;
use Coyote\InternalApiClient;
use Coyote\Model\ResourceModel;
use Coyote\Payload\CreateResourcePayload;
use JetBrains\PhpStorm\ArrayShape;
use JsonMapper\JsonMapperFactory;
use stdClass;

class CreateResourceRequest
{
    private const PATH = '/resources/';

    private CreateResourcePayload $payload;
    private InternalApiClient $apiClient;

    public function __construct(InternalApiClient $apiClient, CreateResourcePayload $payload)
    {
        $this->apiClient = $apiClient;
        $this->payload = $payload;
    }

    public function perform(): ?ResourceModel
    {
        $json = $this->apiClient->post(
            self::PATH,
            $this->marshallPayload(),
            [InternalApiClient::INCLUDE_ORG_ID => true]
        );

        if (is_null($json)) {
            return null;
        }

        $mapper = (new JsonMapperFactory())->bestFit();
        $response = new CreateResourceApiResponse();
        $mapper->mapObject($json, $response);

        $organizationApiModel = $this->getOrganizationApiModel($response);
        $representationApiModels = $this->getRepresentationApiModels($response);

        return new ResourceModel($response->data, $organizationApiModel, $representationApiModels);
    }

    private function getOrganizationApiModel(CreateResourceApiResponse $response): OrganizationApiModel
    {
        $mapper = (new JsonMapperFactory())->bestFit();

        $organizationApiModel = new OrganizationApiModel();

        /** @var stdClass[] $organizationApiData */
        $organizationApiData = array_filter($response->included, function ($data) {
            return $data->type === OrganizationApiModel::TYPE;
        });

        $data = array_shift($organizationApiData) ?? new stdClass();

        $mapper->mapObject($data, $organizationApiModel);

        return $organizationApiModel;
    }

    /** @return ResourceRepresentationApiModel[]
     */
    private function getRepresentationApiModels(CreateResourceApiResponse $response): array
    {
        $mapper = (new JsonMapperFactory())->bestFit();

        return $mapper->mapArray(array_filter($response->included, function ($data) {
            return $data->type === ResourceRepresentationApiModel::TYPE;
        }), new ResourceRepresentationApiModel());
    }

    /** @return mixed[] */
    private function marshallPayload(): array
    {
        return [
            'name' => $this->payload->name,
            'source_uri' => $this->payload->source_uri,
            'resource_type' => $this->payload->resource_type,
            'resource_group_id' => $this->payload->resource_group_id,
            'host_uris' => $this->payload->host_uris,
            'representations' => is_null($this->payload->representations)
                ? null
                : array_map('get_object_vars', $this->payload->representations)
        ];
    }
}
