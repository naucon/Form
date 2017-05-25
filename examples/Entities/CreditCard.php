<?php
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

class CreditCard
{
    protected $cardBrand;

    protected $cardHolderName;

    protected $cardNumber;

    protected $expirationDate;

    public function getCardBrand()
    {
        return $this->cardBrand;
    }

    public function setCardBrand($brand)
    {
        $this->cardBrand = $brand;
    }

    public function getCardHolderName()
    {
        return $this->cardHolderName;
    }

    public function setCardHolderName($name)
    {
        $this->cardHolderName = $name;
    }

    public function getCardNumber()
    {
        return $this->cardNumber;
    }

    public function setCardNumber($number)
    {
        $this->cardNumber = $number;
    }

    public function getExpirationDate()
    {
        return $this->expirationDate;
    }

    public function setExpirationDate($date)
    {
        $this->expirationDate = $date;
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('cardBrand', new Assert\NotBlank());
        $metadata->addPropertyConstraint('cardHolderName', new Assert\NotBlank());
        $metadata->addPropertyConstraint('cardNumber', new Assert\NotBlank());
        $metadata->addPropertyConstraint('expirationDate', new Assert\NotBlank());
    }
}