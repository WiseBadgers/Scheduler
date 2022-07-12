<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    collectionOperations: [
        'get' => ['security' => "is_granted('IS_AUTHENTICATED_FULLY')"],
        'post' => ['security' => "is_granted('ROLE_ADMIN')"],
    ],
    itemOperations: [
        'get' => ['security' => "is_granted('IS_AUTHENTICATED_FULLY')"],
        'patch' => ['security' => "is_granted('ROLE_ADMIN')"],
        'delete' => ['security' => "is_granted('ROLE_ADMIN')"],
    ],
    shortName: 'Class',
)]
#[UniqueEntity(fields: ['name'])]
#[ORM\Entity]
class SchoolClass
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface $id;

    #[Groups(['user:read', 'course:read'])]
    #[ORM\Column(length: 2, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 2)]
    private string $name;

    #[ORM\OneToMany(mappedBy: 'schoolClass', targetEntity: User::class)]
    private Collection $students;

    #[ORM\OneToMany(mappedBy: 'schoolClass', targetEntity: Course::class)]
    private Collection $courses;

    public function __construct()
    {
        $this->students = new ArrayCollection();
        $this->courses = new ArrayCollection();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
