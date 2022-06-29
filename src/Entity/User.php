<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    collectionOperations: ['get', 'post'],
    itemOperations: ['get', 'patch', 'delete']
)]
#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column]
    #[Assert\NotBlank]
    private string $firstName;

    #[ORM\Column]
    #[Assert\NotBlank]
    private string $lastName;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\Email]
    private string $email;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\Column]
    #[Assert\NotBlank]
    private string $password;

    #[ORM\OneToMany(mappedBy: 'student', targetEntity: 'Note')]
    private iterable $notes;

    #[ORM\OneToMany(mappedBy: 'teacher', targetEntity: 'Course')]
    private iterable $courses;

    #[ORM\ManyToOne(targetEntity: 'SchoolClass', inversedBy: 'students')]
    private SchoolClass $schoolClass;

    public function getId(): int
    {
        return $this->id;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getNotes(): iterable|ArrayCollection
    {
        return $this->notes;
    }

    public function setNotes(iterable|ArrayCollection $notes): void
    {
        $this->notes = $notes;
    }

    public function getCourses(): iterable|ArrayCollection
    {
        return $this->courses;
    }

    public function setCourses(iterable|ArrayCollection $courses): void
    {
        $this->courses = $courses;
    }

    public function getSchoolClass(): SchoolClass
    {
        return $this->schoolClass;
    }

    public function setSchoolClass(SchoolClass $schoolClass): void
    {
        $this->schoolClass = $schoolClass;
    }

    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

}
