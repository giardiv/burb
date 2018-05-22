<?php

namespace TelegramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Flux
 *
 * @ORM\Table(name="flux")
 * @ORM\Entity(repositoryClass="TelegramBundle\Repository\FluxRepository")
 */
class Flux
{
    const ADMINID = -127335580; // Flux(group) Id

    function __construct(){
        if(func_num_args() == 1) {
            $update = func_get_args()[0];

            $message = $update->message;
            $this->setTid($message->chat->id);
            $this->setActive(true);
            $this->setArchive(false);
            $this->setName($message->chat->title);
            $this->setColor("#ffcc00");

            $date = new \DateTime();
            $this->setCreationDate($date->setTimestamp($message->date));
            //$this->setPicUrl("url");
        }
    }


    // todo
    // If the flux is already set in DB
    public static function isFluxedAction($chatId)
    {
        $fluxTid = [180160712,-123732933,self::ADMINID, -137496804 ];
        return in_array($chatId, $fluxTid);
    }

    public $lastDate;

    public function getLastDate(){
        return $this->lastDate;
    }


    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="tid", type="bigint", unique=true)
     */
    private $tid;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="subname", type="string", length=255, nullable=true)
     */
    private $subname;

    /**
     * @var string
     *
     * @ORM\Column(name="partners", type="text", nullable=true)
     */
    private $partners;

    /**
     * @var string
     *
     * @ORM\Column(name="color", type="string", length=255)
     */
    private $color;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="picUrl", type="string", length=255, nullable=true)
     */
    private $picUrl;

    /**
     * @var bool
     *
     * @ORM\Column(name="active", type="boolean")
     */
    private $active;


    /**
     * @var bool
     *
     * @ORM\Column(name="archive", type="boolean", nullable=true)
     */
    private $archive;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="creationDate", type="datetime")
     */
    private $creationDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="startDate", type="datetime", nullable=true)
     */
    private $startDate;


    /**
     * @var bool
     *
     * @ORM\Column(name="updating", type="boolean", nullable = true)
     */
    private $updating;


    /**
     * @var int
     *
     * @ORM\Column(name="startUid", type="bigint", nullable=true)
     */
    private $startUid;

    /**
     * @var string
     *
     * @ORM\Column(name="location", type="string", length=255, nullable=true)
     */
    private $location;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set tid
     *
     * @param integer $tid
     *
     * @return Flux
     */
    public function setTid($tid)
    {
        $this->tid = $tid;

        return $this;
    }

    /**
     * Get tid
     *
     * @return int
     */
    public function getTid()
    {
        return $this->tid;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Flux
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
     * Set color
     *
     * @param string $color
     *
     * @return Flux
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
     * Set description
     *
     * @param string $description
     *
     * @return Flux
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set picUrl
     *
     * @param string $picUrl
     *
     * @return Flux
     */
    public function setPicUrl($picUrl)
    {
        $this->picUrl = $picUrl;

        return $this;
    }

    /**
     * Get picUrl
     *
     * @return string
     */
    public function getPicUrl()
    {
        return $this->picUrl;
    }

    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return Flux
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return bool
     */
    public function getActive()
    {
        return $this->active;
    }


    /**
     * Set archive
     *
     * @param boolean $archive
     *
     * @return Flux
     */
    public function setArchive($archive)
    {
        $this->archive = $archive;

        return $this;
    }

    /**
     * Get archive
     *
     * @return bool
     */
    public function getArchive()
    {
        return $this->archive;
    }

    /**
     * Set creationDate
     *
     * @param \DateTime $creationDate
     *
     * @return Flux
     */
    public function setCreationDate($creationDate)
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    /**
     * Get creationDate
     *
     * @return \DateTime
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }

    /**
     * Set startDate
     *
     * @param \DateTime $startDate
     *
     * @return Flux
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get startDate
     *
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set startUid
     *
     * @param integer $startUid
     *
     * @return Flux
     */
    public function setStartUid($startUid)
    {
        $this->startUid = $startUid;

        return $this;
    }

    /**
     * Get startUid
     *
     * @return int
     */
    public function getStartUid()
    {
        return $this->startUid;
    }

    /**
     * Set location
     *
     * @param string $location
     *
     * @return Flux
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
     * Set subname
     *
     * @param string $subname
     *
     * @return Flux
     */
    public function setSubname($subname)
    {
        $this->subname = $subname;

        return $this;
    }

    /**
     * Get subname
     *
     * @return string
     */
    public function getSubname()
    {
        return $this->subname;
    }

    /**
     * Set partners
     *
     * @param string $partners
     *
     * @return Flux
     */
    public function setPartners($partners)
    {
        $this->partners = $partners;

        return $this;
    }

    /**
     * Get partners
     *
     * @return string
     */
    public function getPartners()
    {
        return $this->partners;
    }

    /**
     * Set updating
     *
     * @param boolean $updating
     *
     * @return Flux
     */
    public function setUpdating($updating)
    {
        $this->updating = $updating;

        return $this;
    }

    /**
     * Get updating
     *
     * @return boolean
     */
    public function getUpdating()
    {
        return $this->updating;
    }

    public function __toString()
    {
        return $this->name;
    }
}
