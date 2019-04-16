<?php
// App\Entity/AdvertSkill.php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Advert;
use App\Entity\Skill;

/**
* @ORM\Entity
* @ORM\Table(name="oc_advert_skill")
*/
class AdvertSkill{
    /**
    * @ORM\Column(name="id", type="integer")
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="AUTO")
    */
    private $id;

    /**
    * @ORM\Column(name="level", type="string", length=255)
    */
    private $level;

    /**
    * @ORM\ManyToOne(targetEntity="App\Entity\Advert")
    * @ORM\JoinColumn(nullable=false)
    */
    private $advert;

    /**
    * @ORM\ManyToOne(targetEntity="App\Entity\Skill")
    * @ORM\JoinColumn(nullable=false)
    */
    private $skill;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLevel(): ?string
    {
        return $this->level;
    }

    public function setLevel(string $level): self
    {
        $this->level = $level;

        return $this;
    }

    public function getAdvert(): ?Advert
    {
        return $this->advert;
    }

    public function setAdvert(?Advert $advert): self
    {
        $this->advert = $advert;

        return $this;
    }

    public function getSkill(): ?Skill
    {
        return $this->skill;
    }

    public function setSkill(?Skill $skill): self
    {
        $this->skill = $skill;

        return $this;
    }

    
}