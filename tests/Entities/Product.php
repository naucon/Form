<?php
namespace Naucon\Form\Tests\Entities;

use Naucon\Form\Validator\Constraints as NauconAsserts;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

class Product
{
    protected $productId;

    protected $productNumber;

    protected $productDesc;

    protected $price;

    public function getProductId()
    {
        return $this->productId;
    }

    public function setProductId($productId)
    {
        $this->productId = $productId;
    }

    public function getProductNumber()
    {
        return $this->productNumber;
    }

    public function setProductNumber($productNumber)
    {
        $this->productNumber = $productNumber;
    }

    public function getProductDesc()
    {
        return $this->productDesc;
    }

    public function setProductDesc($productDesc)
    {
        $this->productDesc = $productDesc;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function setPrice($price)
    {
        $this->price = $price;
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('productNumber', new Assert\NotBlank());
        $metadata->addPropertyConstraint('productDesc', new Assert\NotBlank());
        $metadata->addPropertyConstraint('price', new Assert\NotBlank());
        $metadata->addPropertyConstraint('price', new NauconAsserts\IsDecimal());
    }
}
