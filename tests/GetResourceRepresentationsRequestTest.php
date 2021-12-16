<?php

namespace Tests;

use Coyote\InternalApiClient;
use Coyote\Model\ProfileModel;
use Coyote\Request\GetProfileRequest;
use GuzzleHttp\Psr7\Response;
use stdClass;

/**
 * @covers \Coyote\Request\GetResourceRepresentationsRequest
 */
class GetResourceRepresentationsRequestTest extends AbstractTestCase
{
    private stdClass $contract;

    public function setUp(): void
    {
        $this->responses = [
            new Response(
                200,
                ['Content-Type' => 'application/json'],
                $this->getApiContractJson('getValidResourceRepresentations')
            )
        ];

        $this->contract = $this->getApiContract('getValidResourceRepresentations');

        parent::setUp();
    }

    private function doRequest(?array $responses = null): ?ProfileModel
    {
        if (!is_null($responses)) {
            $this->setResponses($responses);
        }

        $client = new InternalApiClient('', '', null, $this->client);
        return (new GetProfileRequest($client))->data();
    }

    public function testInvalidResponseMapsToNull(): void
    {
        $response = $this->doRequest([new Response(404)]);
        $this->assertNull($response);
    }

    public function testValidResponseMapsToProfileModel(): void
    {
        $response = $this->doRequest();
        $this->assertNotNull($response);
        $this->assertInstanceOf(ProfileModel::class, $response);
    }

    public function testProfileIdIsAvailable(): void
    {
        $response = $this->doRequest();
        $this->assertEquals(
            $response->getId(),
            $this->contract->data->id
        );
    }

    public function testProfileNameIsAvailable(): void
    {
        $response = $this->doRequest();
        $this->assertEquals(
            $response->getName(),
            implode(' ', [
                $this->contract->data->attributes->first_name,
                $this->contract->data->attributes->last_name])
        );
    }

    public function testOrganizationsAreMapped(): void
    {
        $response = $this->doRequest();

        $this->assertIsArray($response->getOrganizations());
        $this->assertCount(count($this->contract->included), $response->getOrganizations());

        $this->assertEquals(
            $response->getOrganizations()[0]->getName(),
            $this->contract->included[0]->attributes->name
        );
    }
}
