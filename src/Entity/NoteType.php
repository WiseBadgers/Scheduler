<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    collectionOperations: [
        'get' => [
            'security' => "is_granted('IS_AUTHENTICATED_FULLY')",
            'security_message' => 'Only authenticated users can get collection of subjects.',
        ],
        'post' => [
            'security' => "is_granted('ROLE_ADMIN')",
            'security_message' => 'Only user with ROLE_ADMIN can create a subject',
        ],
    ],
    itemOperations: [
        'get' => [
            'security' => "is_granted('IS_AUTHENTICATED_FULLY')",
            'security_message' => 'Only authenticated users can get a subject.',
        ],
        'patch' => [
            'security' => "is_granted('ROLE_ADMIN')",
            'security_message' => 'Only user with ROLE_ADMIN can update a subject',
        ],
        'delete' => [
            'security' => "is_granted('ROLE_ADMIN')",
            'security_message' => 'Only user with ROLE_ADMIN can update a subject',
        ],
    ],
)]
#[ORM\Entity]
class NoteType
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface $id;

    #[Groups(['note:read'])]
    #[ORM\Column(length: 30)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 30)]
    private string $name;

    #[Groups(['note:read'])]
    #[ORM\Column]
    #[Assert\Choice(options: [1, 2, 3])]
    private int $weight;

    #[ORM\OneToMany(mappedBy: 'noteType', targetEntity: Note::class)]
    private Collection $notes;

    public function __construct()
    {
        $this->notes = new ArrayCollection();
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

    public function getWeight(): int
    {
        return $this->weight;
    }

    public function setWeight(int $weight): void
    {
        $this->weight = $weight;
    }
}
