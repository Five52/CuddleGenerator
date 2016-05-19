<?php

namespace CG\CuddleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Category
 *
 * @ORM\Table(name="category")
 * @ORM\Entity(repositoryClass="CG\CuddleBundle\Repository\CategoryRepository")
 * @UniqueEntity(fields="name", message="Une catégorie existe déjà avec ce nom.")
 */
class Category
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     * @Assert\NotBlank(message="Le champ ne doit pas être vide.")
     */
    private $name;

    /**
     * @var string
     *
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(name="slug", type="string", length=255, unique=true)
     */
    private $slug;

    /**
     * @ORM\OneToMany(targetEntity="CG\CuddleBundle\Entity\Cuddle", mappedBy="category")
     */
    private $cuddles;

    /**
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity="CG\UserBundle\Entity\User")
     */
    private $creator;


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
     * Set name
     *
     * @param string $name
     *
     * @return Category
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
     * Constructor
     */
    public function __construct()
    {
        $this->cuddles = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add cuddle
     *
     * @param \CG\CuddleBundle\Entity\Cuddle $cuddle
     *
     * @return Category
     */
    public function addCuddle(\CG\CuddleBundle\Entity\Cuddle $cuddle)
    {
        $this->cuddles[] = $cuddle;

        return $this;
    }

    /**
     * Remove cuddle
     *
     * @param \CG\CuddleBundle\Entity\Cuddle $cuddle
     */
    public function removeCuddle(\CG\CuddleBundle\Entity\Cuddle $cuddle)
    {
        $this->cuddles->removeElement($cuddle);
    }

    /**
     * Get cuddles
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCuddles()
    {
        return $this->cuddles;
    }

    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return Category
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set creator
     *
     * @param \CG\UserBundle\Entity\User $creator
     *
     * @return Category
     */
    public function setCreator(\CG\UserBundle\Entity\User $creator = null)
    {
        $this->creator = $creator;

        return $this;
    }

    /**
     * Get creator
     *
     * @return \CG\UserBundle\Entity\User
     */
    public function getCreator()
    {
        return $this->creator;
    }
}
