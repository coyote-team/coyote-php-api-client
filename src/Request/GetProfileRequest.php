<?php

namespace Coyote\Request;

use Coyote\ApiModel\MembershipApiModel;
use Coyote\ApiModel\OrganizationApiModel;
use Coyote\ApiModel\ProfileApiModel;
use Coyote\ApiResponse\GetProfileApiResponse;
use Coyote\InternalApiClient;
use Coyote\Model\ProfileModel;

use stdClass;

class GetProfileRequest extends AbstractApiRequest
{
    private const PATH = '/profile/';

    private InternalApiClient $client;

    public function __construct(InternalApiClient $client)
    {
        $this->client = $client;
    }

    /** @return ProfileModel|null */
    public function data(): ?ProfileModel
    {
        self::logDebug('Fetching profile');

        try {
            $json = $this->client->get(self::PATH);
        } catch (\Exception $error) {
            self::logError('Error fetching profile: ' . $error->getMessage());
            return null;
        }

        if (is_null($json)) {
            self::logWarning('Unexpected null response when fetching profile');
            return null;
        }

        return $this->mapResponseToProfileModel($json);
    }

    private function mapResponseToProfileModel(stdClass $json): ProfileModel
    {
        $response = self::mapper()->mapObject($json, (new GetProfileApiResponse()));

        $profileApiModel = $this->getProfileApiModel($response);
        $organizationApiModels = $this->getOrganizationApiModels($response);
        $membershipApiModels = $this->getMembershipApiModels($response);

        return new ProfileModel($profileApiModel, $organizationApiModels, $membershipApiModels);
    }

    private function getProfileApiModel(GetProfileApiResponse $response): ProfileApiModel
    {
        return $response->data;
    }

    /** @return MembershipApiModel[] */
    private function getMembershipApiModels(GetProfileApiResponse $response): array
    {
        $memberships = array_filter($response->included, function (stdClass $item) {
            return $item->type === MembershipApiModel::TYPE;
        });

        return array_map(function (stdClass $item): MembershipApiModel {
            return self::mapper()->mapObject($item, new MembershipApiModel());
        }, $memberships);
    }

    /** @return OrganizationApiModel[] */
    private function getOrganizationApiModels(GetProfileApiResponse $response): array
    {
        $organizations = array_filter($response->included, function (stdClass $item) {
            return $item->type === OrganizationApiModel::TYPE;
        });

        return array_map(function (stdClass $item): OrganizationApiModel {
            return self::mapper()->mapObject($item, new OrganizationApiModel());
        }, $organizations);
    }
}
