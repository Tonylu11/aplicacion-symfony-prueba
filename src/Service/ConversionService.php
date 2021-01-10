<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class ConversionService
{
    private $client;
    
    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function fetchCurrencyConversion($currency, $productPrice, $productCurrency)
    {
        $response = $this->client->request(
            'GET',
            'https://api.exchangeratesapi.io/latest?base=' . $productCurrency . '&symbols=' . $currency
        );

        $statusCode = $response->getStatusCode();

        if($statusCode == 200){//Éxito
            $content = $response->toArray();
            
            return number_format($productPrice * $content['rates'][$currency], 2);
        }
        
        return number_format($productPrice, 2, '.', '');
    }
}