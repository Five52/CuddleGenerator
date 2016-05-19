<?php

namespace CG\CuddleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Cuddle
 *
 * @ORM\Table(name="cuddle")
 * @ORM\Entity(repositoryClass="CG\CuddleBundle\Repository\CuddleRepository")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity(fields="content", message="Un câlin existe déjà avec ce message.")
 */
class Cuddle
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
     * @ORM\Column(name="content", type="string", length=255, unique=true)
     * @Assert\Length(min=10, minMessage="Le message doit faire au moins {{ limit }} caractères.")
     */
    private $content;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var string
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity="CG\UserBundle\Entity\User")
     */
    private $author;

    /**
     * @ORM\ManyToOne(targetEntity="CG\CuddleBundle\Entity\Category", inversedBy="cuddles", cascade={"persist"})
     * ORM\JoinColumn(nullable=false)
     */
    private $category;

    /**
     * @var boolean
     * @ORM\Column(name="validated", type="boolean")
     */
    private $validated;

    /**
     * @var string
     * @Gedmo\Blameable(on="change", field={"validated"})
     * @ORM\ManyToOne(targetEntity="CG\UserBundle\Entity\User")
     */
    private $validatedBy;

    /**
     * @var string
     * @Gedmo\Blameable(on="change", field={"content", "category"})
     * @ORM\ManyToOne(targetEntity="CG\UserBundle\Entity\User")
     */
    private $editedBy;

    public function __construct()
    {
        $this->date = new \DateTime();
        $this->validated = false;
    }

    /**
     * @Assert\Callback
     */
    public function isContentValid(ExecutionContextInterface $context)
    {
        $filepath = realpath(__DIR__ . '/../Validation/dictionary.txt');
        $forbiddenWords = file($filepath, FILE_IGNORE_NEW_LINES);
        // $forbiddenWords = ['moche', 'stupide', 'hideux'];
        if (preg_match_all(
            '#' . implode('|', $forbiddenWords) . '#i',
            $this->getContent(),
            $matches
        )) {
            $offendingWords = '';
            $first = true;
            foreach($matches[0] as $match) {
                if ($first) {
                    $offendingWords .= (string) $match;
                    $first = false;
                } else {
                    $offendingWords .= ', ' . (string) $match;
                }
            }
            $context
                ->buildViolation('Votre câlin contient des termes offensants : ' . $offendingWords)
                ->atPath('content')
                ->addViolation()
            ;
        }
    }

    /**
     * @ORM\PostPersist
     */
    public function increaseUserNbCuddles()
    {
        $this->getAuthor()->increaseNbCuddles();
    }

    /**
     * @ORM\PreRemove
     */
    public function decreaseUserNbCuddles()
    {
        $this->getAuthor()->decreaseNbCuddles();
    }

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
     *
     * @return Cuddle
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
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Cuddle
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
     * Set validated
     *
     * @param boolean $validated
     *
     * @return Cuddle
     */
    public function setValidated($validated)
    {
        $this->validated = $validated;

        return $this;
    }

    /**
     * Get validated
     *
     * @return boolean
     */
    public function getValidated()
    {
        return $this->validated;
    }

    /**
     * Set author
     *
     * @param \CG\UserBundle\Entity\User $author
     *
     * @return Cuddle
     */
    public function setAuthor(\CG\UserBundle\Entity\User $author = null)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return \CG\UserBundle\Entity\User
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set category
     *
     * @param \CG\CuddleBundle\Entity\Category $category
     *
     * @return Cuddle
     */
    public function setCategory(\CG\CuddleBundle\Entity\Category $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \CG\CuddleBundle\Entity\Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set validatedBy
     *
     * @param \CG\UserBundle\Entity\User $validatedBy
     *
     * @return Cuddle
     */
    public function setValidatedBy(\CG\UserBundle\Entity\User $validatedBy = null)
    {
        $this->validatedBy = $validatedBy;

        return $this;
    }

    /**
     * Get validatedBy
     *
     * @return \CG\UserBundle\Entity\User
     */
    public function getValidatedBy()
    {
        return $this->validatedBy;
    }

    /**
     * Set editedBy
     *
     * @param \CG\UserBundle\Entity\User $editedBy
     *
     * @return Cuddle
     */
    public function setEditedBy(\CG\UserBundle\Entity\User $editedBy = null)
    {
        $this->editedBy = $editedBy;

        return $this;
    }

    /**
     * Get editedBy
     *
     * @return \CG\UserBundle\Entity\User
     */
    public function getEditedBy()
    {
        return $this->editedBy;
    }
}
