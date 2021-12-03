<?php

namespace Coyote\Request;

use Coyote\ApiResponse\CreateResourceGroupApiResponse;
use Coyote\ApiResponse\GetProfileApiResponse;
use Coyote\InternalApiClient;
use Coyote\Model\ResourceGroupModel;
use Coyote\Payload\CreateResourceGroupPayload;
use JsonMapper\JsonMapperFactory;

class CreateResourceGroupRequest
{
    private const PATH = '/resource_groups';

    private CreateResourceGroupPayload $payload;
    private InternalApiClient $apiClient;

    public function __construct(InternalApiClient $apiClient, CreateResourceGroupPayload $payload)
    {
        $this->apiClient = $apiClient;
        $this->payload = $payload;
    }

    public function perform(): ?ResourceGroupModel
    {
        $json = $this->apiClient->post(self::PATH, $this->marshallPayload(), [InternalApiClient::INCLUDE_ORG_ID => true]);

        if (is_null($json)) {
            return null;
        }

        $mapper = (new JsonMapperFactory())->bestFit();
        $response = new CreateResourceGroupApiResponse();
        $mapper->mapObject($json, $response);

        return $this->responseToResourceGroup($response);
    }

    private function responseToResourceGroup(CreateResourceGroupApiResponse $response): ResourceGroupModel
    {
        return new ResourceGroupModel($response->data);
    }

    private function marshallPayload(): array
    {
        return get_object_vars($this->payload);
    }
}