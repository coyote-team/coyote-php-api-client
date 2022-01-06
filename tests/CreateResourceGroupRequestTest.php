<?php

namespace Tests;

use Coyote\InternalApiClient;
use Coyote\Model\ResourceGroupModel;
use Coyote\Payload\CreateResourceGroupPayload;
use Coyote\Request\CreateResourceGroupRequest;
use GuzzleHttp\Psr7\Response;
use stdClass;

/**
 * @covers \Coyote\Request\CreateResourceGroupRequest
 */
class CreateResourceGroupRequestTest extends AbstractTestCase
{
    private stdClass $contract;

    public function setUp(): void
    {
        $this->responses = [
            new Response(
                200,
                ['Content-Type' => 'application/json'],
                $this->getApiContractJson('createValidResourceGroup')
            )
        ];

        $this->contract = $this->getApiContract('createValidResourceGroup');

        parent::setUp();
    }

    /**
     * @param array|null $responses
     * @return ResourceGroupModel|null
     */
    private function doRequest(array $responses = null, string $uri = null): ?ResourceGroupModel
    {
        if (!is_null($responses)) {
            $this->setResponses($responses);
        }

        $client = new InternalApiClient('', '', null, $this->client);
        $payload = new CreateResourceGroupPayload('Testing Resource Group', $uri);

        return (new CreateResourceGroupRequest($client, $payload))->perform();
    }

    public function testInvalidResponseMapsToNull(): void
    {
        $response = $this->doRequest([new Response(404)]);
        $this->assertNull($response);
    }

    public function testValidResponseMapsToResourceGroupModel(): void
    {
        $response = $this->doRequest();
        $this->assertNotNull($response);
        $this->assertInstanceOf(ResourceGroupModel::class, $response);
    }

    public function testGroupNameIsCorrect(): void
    {
        $group = $this->doRequest();
        $this->assertEquals($group->getName(), $this->contract->data->attributes->name);
    }

    public function testGroupWebhookUriIsCorrect(): void
    {
        $contract = $this->getApiContract('createValidResourceGroupWithWebhookUri');

        $group = $this->doRequest([
            new Response(
                200,
                ['Content-Type' => 'application/json'],
                $this->getApiContractJson('createValidResourceGroupWithWebhookUri')
            )
        ], 'https://resource-groups-r-us.net/api');

        $this->assertEquals($group->getUri(), $contract->data->attributes->webhook_uri);
    }

    public function testGroupWebhookUriIsNullWhenNotProvided(): void
    {
        $group = $this->doRequest();
        $this->assertNull($group->getUri());
    }
}
