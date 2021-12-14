<?php

namespace Tests;

use Coyote\InternalApiClient;
use Coyote\Model\ProfileModel;
use Coyote\Model\ResourceGroupModel;
use Coyote\Request\GetProfileRequest;
use Coyote\Request\GetResourceGroupsRequest;
use GuzzleHttp\Psr7\Response;
use stdClass;

/**
 * @covers \Coyote\Request\GetResourceGroupsRequest
 */
class GetResourceGroupsRequestTest extends AbstractTestCase
{
    private stdClass $contract;

    public function setUp(): void
    {
        $this->responses = [
            new Response(
                200,
                ['Content-Type' => 'application/json'],
                $this->getApiContractJson('getValidResourceGroups')
            )
        ];

        $this->contract = $this->getApiContract('getValidResourceGroups');

        parent::setUp();
    }

    public function testInvalidResponseMapsToNull(): void
    {
        $this->setResponses([
            new Response(404)
        ]);

        $client = new InternalApiClient('', '', null, $this->client);
        $response = (new GetResourceGroupsRequest($client))->data();

        $this->assertNull($response);
    }

    public function testValidResponseMapsToResourceGroupsModels(): void
    {
        $client = new InternalApiClient('', '', null, $this->client);
        $response = (new GetResourceGroupsRequest($client))->data();

        $this->assertNotNull($response);
        $this->assertIsArray($response);
        $this->assertCount(count($this->contract->data), $response);

        foreach ($response as $model) {
            $this->assertInstanceOf(ResourceGroupModel::class, $model);
        }
    }

    public function testGroupNameIsAvailable(): void
    {
        $client = new InternalApiClient('', '', null, $this->client);
        $response = (new GetResourceGroupsRequest($client))->data();
        $group = array_shift($response);

        $this->assertEquals($group->getName(), $this->contract->data[0]->attributes->name);
    }

    public function testGroupIdIsAvailable(): void
    {
        $client = new InternalApiClient('', '', null, $this->client);
        $response = (new GetResourceGroupsRequest($client))->data();
        $group = array_shift($response);

        $this->assertEquals($group->getId(), $this->contract->data[0]->id);
    }

    public function testGroupUriIsAvailable(): void
    {
        $client = new InternalApiClient('', '', null, $this->client);
        $response = (new GetResourceGroupsRequest($client))->data();
        $group = array_shift($response);

        $this->assertEquals($group->getUri(), $this->contract->data[0]->attributes->webhook_uri);
    }

    public function testGroupDefaultSettingIsAvailable(): void
    {
        $client = new InternalApiClient('', '', null, $this->client);
        $response = (new GetResourceGroupsRequest($client))->data();
        $group = array_shift($response);

        $this->assertEquals($group->isDefault(), $this->contract->data[0]->attributes->default);
    }
}
