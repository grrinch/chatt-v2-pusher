<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MedUsers
 *
 * @ORM\Table(name="med_users", uniqueConstraints={@ORM\UniqueConstraint(name="email_UNIQUE", columns={"email"})})
 * @ORM\Entity
 */
class MedUsers
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=50, nullable=false)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="imie_nazwisko", type="string", length=255, nullable=false)
     */
    private $imieNazwisko;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=200, nullable=false)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="pass", type="string", length=128, nullable=false)
     */
    private $pass;

    /**
     * @var string
     *
     * @ORM\Column(name="color", type="string", length=6, nullable=false)
     */
    private $color;

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean", nullable=false)
     */
    private $active = '1';

    /**
     * @var string
     *
     * @ORM\Column(name="ip", type="string", length=150, nullable=true)
     */
    private $ip;



    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return MedUsers
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string 
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set imieNazwisko
     *
     * @param string $imieNazwisko
     * @return MedUsers
     */
    public function setImieNazwisko($imieNazwisko)
    {
        $this->imieNazwisko = $imieNazwisko;

        return $this;
    }

    /**
     * Get imieNazwisko
     *
     * @return string 
     */
    public function getImieNazwisko()
    {
        return $this->imieNazwisko;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return MedUsers
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set pass
     *
     * @param string $pass
     * @return MedUsers
     */
    public function setPass($pass)
    {
        $this->pass = $pass;

        return $this;
    }

    /**
     * Get pass
     *
     * @return string 
     */
    public function getPass()
    {
        return $this->pass;
    }

    /**
     * Set color
     *
     * @param string $color
     * @return MedUsers
     */
    public function setColor($color)
    {
        $this->color = $color;

        return $this;
    }

    /**
     * Get color
     *
     * @return string 
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * Set active
     *
     * @param boolean $active
     * @return MedUsers
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean 
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set ip
     *
     * @param string $ip
     * @return MedUsers
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get ip
     *
     * @return string 
     */
    public function getIp()
    {
        return $this->ip;
    }
}
