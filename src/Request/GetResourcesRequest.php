<?php

namespace Coyote\Request;

use Coyote\ApiHelper\ResourceRelatedModelInstanceFactory;
use Coyote\ApiModel\AbstractResourceRelatedApiModel;
use Coyote\ApiModel\OrganizationApiModel;
use Coyote\ApiModel\ResourceApiModel;
use Coyote\ApiModel\ResourceRepresentationApiModel;
use Coyote\ApiResponse\GetResourcesApiResponse;
use Coyote\InternalApiClient;
use Coyote\Model\ResourceModel;
use JsonMapper\Handler\FactoryRegistry;
use JsonMapper\JsonMapperBuilder;
use JsonMapper\JsonMapperFactory;
use JsonMapper\Builders\PropertyMapperBuilder;
use stdClass;

class GetResourcesRequest
{
    private const PATH = '/resources/';

    private InternalApiClient $client;

    public function __construct(InternalApiClient $client)
    {
        $this->client = $client;
    }

    /** @return Array<ResourceModel>|null */
    public function data(
        ?int $pageNumber = null,
        ?int $pageSize = null,
        ?string $filterString = null,
        ?string $filterScope = null
    ): ?array {
        $json = $this->client->get(self::PATH, [InternalApiClient::INCLUDE_ORG_ID => true]);

        if (is_null($json)) {
            return null;
        }

        return $this->mapResponseToResourceModels($json);
    }

    /** @return Array<ResourceModel>|null */
    private function mapResponseToResourceModels(stdClass $json): ?array
    {
        $interfaceResolver = new FactoryRegistry();
        $interfaceResolver->addFactory(AbstractResourceRelatedApiModel::class, new ResourceRelatedModelInstanceFactory());
        $propertyMapper = PropertyMapperBuilder::new()
            ->withNonInstantiableTypeResolver($interfaceResolver)
            ->build();
        $mapper = JsonMapperBuilder::new()
            ->withDocBlockAnnotationsMiddleware()
            ->withNamespaceResolverMiddleware()
            ->withPropertyMapper($propertyMapper)
            ->build();

        $response = new GetResourcesApiResponse();
        $mapper->mapObject($json, $response);

        $organizationApiModel = $this->getOrganizationApiModel($response);
        $representationApiModels = $this->getRepresentationApiModels($response);

        return array_map(function (ResourceApiModel $model) use ($organizationApiModel, $representationApiModels) {
            return new ResourceModel($model, $organizationApiModel, $representationApiModels);
        }, $response->data);
    }

    private function getOrganizationApiModel(GetResourcesApiResponse $response): OrganizationApiModel
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

    /** @return Array<ResourceRepresentationApiModel> */
    private function getRepresentationApiModels(GetResourcesApiResponse $response): array
    {
        $mapper = (new JsonMapperFactory())->bestFit();

        return $mapper->mapArray(array_filter($response->included, function ($data) {
            return $data->type === ResourceRepresentationApiModel::TYPE;
        }), new ResourceRepresentationApiModel());
    }
}