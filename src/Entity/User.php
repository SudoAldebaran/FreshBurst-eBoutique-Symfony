<?php

   namespace App\Entity;

   use App\Repository\UserRepository;
   use Doctrine\ORM\Mapping as ORM;
   use Symfony\Component\Security\Core\User\UserInterface;
   use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
   use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

   #[ORM\Entity(repositoryClass: UserRepository::class)]
   #[UniqueEntity(fields: ['email'], message: 'Cet email est déjà utilisé.')]
   class User implements UserInterface, PasswordAuthenticatedUserInterface
   {
       #[ORM\Id]
       #[ORM\GeneratedValue]
       #[ORM\Column]
       private ?int $id = null;

       #[ORM\Column(length: 180, unique: true)]
       private ?string $email = null;

       #[ORM\Column(length: 255)]
       private ?string $password = null;

       #[ORM\Column(length: 255)]
       private ?string $name = null;

       #[ORM\Column(type: 'text', nullable: true)]
       private ?string $address = null;

       #[ORM\Column(type: 'json')]
       private array $roles = [];

       public function getId(): ?int
       {
           return $this->id;
       }

       public function getEmail(): ?string
       {
           return $this->email;
       }

       public function setEmail(string $email): static
       {
           $this->email = $email;
           return $this;
       }

       public function getPassword(): ?string
       {
           return $this->password;
       }

       public function setPassword(string $password): static
       {
           $this->password = $password;
           return $this;
       }

       public function getName(): ?string
       {
           return $this->name;
       }

       public function setName(string $name): static
       {
           $this->name = $name;
           return $this;
       }

       public function getAddress(): ?string
       {
           return $this->address;
       }

       public function setAddress(?string $address): static
       {
           $this->address = $address;
           return $this;
       }

       public function getRoles(): array
       {
           $roles = $this->roles;
           $roles[] = 'ROLE_USER'; // Tous les utilisateurs ont ROLE_USER par défaut
           return array_unique($roles);
       }

       public function setRoles(array $roles): static
       {
           $this->roles = $roles;
           return $this;
       }

       public function getSalt(): ?string
       {
           return null;
       }

       public function getUsername(): string
       {
           return $this->email;
       }

       public function eraseCredentials(): void
       {
       }

       public function getUserIdentifier(): string
       {
           return $this->email;
       }
   }