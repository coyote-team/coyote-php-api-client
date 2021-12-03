<?php

namespace Coyote\Request;

use Coyote\ApiResponse\GetResourceRepresentationsApiResponse;
use Coyote\InternalApiClient;
use Coyote\Model\RepresentationModel;
use JsonMapper\JsonMapperFactory;
use stdClass;

class GetResourceRepresentationsRequest
{
    private const PATH = '/resources/%s/representations';

    private InternalApiClient $client;
    private string $resource_id;

    public function __construct(InternalApiClient $client, string $resource_id)
    {
        $this->client = $client;
        $this->resource_id = $resource_id;
    }

    /** @return RepresentationModel[]|null */
    public function data(): ?array {
        $json = $this->client->get(sprintf(self::PATH, $this->resource_id));

        if (is_null($json)) {
            return null;
        }

        return $this->mapResponseToRepresentationModels($json);
    }

    /** @return RepresentationModel[] */
    private function mapResponseToRepresentationModels(stdClass $json): array
    {
        $mapper = (new JsonMapperFactory())->bestFit();
        $response = new GetResourceRepresentationsApiResponse();
        $mapper->mapObject($json, $response);

        return $this->mapRepresentationApiModelsToRepresentationModels($response);
    }

    /** @return RepresentationModel[] */
    private function mapRepresentationApiModelsToRepresentationModels(GetResourceRepresentationsApiResponse $response): array
    {
        return array_map(function ($apiModel) {
            return new RepresentationModel($apiModel);
        }, $response->data);
    }
}