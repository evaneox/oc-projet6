<?php

namespace AppBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Captcha\Bundle\CaptchaBundle\Validator\Constraints as CaptchaAssert;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="users")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Spot", mappedBy="author")
     */
    protected $spots;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $created_at;

    /**
     * @ORM\Column(name="avatar_path", type="string", length=255, nullable=true)
     */
    protected $avatar_path;

    /**
     * @Assert\File(
     *     maxSize = "5M",
     *     mimeTypes = {"image/jpeg", "image/gif", "image/png"},
     *     maxSizeMessage = "avatar.size",
     *     mimeTypesMessage = "avatar.format")
     */
    protected $avatar;

    protected $avatarTemp;

    public function __construct()
    {
        parent::__construct();
        $this->setCreatedAt(new \DateTime());
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return User
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set avatarPath
     *
     * @param string $avatarPath
     *
     * @return User
     */
    public function setAvatarPath($avatarPath)
    {
        $this->avatar_path = $avatarPath;

        return $this;
    }

    /**
     * Get avatarPath
     *
     * @return string
     */
    public function getAvatarPath()
    {
        return null === $this->avatar_path
            ? $this->getUploadDir().'/no_avatar.png'
            : $this->getUploadDir().'/'.$this->avatar_path;
    }

    /**
     * Get avatar
     *
     * @return mixed
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * Set avatar
     *
     * @param UploadedFile $avatar
     */
    public function setAvatar(UploadedFile $avatar = null)
    {
        $this->avatar = $avatar;

        if ($this->avatar_path !== null) {
            $this->avatarTemp = $this->avatar_path;
            $this->avatar_path = null;
        } else {
            $this->avatar_path = 'initial';
        }
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        if (null !== $this->avatar) {
            $filename = sha1(uniqid(mt_rand(), true));
            $this->avatar_path = $filename.'.'.$this->getAvatar()->guessExtension();
        }
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        if (null === $this->avatar) {
            return;
        }

        if ( null !== $this->avatarTemp) {

            $oldAvatar = $this->getUploadRootDir(). '/' . $this->avatarTemp;

            if(file_exists($oldAvatar)){
                unlink($oldAvatar);
            }

            $this->avatarTemp = null;
        }

        $this->getAvatar()->move($this->getUploadRootDir(), $this->avatar_path);

        // Picture compression
        $image = imagecreatefromjpeg($this->getAbsolutePath());

        if (file_exists($this->getAbsolutePath())) {
            unlink($this->getAbsolutePath());
        }

        imagejpeg($image, $this->getAbsolutePath(), 40);

        $this->avatar = null;
    }

    /**
     * Get web path
     *
     * @return null|string
     */
    public function getAbsolutePath()
    {
        return null === $this->avatar_path
            ? null
            : $this->getUploadRootDir().'/'.$this->avatar_path;
    }

    /**
     * Get web path
     *
     * @return null|string
     */
    public function getWebPath()
    {
        return null === $this->avatar_path
            ? null
            : $this->getUploadDir().'/'.$this->avatar_path;
    }

    /**
     * Get upload directory
     *
     * @return string
     */
    public function getUploadDir()
    {
        return 'uploads/user/avatar';
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
        if ($file) {
            unlink($file);
        }
    }

    /**
     * Add spot
     *
     * @param \AppBundle\Entity\Spot $spot
     *
     * @return User
     */
    public function addSpot(\AppBundle\Entity\Spot $spot)
    {
        $this->spots[] = $spot;

        return $this;
    }

    /**
     * Remove spot
     *
     * @param \AppBundle\Entity\Spot $spot
     */
    public function removeSpot(\AppBundle\Entity\Spot $spot)
    {
        $this->spots->removeElement($spot);
    }

    /**
     * Get spots
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSpots()
    {
        return $this->spots;
    }
}
