<?php

namespace GenjioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * API Access
 *
 * @ORM\Entity
 * @ORM\Table(name="uploads")
 * @Vich\Uploadable
 */
class Upload
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     */
    private $user;

    /**
     * @Vich\UploadableField(mapping="upload", fileNameProperty="uploadName")
     */
    private $uploadFile;

    /**
     * @ORM\Column(name="upload_name", type="string", length=255)
     */
    private $uploadName;

    public function getId()
    {
        return $this->id;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    public function getUploadFile()
    {
        return $this->uploadFile;
    }

    public function setUploadFile(File $upload = null)
    {
        $this->uploadFile = $upload;
        return $this;
    }

    public function getUploadName()
    {
        return $this->uploadName;
    }

    public function setUploadName($uploadName)
    {
        $this->uploadName = $uploadName;
        return $this;
    }
}
