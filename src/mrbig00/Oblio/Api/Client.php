<?php
/**
 * @copyright   Copyright (c) 2019 Zoltan Szanto
 * @author      Zoltan Szanto
 * @license     MIT
 * @since       2019-08-21
 */

namespace mrbig00\Oblio\Api;

use Guzzle\Service\Loader\JsonLoader;
use GuzzleHttp\Command\CommandInterface;
use GuzzleHttp\Command\Guzzle\Description;
use GuzzleHttp\Command\Guzzle\GuzzleClient;
use GuzzleHttp\Command\ResultInterface;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use kamermans\OAuth2\OAuth2Middleware;
use kamermans\OAuth2\GrantType\ClientCredentials;
use mrbig00\Oblio\Api\Exceptions\OblioException;
use Psr\Http\Message\ResponseInterface;
use Traversable;

/**
 * Class Client
 *
 * @package mrbig00\Oblio\Api
 * @method ResultInterface getCompanies(array $args = []) Returns the list of companies associated with the Oblio
 *         account
 * @method ResultInterface getClients(array $args = [])  Returns the list of clients for a particular company
 * @method ResultInterface getProducts(array $args = []) Returns the product list for a particular company. For
 *         services, the management where it is located is not taken into account and therefore the "stock" heading
 *         does not appear.
 * @method ResultInterface getSeries(array $args = []) Returns the list of document series for a particular company
 * @method ResultInterface getLanguages(array $args = []) Returns the list of foreign languages for a particular
 *         company
 * @method ResultInterface getManagement(array $args = []) Returns the list of management for a particular company,
 *         works only if stocks are activated
 * @method ResultInterface getVatRates(array $args = []) Returns the list of VAT rates for a particular company
 * @method ResultInterface addProforma(array $args = [])
 * @method ResultInterface addNotice(array $args = [])
 * @method ResultInterface addInvoice(array $args = [])
 * @method ResultInterface getInvoices(array $args = [])
 * @method ResultInterface getProformas(array $args = [])
 * @method ResultInterface getNotices(array $args = [])
 * @method ResultInterface cancelInvoice(array $args = [])
 * @method ResultInterface cancelProforma(array $args = [])
 * @method ResultInterface cancelNotice(array $args = [])
 * @method ResultInterface restoreInvoice(array $args = [])
 * @method ResultInterface restoreProforma(array $args = [])
 * @method ResultInterface restoreNotice(array $args = [])
 */
class Client
{
    /**
     * @var \GuzzleHttp\Client
     */
    public $client;
    /**
     * @var GuzzleClient
     */
    public $guzzleClient;

    public function __construct(string $clientId, string $clientSecret)
    {
        $this->initClient($clientId, $clientSecret);
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

        $stack->push(Middleware::mapResponse(function (ResponseInterface $response) {
            $responseBody = json_decode($response->getBody()->getContents(), true);
            if ($response->getStatusCode() !== 200) {
                $errorMessage = isset($responseBody['statusMessage']) ?
                    $responseBody['statusMessage'] : null;
                throw new OblioException($errorMessage);
            }
            return $response;
        }));

        $this->client = new \GuzzleHttp\Client([
            'handler' => $stack,
            'auth' => 'oauth',
            'base_uri' => 'https://www.oblio.eu/api/',
        ]);

        $description = new Description(json_decode(file_get_contents(__DIR__ . '/service_description.json'), true));

        $this->guzzleClient = new GuzzleClient($this->client, $description);
    }

    /**
     * Directly call a specific endpoint by creating the command and executing it
     *
     * Using __call magic methods is equivalent to creating and executing a single command.
     * It also supports using optimized iterator requests by adding "Iterator" suffix to the command
     *
     * @param string $method
     * @param array  $args
     *
     * @return ResponseInterface|ResultInterface|Traversable
     */
    public function __call(string $method, array $args = [])
    {
        $params = $args[0] ?? [];
        return $this->guzzleClient->$method($params);
    }
}
