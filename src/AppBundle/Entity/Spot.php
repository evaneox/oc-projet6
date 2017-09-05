<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Validator\AddressCheck;
use Cocur\Slugify\Slugify;

/**
 * Spot
 *
 * @ORM\Table(name="spots")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SpotRepository")
 */
class Spot
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
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Sport", cascade={"persist"})
     * @Assert\Count(min = 1, minMessage = "sports.min")
     */
    private $sports;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Gender", cascade={"persist"})
     */
    private $genders;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Location", cascade={"persist"})
     */
    private $locations;
    
    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Image", mappedBy="spot", cascade={"persist", "remove"})
     * @ORM\OrderBy({"createdAt" = "DESC"})
     * @Assert\Valid()
     */
    private $images;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Comment", mappedBy="spot", cascade={"persist", "remove"})
     * @ORM\OrderBy({"createdAt" = "DESC"})
     * @Assert\Valid()
     */
    private $comments;

    /**
     * @var string
     *
     * @ORM\Column(name="latitude", type="string", length=255)
     */
    private $latitude;

    /**
     * @var string
     *
     * @ORM\Column(name="longitude", type="string", length=255)
     */
    private $longitude;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=255)
     * @AddressCheck()
     */
    private $address;

    /**
     * @var string
     * @ORM\Column(name="slug", type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="spots")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Contribution", mappedBy="spot", cascade={"persist", "remove"})
     * @ORM\OrderBy({"createdAt" = "DESC"})
     */
    private $contributions;


    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(name="last_editor", nullable=true)
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(nullable=true, referencedColumnName="id")
     */
    private $lastEditor;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_editor_start_time", type="datetime", nullable=true)
     */
    private $lastEditorStartTime;


    private $distance;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->sports       = new ArrayCollection();
        $this->images       = new ArrayCollection();
        $this->createdAt    = new \DateTime();
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
     * Set latitude
     *
     * @param string $latitude
     *
     * @return Spot
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * Get latitude
     *
     * @return string
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set longitude
     *
     * @param string $longitude
     *
     * @return Spot
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * Get longitude
     *
     * @return string
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Set address
     *
     * @param string $address
     *
     * @return Spot
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }
    

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Spot
     */
    public function setDescription($description)
    {
        //$description = preg_replace('/<a href=\"(.*?)\">(.*?)<\/a>/', "\\2", $description); // Remove link
        //$description = preg_replace("/<img[^>]+\>/i", "", $description); // Remove img
        $this->description = nl2br(strip_tags($description, '<p><br><h3><h4><b><u><hr>'));

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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Spot
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
     * Add sport
     *
     * @param \AppBundle\Entity\Sport $sport
     *
     * @return Spot
     */
    public function addSport(\AppBundle\Entity\Sport $sport)
    {
        $this->sports[] = $sport;

        return $this;
    }

    /**
     * Remove sport
     *
     * @param \AppBundle\Entity\Sport $sport
     */
    public function removeSport(\AppBundle\Entity\Sport $sport)
    {
        $this->sports->removeElement($sport);
    }

    /**
     * Get sports
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSports()
    {
        return $this->sports;
    }

    /**
     * Set author
     *
     * @param string $author
     *
     * @return Spot
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Add image
     *
     * @param \AppBundle\Entity\Image $image
     *
     * @return Spot
     */
    public function addImage(\AppBundle\Entity\Image $image)
    {
        $this->images[] = $image;

        return $this;
    }

    /**
     * Remove image
     *
     * @param \AppBundle\Entity\Image $image
     */
    public function removeImage(\AppBundle\Entity\Image $image)
    {
        $this->images->removeElement($image);
    }

    /**
     * Get images
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * Get number image items
     *
     * @return int
     */
    public function getCountImages()
    {
        return sizeof($this->getImages());
    }


    /**
     * Set distance
     *
     * @param string $distance
     *
     * @return String
     */
    public function setDistance($distance)
    {
        $this->distance = $distance;

        return $this;
    }

    /**
     * Get distance
     *
     * @return string
     */
    public function getDistance()
    {
        return $this->distance;
    }

    /**
     * Set lastEditor
     *
     * @param string $lastEditor
     *
     * @return Spot
     */
    public function setLastEditor($lastEditor)
    {
        $this->lastEditor = $lastEditor;

        return $this;
    }

    /**
     * Get lastEditor
     *
     * @return string
     */
    public function getLastEditor()
    {
        return $this->lastEditor;
    }

    /**
     * Set lastEditorStartTime
     *
     * @return Spot
     */
    public function setLastEditorStartTime()
    {
        $this->lastEditorStartTime = new \DateTime();;

        return $this;
    }

    /**
     * Get lastEditorStartTime
     *
     * @return \DateTime
     */
    public function getLastEditorStartTime()
    {
        return $this->lastEditorStartTime;
    }

    /**
     * Get second elapse since last edition
     *
     * @return int
     */
    public function getTimeElapseSinceLastEdition()
    {
        $timestampNow           = strtotime("now");
        $timestampLastStartEdit = null == $this->getLastEditorStartTime() ? '0' : ($this->getLastEditorStartTime()->format('Y-m-d H:i:s'));
        return round( ($timestampNow - $timestampLastStartEdit) / 60 );
    }

    /**
     * Add gender
     *
     * @param \AppBundle\Entity\Gender $gender
     *
     * @return Spot
     */
    public function addGender(\AppBundle\Entity\Gender $gender)
    {
        $this->genders[] = $gender;

        return $this;
    }

    /**
     * Remove gender
     *
     * @param \AppBundle\Entity\Gender $gender
     */
    public function removeGender(\AppBundle\Entity\Gender $gender)
    {
        $this->genders->removeElement($gender);
    }

    /**
     * Get genders
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGenders()
    {
        return $this->genders;
    }

    /**
     * Add location
     *
     * @param \AppBundle\Entity\Location $location
     *
     * @return Spot
     */
    public function addLocation(\AppBundle\Entity\Location $location)
    {
        $this->locations[] = $location;

        return $this;
    }

    /**
     * Remove location
     *
     * @param \AppBundle\Entity\Location $location
     */
    public function removeLocation(\AppBundle\Entity\Location $location)
    {
        $this->locations->removeElement($location);
    }

    /**
     * Get locations
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLocations()
    {
        return $this->locations;
    }
    


    /**
     * Add contribution
     *
     * @param \AppBundle\Entity\Contribution $contribution
     *
     * @return Spot
     */
    public function addContribution(\AppBundle\Entity\Contribution $contribution)
    {
        $this->contributions[] = $contribution;

        return $this;
    }

    /**
     * Remove contribution
     *
     * @param \AppBundle\Entity\Contribution $contribution
     */
    public function removeContribution(\AppBundle\Entity\Contribution $contribution)
    {
        $this->contributions->removeElement($contribution);
    }

    /**
     * Get contributions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getContributions()
    {
        return $this->contributions;
    }


    /**
     * Add comment
     *
     * @param \AppBundle\Entity\Comment $comment
     *
     * @return Spot
     */
    public function addComment(\AppBundle\Entity\Comment $comment)
    {
        $this->comments[] = $comment;

        return $this;
    }

    /**
     * Remove comment
     *
     * @param \AppBundle\Entity\Comment $comment
     */
    public function removeComment(\AppBundle\Entity\Comment $comment)
    {
        $this->comments->removeElement($comment);
    }

    /**
     * Get comments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getComments()
    {
        return $this->comments;
    }


    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return Spot
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
        if(!empty($this->slug)){
            return $this->slug;
        }else{
            $slugify = new Slugify();
            return $slugify->slugify($this->getAddress());
        }
        return $this->slug;
    }
}
