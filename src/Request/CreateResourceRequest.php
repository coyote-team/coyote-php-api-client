<?php

namespace Coyote\Request;

use Coyote\ApiResponse\CreateResourceApiResponse;
use Coyote\InternalApiClient;
use Coyote\Model\ResourceModel;
use Coyote\ModelHelper\ResourceModelHelper;
use Coyote\Payload\CreateResourcePayload;
use Coyote\RequestLogger;
use JsonMapper\JsonMapperFactory;
use Monolog\Logger;

class CreateResourceRequest
{
    private const PATH = '/resources/';

    private RequestLogger $logger;
    private CreateResourcePayload $payload;
    private InternalApiClient $apiClient;

    public function __construct(
        InternalApiClient $apiClient,
        CreateResourcePayload $payload,
        int $logLevel = Logger::INFO
    ) {
        $this->apiClient = $apiClient;
        $this->payload = $payload;
        $this->logger = new RequestLogger('CreateResourceRequest', $logLevel);
    }

    public function perform(): ?ResourceModel
    {
        try {
            $json = $this->apiClient->post(
                self::PATH,
                $this->marshallPayload(),
                [InternalApiClient::INCLUDE_ORG_ID => true]
            );
        } catch (\Exception $error) {
            $this->logger->error("Error creating resource {$this->payload->source_uri}: " . $error->getMessage());
            return null;
        }

        if (is_null($json)) {
            $this->logger->warn("Unexpected null response when creating resource {$this->payload->source_uri}");
            return null;
        }

        $mapper = (new JsonMapperFactory())->bestFit();

        /** @var CreateResourceApiResponse $response */
        $response = $mapper->mapObject($json, new CreateResourceApiResponse());

        return ResourceModelHelper::mapCreateResourceResponseToResourceModel($response);
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
