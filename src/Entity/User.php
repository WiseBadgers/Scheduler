<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Common\Filter\SearchFilterInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/** Users resource */
#[
    ApiResource(
        collectionOperations: ['get', 'post'],
        itemOperations: ['get', 'patch', 'delete'],
        attributes: ['pagination_items_per_page' => 10],
        normalizationContext: ['groups' => 'user.read']
    ),
    ApiFilter(
        SearchFilter::class,
        properties: [
            'schoolClass.id' => SearchFilterInterface::STRATEGY_EXACT,
            'roles' => SearchFilterInterface::STRATEGY_PARTIAL
        ],
    ),
    ApiFilter(
        OrderFilter::class,
        properties: ['firstName', 'lastName']
    )
]
#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('user.read')]
    private int $id;

    #[ORM\Column]
    #[Groups('user.read')]
    #[Assert\NotBlank]
    private string $firstName;

    #[ORM\Column]
    #[Groups('user.read')]
    #[Assert\NotBlank]
    private string $lastName;

    #[ORM\Column(length: 180, unique: true)]
    #[Groups('user.read')]
    #[Assert\Email]
    private string $email;

    #[ORM\Column(type: 'json')]
    #[Groups('user.read')]
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
        return $this->roles;
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
