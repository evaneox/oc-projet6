<?php

namespace AppBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Image
 *
 * @ORM\Table(name="images")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ImageRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Image
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
     * @ORM\Column(name="url", type="string", length=255)
     */
    private $url;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Spot", inversedBy="images")
     * @ORM\JoinColumn(referencedColumnName="id", nullable=false)
     */
    private $spot;

    /**
     * @Assert\File(
     *     maxSize = "5M",
     *     mimeTypes = {"image/jpeg"},
     *     maxSizeMessage = "avatar.size",
     *     mimeTypesMessage = "avatar.format")
     *
     * @Assert\Image(
     *     allowPortrait = false,
     *     allowSquare = false,
     *     minWidth = 600,
     *     minHeight = 450)
     */
    private $file;

    private $tempFilename;

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
     * @return Image
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
     * Set spot
     *
     * @param \AppBundle\Entity\Spot $spot
     *
     * @return Image
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

    /**
     * Set url
     *
     * @param string $url
     *
     * @return Image
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->getUploadDir().'/'. $this->url;
    }

    /**
     * Get file
     *
     * @return mixed
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set File
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;

        if ($this->url !== null) {
            $this->tempFilename = $this->url;
            $this->url = null;
        } else {
            $this->url = 'initial';
        }
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        if (null !== $this->file) {
            $filename = sha1(uniqid(mt_rand(), true));
            $this->url = $filename.'.'.$this->getFile()->guessExtension();
        }
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        if (null === $this->file) {
            return;
        }

        // Upload image to destination
        $this->getFile()->move($this->getUploadRootDir(), $this->url);

        // Picture compression
        $image = imagecreatefromjpeg($this->getAbsolutePath());

        if (file_exists($this->getAbsolutePath())) {
            unlink($this->getAbsolutePath());
        }

        imagejpeg($image, $this->getAbsolutePath(), 30);

        $this->file = null;

    }

    /**
     * Get absolute path
     *
     * @return null|string
     */
    public function getAbsolutePath()
    {
        return null === $this->url
            ? null
            : $this->getUploadRootDir().'/'.$this->url;
    }

    /**
     * Get web path
     *
     * @return null|string
     */
    public function getWebPath()
    {
        return null === $this->url
            ? null
            : $this->getUploadDir().'/'.$this->url;
    }

    /**
     * Get upload directory
     *
     * @return string
     */
    public function getUploadDir()
    {
        return 'uploads/pictures/spot';
    }

    /**
     * Get upload directory from root
     *
     * @return string
     */
    protected function getUploadRootDir()
    {
        return __DIR__.'/../../../web/'.$this->getUploadDir();
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        $file = $this->getAbsolutePath();
        if (file_exists($file)) {
            unlink($file);
        }
    }
}
