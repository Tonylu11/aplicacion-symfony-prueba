<?php

namespace App\Service;

use App\Service\ConversionService;

class ProductService
{
    private $conversionService;
    
    public function __construct(ConversionService $conversionService)
    {
        $this->conversionService = $conversionService;
    }

    public function getProductConverted($products, $currency)
    {
        $convertedProducts = array();

        foreach ($products as $key => $product) {
            $productCategory = $product->getCategory();

            $updatedPrice = $this->conversionService->fetchCurrencyConversion($currency, $product->getPrice(), $product->getCurrency());

            array_push($convertedProducts, 
                array(
                    "id" => $product->getId(), 
                    "name" => $product->getName(), 
                    "price" => $updatedPrice,
                    "currency" => $currency,
                    "category" => array("name" => (isset($productCategory)) ? $productCategory->getName() : null)
                )
            );
        }

        return $convertedProducts;
    }
}