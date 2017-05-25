<?php
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

class User
{
    protected $username;

    protected $firstname;

    protected $lastname;

    protected $email;

    protected $age;

    protected $newsletter;

    protected $comment;


    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function getFirstname()
    {
        return $this->firstname;
    }

    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    }

    public function getLastname()
    {
        return $this->lastname;
    }

    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getAge()
    {
        return $this->age;
    }

    public function setAge($age)
    {
        $this->age = $age;
    }

    public function getNewsletter()
    {
        return $this->newsletter;
    }

    public function setNewsletter($newsletter)
    {
        $this->newsletter = $newsletter;
    }

    public function getComment()
    {
        return $this->comment;
    }

    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('username', new Assert\NotBlank());
        $metadata->addPropertyConstraint('username', new Assert\Length(
            array('min' => 5, 'max' => 50)
        ));
        $metadata->addPropertyConstraint('email', new Assert\Email());
        $metadata->addPropertyConstraint('age', new Assert\Type('numeric'));
    }
}