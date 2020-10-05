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

$client->getCompanies(['cif' => 'YOUR_CIF']);
$client->getVatRates(['cif' => 'YOUR_CIF']);

```

## Available methods

- getClients
- getProducts
- getSeries
- getLanguages
- getManagement
- getVatRates
- addProforma
- addNotice
- addInvoice
- getInvoice
- getProforma
- getNotice
- cancelInvoice
- cancelProforma
- cancelNotice
- deleteInvoice
- deleteProforma
- deleteNotice
- restoreInvoice
- restoreProforma
- restoreNotice

## Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

Please make sure to update tests as appropriate.

## License
[MIT](https://choosealicense.com/licenses/mit/)
