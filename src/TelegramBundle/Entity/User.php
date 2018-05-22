<?php

namespace TelegramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="TelegramBundle\Repository\UserRepository")
 */
class User
{
    const BOTID = 231759836;

    function __construct($from){
        $this->setTid($from->id);
        $this->setFname($from->first_name);
        if(isset($from->last_name)){
        $this->setLname($from->last_name);}
        $this->setAdmin(false);
        if(isset($from->username)){
            $this->setUsername($from->username);
        }
    }

    public static function isAdmin($tid){
        return in_array($tid, User::getAdminIds());
    }
    // todo
    private static function getAdminIds(){
        return ['180160712','114177835','153163864','182250070','180170485','228957975'];
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
     * @ORM\Column(name="fname", type="string", length=255, nullable=true)
     */
    private $fname;

    /**
     * @var string
     *
     * @ORM\Column(name="lname", type="string", length=255, nullable=true)
     */
    private $lname;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=255, nullable=true)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="picUrl", type="string", length=255, nullable=true)
     */
    private $picUrl;

    /**
     * @var bool
     *
     * @ORM\Column(name="admin", type="boolean")
     */
    private $admin;


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
     * @return User
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
     * Set fname
     *
     * @param string $fname
     *
     * @return User
     */
    public function setFname($fname)
    {
        $this->fname = $fname;

        return $this;
    }

    /**
     * Get fname
     *
     * @return string
     */
    public function getFname()
    {
        return $this->fname;
    }

    /**
     * Set lname
     *
     * @param string $lname
     *
     * @return User
     */
    public function setLname($lname)
    {
        $this->lname = $lname;

        return $this;
    }

    /**
     * Get lname
     *
     * @return string
     */
    public function getLname()
    {
        return $this->lname;
    }

    /**
     * Set username
     *
     * @param string $username
     *
     * @return User
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
     * Set picUrl
     *
     * @param string $picUrl
     *
     * @return User
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
     * Set admin
     *
     * @param boolean $admin
     *
     * @return User
     */
    public function setAdmin($admin)
    {
        $this->admin = $admin;

        return $this;
    }

    /**
     * Get admin
     *
     * @return bool
     */
    public function getAdmin()
    {
        return $this->admin;
    }
    public function __toString()
    {
        return $this->fname .' '.$this->lname;
    }
}

