<?php

namespace App\Factory;

use App\Entity\ProductData;
use DateTime;

class ProductFactory
{
    public function createProduct(
        string $name,
        string $description,
        string $code,
        float  $price,
        int    $stock,
        bool   $isDiscontinued,
    ): ProductData {
        $product = new ProductData();

        $product
            ->setName($name)
            ->setDescription($description)
            ->setCode($code)
            ->setPrice($price)
            ->setStockLevel($stock)
            ->setAdded(new DateTime())
            ->setTimestamp(new DateTime());

        if ($isDiscontinued) {
            $product->setDiscontinued(new DateTime());
        }

        return $product;
    }
}
