<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MedUsers
 *
 * @ORM\Table(name="med_users", uniqueConstraints={@ORM\UniqueConstraint(name="email_UNIQUE", columns={"email"}), @ORM\UniqueConstraint(name="username_UNIQUE", columns={"username"})}, indexes={@ORM\Index(name="fk_med_users_rooms", columns={"room_id"})})
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
     * @var \Application\Entity\MedRooms
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MedRooms")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="room_id", referencedColumnName="id")
     * })
     */
    private $room;



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

    /**
     * Set room
     *
     * @param \Application\Entity\MedRooms $room
     * @return MedUsers
     */
    public function setRoom(\Application\Entity\MedRooms $room = null)
    {
        $this->room = $room;

        return $this;
    }

    /**
     * Get room
     *
     * @return \Application\Entity\MedRooms 
     */
    public function getRoom()
    {
        return $this->room;
    }
}
