<?php
/**
 * @copyright   Copyright (c) 2019 Zoltan Szanto
 * @author      Zoltan Szanto
 * @license     MIT
 * @since       2019-08-21
 */

namespace mrbig00\Oblio\Api;

/**
 * Class Nomenclator
 *
 * @package mrbig00\Oblio\Api
 */
class Nomenclator
{
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function getCompanies(string $cif)
    {
        return $this->client->get(
            '/nomenclature/companies',
            [
                'cif' => $cif,
            ]
        )
            ->getBody();
    }

    public function getVatRates(string $cif)
    {
        return $this->client->get(
            '/nomenclature/vat_rates',
            [
                'cif' => $cif,
            ]
        )
            ->getBody();
    }

    public function getClients(string $cif, string $name = null, int $offset = 0)
    {
        $params = ['cif' => $cif];
        if ($name) {
            $params['name'] = $name;
        }
        $params['offset'] = $offset;

        return $this->client->get(
            '/nomenclature/clients',
            [
                'cif' => $cif,
            ]
        )
            ->getBody();
    }

    public function getProducts(string $cif, string $name = null, string $code = null, string $management = null, string $workStation = null, int $offset = 0)
    {
        $params = ['cif' => $cif];
        if ($name) {
            $params['name'] = $name;
        }

        if ($code) {
            $params['code'] = $code;
        }

        if ($management) {
            $params['management'] = $management;
        }

        if ($workStation) {
            $params['workStation'] = $workStation;
        }

        $params['offset'] = $offset;

        return $this->client->get(
            '/nomenclature/products',
            $params
        )
            ->getBody();
    }

    public function getSeries(string $cif)
    {
        return $this->client->get(
            '/nomenclature/series',
            [
                'cif' => $cif,
            ]
        )
            ->getBody();
    }

    public function getLanguages(string $cif)
    {
        return $this->client->get(
            '/nomenclature/languages',
            [
                'cif' => $cif,
            ]
        )
            ->getBody();
    }

    public function getManagements(string $cif)
    {
        return $this->client->get(
            '/nomenclature/management',
            [
                'cif' => $cif,
            ]
        )
            ->getBody();
    }
}
