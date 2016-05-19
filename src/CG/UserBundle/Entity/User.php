<?php

namespace CG\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="CG\UserBundle\Repository\UserRepository")
 */
class User extends BaseUser
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="nb_cuddles", type="integer")
     */
    private $nbCuddles = 0;

    /**
     * @ORM\ManyToMany(targetEntity="CG\CuddleBundle\Entity\Category")
     * 
     */
    private $subscriptions;

    public function increaseNbCuddles()
    {
        $this->nbCuddles++;
    }

    public function decreaseNbCuddles()
    {
        $this->nbCuddles--;
    }

    /**
     * @Assert\Callback
     */
    public function hasNotTooMuchSubscriptions(ExecutionContextInterface $context)
    {
        if (count($this->subscriptions) > 1) {
            $context
                ->buildViolation("Vous ne pouvez vous inscrire qu'à une catégorie")
                ->atPath('subscriptions')
                ->addViolation()
            ;
        }
    }

    /**
     * Add subscription
     *
     * @param \CG\CuddleBundle\Entity\Category $subscription
     *
     * @return User
     */
    public function addSubscription(\CG\CuddleBundle\Entity\Category $subscription)
    {
        $this->subscriptions[] = $subscription;

        return $this;
    }

    /**
     * Remove subscription
     *
     * @param \CG\CuddleBundle\Entity\Category $subscription
     */
    public function removeSubscription(\CG\CuddleBundle\Entity\Category $subscription)
    {
        $this->subscriptions->removeElement($subscription);
    }

    /**
     * Get subscriptions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSubscriptions()
    {
        return $this->subscriptions;
    }
}
