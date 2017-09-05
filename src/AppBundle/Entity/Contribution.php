<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Contribution
 *
 * @ORM\Table(name="contributions")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ContributionRepository")
 */
class Contribution
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", cascade={"persist"})
     * @ORM\JoinColumn(referencedColumnName="id")
     */
    private $contributor;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Spot", inversedBy="contributions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $spot;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->createdAt    = new \DateTime();
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Contribution
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set contributor
     *
     * @param \AppBundle\Entity\User $contributor
     *
     * @return Contribution
     */
    public function setContributor(\AppBundle\Entity\User $contributor = null)
    {
        $this->contributor = $contributor;

        return $this;
    }

    /**
     * Get contributor
     *
     * @return \AppBundle\Entity\User
     */
    public function getContributor()
    {
        return $this->contributor;
    }

    /**
     * Set spot
     *
     * @param \AppBundle\Entity\Spot $spot
     *
     * @return Contribution
     */
    public function setSpot(\AppBundle\Entity\Spot $spot)
    {
        $this->spot = $spot;

        return $this;
    }

    /**
     * Get spot
     *
     * @return \AppBundle\Entity\Spot
     */
    public function getSpot()
    {
        return $this->spot;
    }

}
