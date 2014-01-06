<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MedTalk
 *
 * @ORM\Table(name="med_talk", indexes={@ORM\Index(name="fk_med_talk_room", columns={"room_id"}), @ORM\Index(name="fk_med_talk_user", columns={"user_id"}), @ORM\Index(name="fk_med_talk_image", columns={"image_id"})})
 * @ORM\Entity
 */
class MedTalk
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
     * @ORM\Column(name="content", type="text", nullable=false)
     */
    private $content;

    /**
     * @var integer
     *
     * @ORM\Column(name="time", type="bigint", nullable=false)
     */
    private $time;

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
     * @var \Application\Entity\MedUsers
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MedUsers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;

    /**
     * @var \Application\Entity\MedImages
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MedImages")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="image_id", referencedColumnName="id")
     * })
     */
    private $image;



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
     * Set content
     *
     * @param string $content
     * @return MedTalk
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string 
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set time
     *
     * @param integer $time
     * @return MedTalk
     */
    public function setTime($time = null)
    {
        $this->time = time();

        return $this;
    }

    /**
     * Get time
     *
     * @return integer 
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * Set room
     *
     * @param \Application\Entity\MedRooms $room
     * @return MedTalk
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

    /**
     * Set user
     *
     * @param \Application\Entity\MedUsers $user
     * @return MedTalk
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
     * Set image
     *
     * @param \Application\Entity\MedImages $image
     * @return MedTalk
     */
    public function setImage(\Application\Entity\MedImages $image = null)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return \Application\Entity\MedImages 
     */
    public function getImage()
    {
        return $this->image;
    }
}
