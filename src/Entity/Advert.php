<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use OC\PlatformBundle\Entity\Category;

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
     * @ORM\ManyToMany(targetEntity="App\Entity\Category", cascade={"persist"})
     */
    private $categories;


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
        $this->categories = new ArrayCollection();

    }

    // Notez le singulier, on ajoute une seule catégorie à la fois
    public function addCategory(Category $category)
    {
        // Ici, on utilise l'ArrayCollection vraiment comme un tableau
        $this->categories[] = $category;
        }

        public function removeCategory(Category $category)
        {
        // Ici on utilise une méthode de l'ArrayCollection, pour supprimer la catégorie en argument
        $this->categories->removeElement($category);
        }

        // Notez le pluriel, on récupère une liste de catégories ici !
        public function getCategories()
        {
        return $this->categories;
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
