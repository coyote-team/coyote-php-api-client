<?php

namespace Coyote;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use stdClass;

class InternalApiClient
{
    public const INCLUDE_ORG_ID = 'includeOrganizationId';

    private const METHOD_GET = 'GET';
    private const METHOD_POST = 'POST';
    private const METHOD_PUT = 'PUT';

    private Client $client;

    private string $endpoint;
    private string $token;
    private int $organizationId;
    private string $locale = 'en';

    public function __construct(string $endpoint, string $token, int $organizationId)
    {
        $this->endpoint = $endpoint;
        $this->token = $token;
        $this->organizationId = $organizationId;

        $this->client = new Client();
    }

    /**
     * @param array<mixed> $options
     *
     * @throws \Exception
     *
     * @return null|\stdClass
     */
    public function get(string $url, array $options = []): ?stdClass
    {
        $includeOrganizationId = array_key_exists(self::INCLUDE_ORG_ID, $options)
            ? $options[self::INCLUDE_ORG_ID]
            : false;

        return self::request(
            $this->makeUrl($url, $includeOrganizationId),
            array_merge($options, ['method' => self::METHOD_GET])
        );
    }

    /**
     * @param array<mixed> $payload
     * @param array<mixed> $options
     *
     * @throws \Exception
     *
     * @return null|stdClass
     */
    public function post(string $url, array $payload, array $options = []): ?stdClass
    {
        return self::request(
            $this->makeUrl($url),
            array_merge($options, ['method' => self::METHOD_POST], ['json' => $payload])
        );
    }

    /**
     * @param array<mixed> $payload
     * @param array<mixed> $options
     *
     * @throws \Exception
     *
     * @return null|array<mixed>
     */
    public function put(string $url, array $payload, array $options = []): ?array
    {
        return self::request(
            $this->makeUrl($url),
            array_merge($options, ['method' => self::METHOD_PUT], ['json' => $payload])
        );
    }

    private function makeUrl(string $part, $includeOrganizationId = false): string
    {
        return $includeOrganizationId
            ? sprintf('%s/organizations/%d/%s', $this->endpoint, $this->organizationId, $part)
            : sprintf('%s/%s', $this->endpoint, $part);
    }

    /**
     * @param array<mixed> $options
     *
     * @throws \Exception
     *
     * @return null|array<mixed>
     */
    private function request(string $url, array $options = []): ?stdClass
    {
        $options = array_merge(
            $options,
            ['headers' => $this->getRequestHeaders($options)],
            ['http_errors' => false],
        );

        switch ($options['method']) {
            case self::METHOD_GET:
                $response = $this->client->get($url, $options);

                break;

            case self::METHOD_POST:
                $response = $this->client->post($url, $options);

                break;

            default:
                throw new \Exception('Invalid Coyote API request');
        }

        $body = (string) $response->getBody();

        if ($this->isResponseOk($response)) {
            return json_decode($body);
        }

        $status = $response->getStatusCode();

        throw new \Exception("Invalid Coyote API response for {$url}");
    }

    /**
     * @param array<mixed> $options
     *
     * @return array<mixed>
     */
    private function getRequestHeaders(array $options = []): array
    {
        $headers = [
            'Authorization' => $this->token,
            'Accept-Language' => $this->locale,
            'Content-Type' => 'application/json',
        ];

        return $headers;
    }

    private function isResponseOk(ResponseInterface $response): bool
    {
        return $response->getStatusCode() >= 200 && $response->getStatusCode() < 400;
    }

}