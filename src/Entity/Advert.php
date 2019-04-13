<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AdvertRepository")
 */
class Advert{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
    * @ORM\OneToOne(targetEntity="App\Entity\Image", cascade={"persist"})
    */
    private $image;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $author;

    /**
     * @ORM\Column(type="text")
     */
    private $content;

    /**
    * @ORM\Column(name="published", type="boolean")
    */
    private $published = true;

    public function __construct(){
        
        // Par défaut, la date de l'annonce est la date d'aujourd'hui
        $this->date = new \Datetime();
        $this->published = true;
    }

    public function getId(): ?int{
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface{
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self{
        
        $this->date = $date;
        return $this;
    }

    public function getTitle(): ?string{
        return $this->title;
    }

    public function setTitle(string $title): self{
        
        $this->title = $title;
        return $this;
    }

    public function getAuthor(): ?string{
        return $this->author;
    }

    public function setAuthor(string $author): self{
        
        $this->author = $author;
        return $this;
    }

    public function getContent(): ?string{
        return $this->content;
    }

    public function setContent(string $content): self{
        
        $this->content = $content;
        return $this;
    }

    public function getPublished(): ?bool{
        return $this->published;
    }

    public function setPublished(bool $published): self{
        
        $this->published = $published;
        return $this;
    }

    public function getImage(): ?Image
    {
        return $this->image;
    }

    public function setImage(?Image $image): self
    {
        $this->image = $image;

        return $this;
    }
}
