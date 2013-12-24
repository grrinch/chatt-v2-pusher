<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MedImages
 *
 * @ORM\Table(name="med_images", indexes={@ORM\Index(name="fk_med_images_1", columns={"user_id"}), @ORM\Index(name="fk_med_images_2", columns={"room_id"})})
 * @ORM\Entity
 */
class MedImages
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
     * @ORM\Column(name="name", type="string", length=45, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="location", type="string", length=255, nullable=false)
     */
    private $location;

    /**
     * @var \Application\Entity\MedUsers
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MedUsers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;

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
     * Set name
     *
     * @param string $name
     * @return MedImages
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set location
     *
     * @param string $location
     * @return MedImages
     */
    public function setLocation($location)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get location
     *
     * @return string 
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set user
     *
     * @param \Application\Entity\MedUsers $user
     * @return MedImages
     */
    public function setUser(\Application\Entity\MedUsers $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Application\Entity\MedUsers 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set room
     *
     * @param \Application\Entity\MedRooms $room
     * @return MedImages
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
