<?php

namespace Coyote\Request;

use Coyote\ApiModel\OrganizationApiModel;
use Coyote\ApiModel\ProfileApiModel;
use Coyote\ApiResponse\GetProfileApiResponse;
use Coyote\InternalApiClient;
use Coyote\Model\ProfileModel;
use Coyote\RequestLogger;
use JsonMapper\JsonMapperFactory;
use Monolog\Logger;
use stdClass;

class GetProfileRequest
{
    private const PATH = '/profile/';

    private InternalApiClient $client;
    private RequestLogger $logger;

    public function __construct(InternalApiClient $client, int $logLevel = Logger::INFO)
    {
        $this->client = $client;
        $this->logger = new RequestLogger('GetProfileRequest', $logLevel);
    }

    /** @return ProfileModel|null */
    public function data(): ?ProfileModel
    {
        $this->logger->debug('Fetching profile');

        try {
            $json = $this->client->get(self::PATH);
        } catch (\Exception $error) {
            $this->logger->error('Error fetching profile: ' . $error->getMessage());
            return null;
        }

        if (is_null($json)) {
            $this->logger->warn('Unexpected null response when fetching profile');
            return null;
        }

        return $this->mapResponseToProfileModel($json);
    }

    private function mapResponseToProfileModel(stdClass $json): ProfileModel
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
