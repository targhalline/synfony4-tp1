<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;


    /**
    * @ORM\Entity(repositoryClass="App\Entity\UserRepository")
    */
    class User implements UserInterface
    {
        /**
        * @ORM\Column(name="id", type="integer")
        * @ORM\Id
        * @ORM\GeneratedValue(strategy="AUTO")
        */
        private $id;

        /**
        * @ORM\Column(name="username", type="string", length=50, unique=true)
        */
        private $username;

        /**
        * @ORM\Column(name="password", type="string", length=255)
        */
        private $password;

        /**
        * @ORM\Column(name="salt", type="string", length=255)
        */
        private $salt;

        /**
        * @ORM\Column(name="roles", type="array")
        */
        private $roles = array();

        public function getId(): ?int
        {
            return $this->id;
        }

        public function getUsername(): ?string
        {
            return $this->username;
        }

        public function setUsername(string $username): self
        {
            $this->username = $username;

            return $this;
        }

        public function getPassword(): ?string
        {
            return $this->password;
        }

        public function setPassword(string $password): self
        {
            $this->password = $password;
            return $this;
        }

        public function getSalt(): ?string
        {
            return $this->salt;
        }

        public function setSalt(string $salt): self
        {
            $this->salt = $salt;

        return $this;
        }

        public function getRoles(): ?array
        {
            return $this->roles;
        }

        public function setRoles(array $roles): self
        {
            $this->roles = $roles;
            return $this;
        }

        public function eraseCredentials()
        {

        }
    }
