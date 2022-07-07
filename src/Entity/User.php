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
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiResource(
        collectionOperations: ['get', 'post'],
        itemOperations: ['get', 'patch', 'delete'],
        attributes: [
            'pagination_items_per_page' => 10,
            'formats' => ['json', 'jsonld', 'html', 'csv' => ['text/csv']],
        ],
        normalizationContext: ['groups' => 'user.read']
    ),
    ApiFilter(
        SearchFilter::class,
        properties: [
            'schoolClass.id' => SearchFilterInterface::STRATEGY_EXACT,
            'roles' => SearchFilterInterface::STRATEGY_PARTIAL,
        ],
    ),
    ApiFilter(
        OrderFilter::class,
        properties: ['firstName', 'lastName']
    )
]
#[UniqueEntity(fields: ['email'])]
#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[Groups(['user.read', 'course.read'])]
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface $id;

    #[Groups(['user.read', 'course.read'])]
    #[ORM\Column]
    #[Assert\NotBlank]
    private string $firstName;

    #[Groups(['user.read', 'course.read'])]
    #[ORM\Column]
    #[Assert\NotBlank]
    private string $lastName;

    #[Groups(['user.read', 'course.read'])]
    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Email]
    private string $email;

    #[Groups(['user.read', 'course.read'])]
    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\Column]
    #[Assert\NotBlank]
    private string $password;

    #[ORM\OneToMany(mappedBy: 'student', targetEntity: Note::class)]
    private Collection $notes;

    #[ORM\OneToMany(mappedBy: 'teacher', targetEntity: Course::class)]
    private Collection $courses;

    #[ORM\ManyToOne(inversedBy: 'students')]
    private SchoolClass $schoolClass;

    public function __construct()
    {
        $this->notes = new ArrayCollection();
        $this->courses = new ArrayCollection();
    }

    public function getId(): UuidInterface
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

    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
}
