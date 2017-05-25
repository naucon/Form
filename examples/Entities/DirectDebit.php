<?php
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

class DirectDebit
{
    protected $accountHolderName;

    protected $iban = '';

    protected $bic = '';

    protected $bank = '';

    public function getAccountHolderName()
    {
        return $this->accountHolderName;
    }

    public function setAccountHolderName($name)
    {
        $this->accountHolderName = $name;
    }

    public function getIban()
    {
        return $this->iban;
    }

    public function setIban($iban)
    {
        $this->iban = $iban;
    }

    public function getBic()
    {
        return $this->bic;
    }

    public function setBic($bic)
    {
        $this->bic = $bic;
    }

    public function getBank()
    {
        return $this->bank;
    }

    public function setBank($bank)
    {
        $this->bank = $bank;
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('accountHolderName', new Assert\NotBlank());
        $metadata->addPropertyConstraint('iban', new Assert\NotBlank());
        $metadata->addPropertyConstraint('bic', new Assert\NotBlank());
        $metadata->addPropertyConstraint('bank', new Assert\NotBlank());
    }
}