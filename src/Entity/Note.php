<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Common\Filter\SearchFilterInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiResource(
        collectionOperations: [
            'get' => ['security' => "is_granted('IS_AUTHENTICATED_FULLY')"],
            'post' => ['security' => "is_granted('ROLE_TEACHER')"],
        ],
        itemOperations: [
            'get' => ['security' => "is_granted('GET_ITEM', object)"],
            'delete' => ['security' => "is_granted('DELETE_ITEM', object)"],
            'patch' => ['security' => "is_granted('PATCH_ITEM', object)"],
        ],
        denormalizationContext: ['groups' => ['note:write']],
        normalizationContext: ['groups' => ['note:read']]
    ),
    ApiFilter(
        SearchFilter::class,
        properties: [
            'value' => SearchFilterInterface::STRATEGY_EXACT,
            'student.id' => SearchFilterInterface::STRATEGY_EXACT,
            'teacher.id' => SearchFilterInterface::STRATEGY_EXACT,
            'course.subject.id' => SearchFilterInterface::STRATEGY_EXACT,
            'course.semester.id' => SearchFilterInterface::STRATEGY_EXACT,
        ]
    ),
    ApiFilter(
        OrderFilter::class,
        properties: ['createdAt']
    )
]
#[ORM\Entity]
class Note
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface $id;

    #[Groups(['note:read', 'note:write'])]
    #[ORM\Column]
    #[Assert\Range(min: 1, max: 5)]
    #[Assert\NotBlank]
    private int $value;

    #[Groups(['note:read', 'note:write'])]
    #[ORM\Column(type: 'text', nullable: true)]
    private string $noteComment;

    #[Groups(['note:read', 'note:write'])]
    #[ORM\ManyToOne(inversedBy: 'studentNotes')]
    #[Assert\NotBlank]
    private User $student;

    #[Groups(['note:read', 'note:write'])]
    #[ORM\ManyToOne(inversedBy: 'teacherNotes')]
    #[Assert\NotBlank]
    private User $teacher;

    #[Groups(['note:read'])]
    #[ORM\Column(type: 'datetime')]
    private \DateTime $createdAt;

    #[Groups(['note:read', 'note:write'])]
    #[ORM\ManyToOne(inversedBy: 'notes')]
    #[Assert\NotBlank]
    private Course $course;

    #[Groups(['note:read', 'note:write'])]
    #[ORM\ManyToOne(inversedBy: 'notes')]
    #[Assert\NotBlank]
    private NoteType $noteType;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function setValue(int $value): void
    {
        $this->value = $value;
    }

    public function getNoteComment(): string
    {
        return $this->noteComment;
    }

    public function setNoteComment(string $noteComment): void
    {
        $this->noteComment = $noteComment;
    }

    public function getStudent(): User
    {
        return $this->student;
    }

    public function setStudent(User $student): void
    {
        $this->student = $student;
    }

    public function getTeacher(): User
    {
        return $this->teacher;
    }

    public function setTeacher(User $teacher): void
    {
        $this->teacher = $teacher;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function getCourse(): Course
    {
        return $this->course;
    }

    public function setCourse(Course $course): void
    {
        $this->course = $course;
    }

    public function getNoteType(): NoteType
    {
        return $this->noteType;
    }

    public function setNoteType(NoteType $noteType): void
    {
        $this->noteType = $noteType;
    }
}
