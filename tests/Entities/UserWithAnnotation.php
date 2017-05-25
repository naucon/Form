<?php
namespace Naucon\Form\Tests\Entities;

use Symfony\Component\Validator\Constraints as Assert;

class UserWithAnnotation
{
    /**
     * @Assert\NotBlank()
     * @Assert\Length(min = 5, max = 50)
     */
    protected $username;

    protected $firstname;

    protected $lastname;

    /**
     * @Assert\Email()
     */
    protected $email;

    /**
     * @Assert\Type("numeric")
     */
    protected $age;

    protected $newsletter;

    protected $comment;

    protected $secret = 'secrethash';

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

    public function getSecret()
    {
        return $this->secret;
    }

    protected function setSecret($secret)
    {
        $this->secret = $secret;
    }
}