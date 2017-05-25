<?php
namespace Naucon\Form\Tests\Entities;

use Naucon\Form\FormHook;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

class Address
{
    protected $streetName;

    protected $streetNumber;

    protected $postalCode;

    protected $town;

    protected $country;

    public function getStreetName()
    {
        return $this->streetName;
    }

    public function setStreetName($streetName)
    {
        $this->streetName = $streetName;
    }

    public function getStreetNumber()
    {
        return $this->streetNumber;
    }

    public function setStreetNumber($streetNumber)
    {
        $this->streetNumber = $streetNumber;
    }

    public function getPostalCode()
    {
        return $this->postalCode;
    }

    public function setPostalCode($postalCode)
    {
        $this->postalCode = $postalCode;
    }

    public function getTown()
    {
        return $this->town;
    }

    public function setTown($town)
    {
        $this->town = $town;
    }

    public function getCountry()
    {
        return $this->country;
    }

    public function setCountry($country)
    {
        $this->country = $country;
    }

    public function prevalidatorHook(FormHook $formHook)
    {
        $this->streetName = trim($this->streetName);
        $this->streetNumber = trim($this->streetNumber);
        $this->postalCode = trim($this->postalCode);
        $this->town = trim($this->town);
        $this->country = trim($this->country);
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('streetName', new Assert\NotBlank());
        $metadata->addPropertyConstraint('streetNumber', new Assert\NotBlank());
        $metadata->addPropertyConstraint('postalCode', new Assert\NotBlank());
        $metadata->addPropertyConstraint('postalCode', new Assert\Length(
            array('min' => 5, 'max' => 5)
        ));
        $metadata->addPropertyConstraint('town', new Assert\NotBlank());
        $metadata->addPropertyConstraint('country', new Assert\NotBlank());
    }

    public function postvalidatorHook(FormHook $formHook)
    {
        if ($this->postalCode != '54321') {
            $formHook->setError('postal_code', 'has unexpected value');
        }
    }
}