<?php
/**
 * @copyright   Copyright (c) 2019 Zoltan Szanto
 * @author      Zoltan Szanto
 * @license     MIT
 * @since       2019-08-21
 */

namespace mrbig00\Oblio\Api;

use GuzzleHttp\Command\CommandInterface;
use GuzzleHttp\Command\Guzzle\Description;
use GuzzleHttp\Command\Guzzle\GuzzleClient;
use GuzzleHttp\Command\ResultInterface;
use GuzzleHttp\HandlerStack;
use kamermans\OAuth2\OAuth2Middleware;
use kamermans\OAuth2\GrantType\ClientCredentials;
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


        $description = new Description([
            'baseUri' => 'https://www.oblio.eu/api/',
            'operations' => [
                'getCompanies' => [
                    'httpMethod' => 'GET',
                    'uri' => '/api/nomenclature/companies',
                    'responseModel' => 'getResponse',
                ],
                'getClients' => [
                    'httpMethod' => 'GET',
                    'uri' => '/api/nomenclature/clients',
                    'responseModel' => 'getResponse',
                    'parameters' => [
                        'cif' => [
                            'location' => 'query',
                            'type' => 'string',
                            'required' => true,
                        ],
                        'name' => [
                            'location' => 'query',
                            'type' => 'string',
                            'required' => false,
                        ],
                        'offset' => [
                            'location' => 'query',
                            'type' => 'integer',
                            'required' => false,
                        ],
                    ],
                ],
                'getProducts' => [
                    'httpMethod' => 'GET',
                    'uri' => '/api/nomenclature/products',
                    'responseModel' => 'getResponse',
                    'parameters' => [
                        'cif' => [
                            'location' => 'query',
                            'type' => 'string',
                            'required' => true,
                        ],
                        'name' => [
                            'location' => 'query',
                            'type' => 'string',
                            'required' => false,
                        ],
                        'code' => [
                            'location' => 'query',
                            'type' => 'string',
                            'required' => false,
                        ],
                        'management' => [
                            'location' => 'query',
                            'type' => 'string',
                            'required' => false,
                        ],
                        'workStation' => [
                            'location' => 'query',
                            'type' => 'string',
                            'required' => false,
                        ],
                        'offset' => [
                            'location' => 'query',
                            'type' => 'integer',
                            'required' => false,
                        ],
                    ],
                ],
                'getSeries' => [
                    'httpMethod' => 'GET',
                    'uri' => '/api/nomenclature/series',
                    'responseModel' => 'getResponse',
                    'parameters' => [
                        'cif' => [
                            'location' => 'query',
                            'type' => 'string',
                            'required' => true,
                        ],
                    ],
                ],
                'getLanguages' => [
                    'httpMethod' => 'GET',
                    'uri' => '/api/nomenclature/languages',
                    'responseModel' => 'getResponse',
                    'parameters' => [
                        'cif' => [
                            'location' => 'query',
                            'type' => 'string',
                            'required' => true,
                        ],
                    ],
                ],
                'getManagement' => [
                    'httpMethod' => 'GET',
                    'uri' => '/api/nomenclature/management',
                    'responseModel' => 'getResponse',
                    'parameters' => [
                        'cif' => [
                            'location' => 'query',
                            'type' => 'string',
                            'required' => true,
                        ],
                    ],
                ],
                'getVatRates' => [
                    'httpMethod' => 'GET',
                    'uri' => '/api/nomenclature/vat_rates',
                    'responseModel' => 'getResponse',
                    'parameters' => [
                        'cif' => [
                            'location' => 'query',
                            'type' => 'string',
                            'required' => true,
                        ],
                    ],
                ],
                'addProforma' => [
                    'httpMethod' => 'POST',
                    'uri' => '/api/docs/proforma',
                    'responseModel' => 'getResponse',
                    'parameters' => [
                        'cif' => [
                            'location' => 'json',
                            'type' => 'string',
                            'required' => true,
                        ],
                        'client' => [
                            'location' => 'json',
                            'type' => 'object',
                            'required' => true,
                        ],
                        'issueDate' => [
                            'location' => 'json',
                            'type' => 'string',
                            'required' => false,
                        ],
                        'dueDate' => [
                            'location' => 'json',
                            'type' => 'string',
                            'required' => false,
                        ],
                        'seriesName' => [
                            'location' => 'json',
                            'type' => 'string',
                            'required' => true,
                        ],
                        'language' => [
                            'location' => 'json',
                            'type' => 'string',
                            'required' => false,
                        ],
                        'precision' => [
                            'location' => 'json',
                            'type' => 'integer',
                            'required' => false,
                        ],
                        'currency' => [
                            'location' => 'json',
                            'type' => 'string',
                            'required' => false,
                        ],
                        'exchangeRate' => [
                            'location' => 'json',
                            'type' => 'double',
                            'required' => false,
                        ],
                        'products' => [
                            'location' => 'json',
                            'type' => 'any',
                            'required' => true,
                        ],
                        'issuerName' => [
                            'location' => 'json',
                            'type' => 'string',
                            'required' => false,
                        ],
                        'issuerId' => [
                            'location' => 'json',
                            'type' => 'string',
                            'required' => false,
                        ],
                        'noticeNumber' => [
                            'location' => 'json',
                            'type' => 'string',
                            'required' => false,
                        ],
                        'internalNote' => [
                            'location' => 'json',
                            'type' => 'string',
                            'required' => false,
                        ],
                        'deputyName' => [
                            'location' => 'json',
                            'type' => 'string',
                            'required' => false,
                        ],
                        'deputyIdentityCard' => [
                            'location' => 'json',
                            'type' => 'string',
                            'required' => false,
                        ],
                        'deputyAuto' => [
                            'location' => 'json',
                            'type' => 'string',
                            'required' => false,
                        ],
                        'selesAgent' => [
                            'location' => 'json',
                            'type' => 'string',
                            'required' => false,
                        ],
                        'mentions' => [
                            'location' => 'json',
                            'type' => 'string',
                            'required' => false,
                        ],
                        'workStation' => [
                            'location' => 'json',
                            'type' => 'string',
                            'required' => false,
                        ],
                    ],
                ],
                'addNotice' => [
                    'httpMethod' => 'POST',
                    'uri' => '/api/docs/proforma',
                    'responseModel' => 'getResponse',
                    'parameters' => [
                        'cif' => [
                            'location' => 'json',
                            'type' => 'string',
                            'required' => true,
                        ],
                        'client' => [
                            'location' => 'json',
                            'type' => 'object',
                            'required' => true,
                        ],
                        'issueDate' => [
                            'location' => 'json',
                            'type' => 'string',
                            'required' => false,
                        ],
                        'dueDate' => [
                            'location' => 'json',
                            'type' => 'string',
                            'required' => false,
                        ],
                        'seriesName' => [
                            'location' => 'json',
                            'type' => 'string',
                            'required' => true,
                        ],
                        'language' => [
                            'location' => 'json',
                            'type' => 'string',
                            'required' => false,
                        ],
                        'precision' => [
                            'location' => 'json',
                            'type' => 'integer',
                            'required' => false,
                        ],
                        'currency' => [
                            'location' => 'json',
                            'type' => 'string',
                            'required' => false,
                        ],
                        'exchangeRate' => [
                            'location' => 'json',
                            'type' => 'double',
                            'required' => false,
                        ],
                        'products' => [
                            'location' => 'json',
                            'type' => 'any',
                            'required' => true,
                        ],
                        'issuerName' => [
                            'location' => 'json',
                            'type' => 'string',
                            'required' => false,
                        ],
                        'issuerId' => [
                            'location' => 'json',
                            'type' => 'string',
                            'required' => false,
                        ],
                        'internalNote' => [
                            'location' => 'json',
                            'type' => 'string',
                            'required' => false,
                        ],
                        'useStock' => [
                            'location' => 'json',
                            'type' => 'integer',
                            'required' => false,
                        ],
                        'deputyName' => [
                            'location' => 'json',
                            'type' => 'string',
                            'required' => false,
                        ],
                        'deputyIdentityCard' => [
                            'location' => 'json',
                            'type' => 'string',
                            'required' => false,
                        ],
                        'deputyAuto' => [
                            'location' => 'json',
                            'type' => 'string',
                            'required' => false,
                        ],
                        'selesAgent' => [
                            'location' => 'json',
                            'type' => 'string',
                            'required' => false,
                        ],
                        'mentions' => [
                            'location' => 'json',
                            'type' => 'string',
                            'required' => false,
                        ],
                        'workStation' => [
                            'location' => 'json',
                            'type' => 'string',
                            'required' => false,
                        ],
                    ],
                ],
                'addInvoice' => [
                    'httpMethod' => 'POST',
                    'uri' => '/api/docs/invoice',
                    'responseModel' => 'getResponse',
                    'parameters' => [
                        'cif' => [
                            'location' => 'json',
                            'type' => 'string',
                            'required' => true,
                        ],
                        'client' => [
                            'location' => 'json',
                            'type' => 'object',
                            'required' => true,
                        ],
                        'issueDate' => [
                            'location' => 'json',
                            'type' => 'string',
                            'required' => false,
                        ],
                        'dueDate' => [
                            'location' => 'json',
                            'type' => 'string',
                            'required' => false,
                        ],
                        'deliveryDate' => [
                            'location' => 'json',
                            'type' => 'string',
                            'required' => false,
                        ],
                        'collectDate' => [
                            'location' => 'json',
                            'type' => 'string',
                            'required' => false,
                        ],
                        'referenceDocument' => [
                            'location' => 'json',
                            'type' => 'object',
                            'required' => false,
                        ],
                        'collect' => [
                            'location' => 'json',
                            'type' => 'object',
                            'required' => false,
                        ],
                        'useStock' => [
                            'location' => 'json',
                            'type' => 'integer',
                            'required' => false,
                        ],
                        'seriesName' => [
                            'location' => 'json',
                            'type' => 'string',
                            'required' => true,
                        ],
                        'language' => [
                            'location' => 'json',
                            'type' => 'string',
                            'required' => false,
                        ],
                        'precision' => [
                            'location' => 'json',
                            'type' => 'integer',
                            'required' => false,
                        ],
                        'currency' => [
                            'location' => 'json',
                            'type' => 'string',
                            'required' => false,
                        ],
                        'exchangeRate' => [
                            'location' => 'json',
                            'type' => 'double',
                            'required' => false,
                        ],
                        'products' => [
                            'location' => 'json',
                            'type' => 'any',
                            'required' => true,
                        ],
                        'issuerName' => [
                            'location' => 'json',
                            'type' => 'string',
                            'required' => false,
                        ],
                        'issuerId' => [
                            'location' => 'json',
                            'type' => 'string',
                            'required' => false,
                        ],
                        'noticeNumber' => [
                            'location' => 'json',
                            'type' => 'string',
                            'required' => false,
                        ],
                        'internalNote' => [
                            'location' => 'json',
                            'type' => 'string',
                            'required' => false,
                        ],
                        'deputyName' => [
                            'location' => 'json',
                            'type' => 'string',
                            'required' => false,
                        ],
                        'deputyIdentityCard' => [
                            'location' => 'json',
                            'type' => 'string',
                            'required' => false,
                        ],
                        'deputyAuto' => [
                            'location' => 'json',
                            'type' => 'string',
                            'required' => false,
                        ],
                        'selesAgent' => [
                            'location' => 'json',
                            'type' => 'string',
                            'required' => false,
                        ],
                        'mentions' => [
                            'location' => 'json',
                            'type' => 'string',
                            'required' => false,
                        ],
                        'workStation' => [
                            'location' => 'json',
                            'type' => 'string',
                            'required' => false,
                        ],
                    ],
                ],
                'getInvoices' => [
                    'httpMethod' => 'GET',
                    'uri' => '/api/docs/invoice',
                    'responseModel' => 'getResponse',
                    'parameters' => [
                        'cif' => [
                            'location' => 'query',
                            'type' => 'string',
                            'required' => true,
                        ],
                        'seriesName' => [
                            'location' => 'query',
                            'type' => 'string',
                            'required' => true,
                        ],
                        'number' => [
                            'location' => 'query',
                            'type' => 'string',
                            'required' => true,
                        ],
                    ],
                ],
                'getProformas' => [
                    'httpMethod' => 'GET',
                    'uri' => '/api/docs/proforma',
                    'responseModel' => 'getResponse',
                    'parameters' => [
                        'cif' => [
                            'location' => 'query',
                            'type' => 'string',
                            'required' => true,
                        ],
                        'seriesName' => [
                            'location' => 'query',
                            'type' => 'string',
                            'required' => true,
                        ],
                        'number' => [
                            'location' => 'query',
                            'type' => 'string',
                            'required' => true,
                        ],
                    ],
                ],
                'getNotices' => [
                    'httpMethod' => 'GET',
                    'uri' => '/api/docs/notice',
                    'responseModel' => 'getResponse',
                    'parameters' => [
                        'cif' => [
                            'location' => 'query',
                            'type' => 'string',
                            'required' => true,
                        ],
                        'seriesName' => [
                            'location' => 'query',
                            'type' => 'string',
                            'required' => true,
                        ],
                        'number' => [
                            'location' => 'query',
                            'type' => 'string',
                            'required' => true,
                        ],
                    ],
                ],
                'cancelInvoice' => [
                    'httpMethod' => 'PUT',
                    'uri' => '/api/docs/invoice/cancel',
                    'responseModel' => 'getResponse',
                    'parameters' => [
                        'cif' => [
                            'location' => 'query',
                            'type' => 'string',
                            'required' => true,
                        ],
                        'seriesName' => [
                            'location' => 'query',
                            'type' => 'string',
                            'required' => true,
                        ],
                        'number' => [
                            'location' => 'query',
                            'type' => 'string',
                            'required' => true,
                        ],
                    ],
                ],
                'cancelProforma' => [
                    'httpMethod' => 'PUT',
                    'uri' => '/api/docs/proforma/cancel',
                    'responseModel' => 'getResponse',
                    'parameters' => [
                        'cif' => [
                            'location' => 'query',
                            'type' => 'string',
                            'required' => true,
                        ],
                        'seriesName' => [
                            'location' => 'query',
                            'type' => 'string',
                            'required' => true,
                        ],
                        'number' => [
                            'location' => 'query',
                            'type' => 'string',
                            'required' => true,
                        ],
                    ],
                ],
                'cancelNotice' => [
                    'httpMethod' => 'PUT',
                    'uri' => '/api/docs/notice/cancel',
                    'responseModel' => 'getResponse',
                    'parameters' => [
                        'cif' => [
                            'location' => 'query',
                            'type' => 'string',
                            'required' => true,
                        ],
                        'seriesName' => [
                            'location' => 'query',
                            'type' => 'string',
                            'required' => true,
                        ],
                        'number' => [
                            'location' => 'query',
                            'type' => 'string',
                            'required' => true,
                        ],
                    ],
                ],
                'restoreInvoice' => [
                    'httpMethod' => 'PUT',
                    'uri' => '/api/docs/invoice/restore',
                    'responseModel' => 'getResponse',
                    'parameters' => [
                        'cif' => [
                            'location' => 'query',
                            'type' => 'string',
                            'required' => true,
                        ],
                        'seriesName' => [
                            'location' => 'query',
                            'type' => 'string',
                            'required' => true,
                        ],
                        'number' => [
                            'location' => 'query',
                            'type' => 'string',
                            'required' => true,
                        ],
                    ],
                ],
                'restoreProforma' => [
                    'httpMethod' => 'PUT',
                    'uri' => '/api/docs/proforma/restore',
                    'responseModel' => 'getResponse',
                    'parameters' => [
                        'cif' => [
                            'location' => 'query',
                            'type' => 'string',
                            'required' => true,
                        ],
                        'seriesName' => [
                            'location' => 'query',
                            'type' => 'string',
                            'required' => true,
                        ],
                        'number' => [
                            'location' => 'query',
                            'type' => 'string',
                            'required' => true,
                        ],
                    ],
                ],
                'restoreNotice' => [
                    'httpMethod' => 'PUT',
                    'uri' => '/api/docs/notice/restore',
                    'responseModel' => 'getResponse',
                    'parameters' => [
                        'cif' => [
                            'location' => 'query',
                            'type' => 'string',
                            'required' => true,
                        ],
                        'seriesName' => [
                            'location' => 'query',
                            'type' => 'string',
                            'required' => true,
                        ],
                        'number' => [
                            'location' => 'query',
                            'type' => 'string',
                            'required' => true,
                        ],
                    ],
                ],
            ],
            'models' => [
                'getResponse' => [
                    'type' => 'object',
                    'additionalProperties' => [
                        'location' => 'json',
                    ],
                ],
            ],
        ]);

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