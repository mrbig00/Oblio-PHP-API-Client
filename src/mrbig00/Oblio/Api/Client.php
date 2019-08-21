<?php
/**
 * @copyright   Copyright (c) 2019 Zoltan Szanto
 * @author      Zoltan Szanto
 * @license     MIT
 * @since       2019-08-21
 */

namespace mrbig00\Oblio\Api;

use GuzzleHttp\HandlerStack;
use kamermans\OAuth2\OAuth2Middleware;
use kamermans\OAuth2\GrantType\ClientCredentials;

/**
 * Class Client
 *
 * @package mrbig00\Oblio\Api
 */
class Client
{
    /**
     * @var \GuzzleHttp\Client
     */
    public $client;
    public $nomenclator;

    public function __construct(string $clientId, string $clientSecret)
    {
        $this->initClient($clientId, $clientSecret);
        $this->nomenclator = new Nomenclator($this);
    }

    protected function initClient(string $clientId, string $clientSecret)
    {
        $reAuthClient = new \GuzzleHttp\Client([
            'base_uri' => 'https://www.oblio.eu/api/authorize/token',
        ]);

        $grant_type = new ClientCredentials(
            $reAuthClient,
            [
                "client_id" => $clientId,
                "client_secret" => $clientSecret,
            ]
        );

        $oauth = new OAuth2Middleware($grant_type);

        $stack = HandlerStack::create();
        $stack->push($oauth);

        $this->client = new \GuzzleHttp\Client([
            'handler' => $stack,
            'auth' => 'oauth',
            'base_uri' => 'https://www.oblio.eu/api/',
        ]);
    }

    public function get(string $uri, array $parameters)
    {
        return $this->client->request('GET', ltrim($uri, '/'), ['query' => $parameters]);
    }
}