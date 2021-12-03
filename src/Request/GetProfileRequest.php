<?php

namespace Coyote\Request;

use Coyote\ApiModel\OrganizationApiModel;
use Coyote\ApiModel\ProfileApiModel;
use Coyote\ApiResponse\GetProfileApiResponse;
use Coyote\InternalApiClient;
use Coyote\Model\ProfileModel;
use JsonMapper\JsonMapperFactory;
use stdClass;

class GetProfileRequest
{
    private const PATH = '/profile/';

    private InternalApiClient $client;

    public function __construct(InternalApiClient $client)
    {
        $this->client = $client;
    }

    /** @return ProfileApiModel|null */
    public function data(): ?ProfileModel {
        $json = $this->client->get(self::PATH);

        if (is_null($json)) {
            return null;
        }

        return $this->mapResponseToProfileModel($json);
    }

    /** @return ProfileModel|null */
    private function mapResponseToProfileModel(stdClass $json): ?ProfileModel
    {
        $mapper = (new JsonMapperFactory())->bestFit();
        $response = new GetProfileApiResponse();
        $mapper->mapObject($json, $response);

        $profileApiModel = $this->getProfileApiModel($response);
        $organizationApiModels = $this->getOrganizationApiModels($response);

        return new ProfileModel($profileApiModel, $organizationApiModels);
    }

    private function getProfileApiModel(GetProfileApiResponse $response): ProfileApiModel
    {
        return $response->data;
    }

    /** @return OrganizationApiModel[] */
    private function getOrganizationApiModels(GetProfileApiResponse $response): array
    {
        return $response->included;
    }
}