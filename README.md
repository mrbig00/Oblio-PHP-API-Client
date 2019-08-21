# Oblio PHP API Client

Super-simple, minimum abstraction Oblio API wrapper, in PHP

## Installation

Use the package manager [composer](https://getcomposer.org/) to install the Oblio API Wrapper.

```bash
composer require mrbig00/oblio-php-api-client
```

## Usage

```php
$client = new \mrbig00\Oblio\Api\Client(
    'batman@superman.com',
    'clientSecret'
);

$client->nomenclator->getCompanies('YOUR_CIF');
$client->nomenclator->getVatRates('YOUR_CIF');
$client->nomenclator->getClients('YOUR_CIF');
$client->nomenclator->getProducts('YOUR_CIF');
$client->nomenclator->getSeries('YOUR_CIF');
$client->nomenclator->getLanguages('YOUR_CIF');
$client->nomenclator->getManagements('YOUR_CIF');
```

## Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

Please make sure to update tests as appropriate.

## License
[MIT](https://choosealicense.com/licenses/mit/)