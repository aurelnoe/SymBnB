<?php

namespace App\Entity;

use App\Entity\Ad;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ImageRepository;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ImageRepository::class)
 */
class Image
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Url()
     */
    private $url;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min=10, minMessage="Votre image doit faire plus de 10 caractères !")
     */
    private $caption;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @ORM\ManyToOne(targetEntity=Ad::class, inversedBy="images", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $ad;

    public function __toString()
    {
        return 
            $this->id .
            $this->url .
            $this->caption;
    }

    /**
     * Get the value of url
     */ 
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * Set the value of url
     *
     * @return  self
     */ 
    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get the value of caption
     */ 
    public function getCaption(): ?string
    {
        return $this->caption;
    }

    /**
     * Set the value of caption
     *
     * @return  self
     */ 
    public function setCaption(?string $caption): self
    {
        $this->caption = $caption;

        return $this;
    }

    /**
     * Get the value of ad
     */ 
    public function getAd(): ?Ad
    {
        return $this->ad;
    }

    /**
     * Set the value of ad
     *
     * @return  self
     */ 
    public function setAd(?Ad $ad):self
    {
        $this->ad = $ad;

        return $this;
    }
}
