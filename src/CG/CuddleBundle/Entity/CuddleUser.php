<?php

namespace CG\CuddleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CuddleUser
 *
 * @ORM\Table(name="cuddle_user")
 * @ORM\Entity(repositoryClass="CG\CuddleBundle\Repository\CuddleUserRepository")
 */
class CuddleUser
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;


    /**
     * @ORM\ManyToOne(targetEntity="CG\CuddleBundle\Entity\Cuddle")
     * @ORM\JoinColumn(nullable=false)
     */
    private $cuddle;

    /**
     * @ORM\ManyToOne(targetEntity="CG\UserBundle\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function __construct()
    {
        $this->date = new \DateTime();
    }

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
     * Set date
     *
     * @param \DateTime $date
     *
     * @return CuddleUser
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set cuddle
     *
     * @param \CG\CuddleBundle\Entity\Cuddle $cuddle
     *
     * @return CuddleUser
     */
    public function setCuddle(\CG\CuddleBundle\Entity\Cuddle $cuddle)
    {
        $this->cuddle = $cuddle;

        return $this;
    }

    /**
     * Get cuddle
     *
     * @return \CG\CuddleBundle\Entity\Cuddle
     */
    public function getCuddle()
    {
        return $this->cuddle;
    }

    /**
     * Set user
     *
     * @param \CG\UserBundle\Entity\User $user
     *
     * @return CuddleUser
     */
    public function setUser(\CG\UserBundle\Entity\User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \CG\UserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }
}
