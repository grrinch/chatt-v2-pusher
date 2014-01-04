<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MedRooms
 *
 * @ORM\Table(name="med_rooms", indexes={@ORM\Index(name="hash", columns={"hash"})})
 * @ORM\Entity
 */
class MedRooms {

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
     * @ORM\Column(name="hash", type="string", length=32, nullable=false)
     */
    private $hash;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=150, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="pass", type="string", length=40, nullable=false)
     */
    private $pass;

    /**
     * @var integer
     *
     * @ORM\Column(name="last_act", type="bigint", nullable=false)
     */
    private $lastAct;

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean", nullable=false)
     */
    private $active = '1';

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set hash
     *
     * @param string $hash
     * @return MedRooms
     */
    public function setHash($hash) {
        $this->hash = $hash;

        return $this;
    }

    /**
     * Get hash
     *
     * @return string 
     */
    public function getHash() {
        return $this->hash;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return MedRooms
     */
    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Set pass
     *
     * @param string $pass
     * @return MedRooms
     */
    public function setPass($pass) {
        $this->pass = $pass;

        return $this;
    }

    /**
     * Get pass
     *
     * @return string 
     */
    public function getPass() {
        return $this->pass;
    }

    /**
     * Set lastAct
     *
     * @param integer $lastAct
     * @return MedRooms
     */
    public function setLastAct($lastAct) {
        $this->lastAct = time();

        return $this;
    }

    /**
     * Get lastAct
     *
     * @return integer 
     */
    public function getLastAct() {
        return $this->lastAct;
    }

    /**
     * Set active
     *
     * @param boolean $active
     * @return MedRooms
     */
    public function setActive($active) {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean 
     */
    public function getActive() {
        return $this->active;
    }

}
