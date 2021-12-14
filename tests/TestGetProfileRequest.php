<?php

namespace Tests;

use Coyote\InternalApiClient;
use Coyote\Model\ProfileModel;
use Coyote\Request\GetProfileRequest;
use GuzzleHttp\Psr7\Response;
use stdClass;

/**
 * @covers \Coyote\Request\GetProfileRequest
 */
class TestGetProfileRequest extends AbstractTestCase
{
    private stdClass $contract;

    public function setUp(): void
    {
        $this->responses = [
            new Response(200, ['Content-Type' => 'application/json'], $this->getApiContractJson('getValidProfile'))
        ];

        $this->contract = $this->getApiContract('getValidProfile');

        parent::setUp();
    }

    public function testInValidResponseMapsToNull(): void
    {
        $this->setResponses([
            new Response(404)
        ]);

        $client = new InternalApiClient('', '', null, $this->client);
        $response = (new GetProfileRequest($client))->data();

        $this->assertNull($response);
    }

    public function testValidResponseMapsToProfileModel(): void
    {
        $client = new InternalApiClient('', '', null, $this->client);
        $response = (new GetProfileRequest($client))->data();

        $this->assertNotNull($response);
        $this->assertInstanceOf(ProfileModel::class, $response);
    }

    public function testProfileIdIsAvailable(): void
    {
        $client = new InternalApiClient('', '', null, $this->client);
        $response = (new GetProfileRequest($client))->data();

        $this->assertEquals(
            $response->getId(),
            $this->contract->data->id
        );
    }


    public function testProfileNameIsAvailable(): void
    {
        $client = new InternalApiClient('', '', null, $this->client);
        $response = (new GetProfileRequest($client))->data();

        $this->assertEquals(
            $response->getName(),
            implode(' ', [
                $this->contract->data->attributes->first_name,
                $this->contract->data->attributes->last_name])
        );
    }

    public function testOrganizationsAreMapped(): void
    {
        $client = new InternalApiClient('', '', null, $this->client);
        $response = (new GetProfileRequest($client))->data();

        $this->assertIsArray($response->getOrganizations());
        $this->assertCount(count($this->contract->included), $response->getOrganizations());

        $this->assertEquals(
            $response->getOrganizations()[0]->getName(),
            $this->contract->included[0]->attributes->name
        );
    }
}
