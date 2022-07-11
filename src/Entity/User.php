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
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiResource(
        collectionOperations: [
            'get',
            'post' => [
              'validation_groups' => ['Default', 'create'],
            ],
        ],
        itemOperations: ['get', 'patch', 'delete'],
        attributes: [
            'pagination_items_per_page' => 10,
            'formats' => ['json', 'jsonld', 'html', 'csv' => ['text/csv']],
        ],
        denormalizationContext: ['groups' => ['user:write']],
        normalizationContext: ['groups' => ['user:read']],
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
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface $id;

    #[Groups(['user:read', 'user:write', 'course:read', 'note:read'])]
    #[ORM\Column]
    #[Assert\NotBlank]
    private string $firstName;

    #[Groups(['user:read', 'user:write', 'course:read', 'note:read'])]
    #[ORM\Column]
    #[Assert\NotBlank]
    private string $lastName;

    #[Groups(['user:read', 'user:write', 'course:read'])]
    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Email]
    private string $email;

    #[Groups(['teacher:read', 'user:write'])]
    #[ORM\Column(length: 50, nullable: true)]
    private ?string $phoneNumber = null;

    #[Groups(['user:read', 'admin:write', 'course:read'])]
    #[ORM\Column(type: 'json')]
    #[Assert\NotBlank(groups: ['create'])]
    private array $roles = [];

    #[ORM\Column]
    private string $password;

    #[Groups(['user:write'])]
    #[SerializedName('password')]
    #[Assert\NotBlank(groups: ['create'])]
    private ?string $plainPassword = null;

    #[ORM\OneToMany(mappedBy: 'student', targetEntity: Note::class)]
    private Collection $studentNotes;

    #[ORM\OneToMany(mappedBy: 'teacher', targetEntity: Note::class)]
    private Collection $teacherNotes;

    #[ORM\OneToMany(mappedBy: 'teacher', targetEntity: Course::class)]
    private Collection $courses;

    #[Groups(['user:read', 'user:write'])]
    #[ORM\ManyToOne(inversedBy: 'students')]
    private SchoolClass $schoolClass;

    public function __construct()
    {
        $this->studentNotes = new ArrayCollection();
        $this->teacherNotes = new ArrayCollection();
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

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): void
    {
        $this->phoneNumber = $phoneNumber;
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

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
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
        $this->plainPassword = null;
    }
}
