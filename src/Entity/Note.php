<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Common\Filter\SearchFilterInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiResource(
        collectionOperations: [
            'get' => [
                'security' => "is_granted('IS_AUTHENTICATED_FULLY')",
                'security_message' => 'You need to be logged-in user to access this resource.',
            ],
            'post' => [
                'security' => "is_granted('ROLE_TEACHER')",
                'security_message' => 'Only user with ROLE_TEACHER can create a note.',
            ],
        ],
        itemOperations: [
            'get' => [
                'security' => "is_granted('GET_ITEM', object)",
                'security_message' => 'Only user with ROLE_TEACHER who gave a note or ROLE_STUDENT who owns the note can get the note.',
            ],
            'delete' => [
                'security' => "is_granted('DELETE_ITEM', object)",
                'security_message' => 'Only user with ROLE TEACHER who gave a note can delete a note.',
            ],
            'patch' => [
                'security' => "is_granted('PATCH_ITEM', object)",
                'security_message' => 'Only user with ROLE_TEACHER who gave a note can update a note.',
            ],
        ],
        denormalizationContext: ['groups' => ['note:write']],
        normalizationContext: ['groups' => ['note:read']]
    ),
    ApiFilter(
        SearchFilter::class,
        properties: [
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

    // TODO: Add it to serialization group note:write and add NotBlank validation
    #[Groups(['note:read'])]
    #[ORM\ManyToOne(inversedBy: 'notes')]
    private Course $course;

    // TODO: Add it to serialization group note:write and add NotBlank validation
    #[Groups(['note:read'])]
    #[ORM\ManyToOne(inversedBy: 'notes')]
    private NoteType $noteType;

    #[Groups(['note:read', 'note:write'])]
    #[ORM\OneToMany(
        mappedBy: 'note',
        targetEntity: NoteComment::class,
        cascade: ['persist', 'remove'],
        orphanRemoval: true)
    ]
    #[Assert\Valid]
    private Collection $noteComments;

    public function __construct()
    {
        $this->noteComments = new ArrayCollection();
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

    public function getNoteComments(): iterable
    {
        return $this->noteComments;
    }

    public function addNoteComment(NoteComment $noteComment): self
    {
        if (!$this->noteComments->contains($noteComment)) {
            $this->noteComments[] = $noteComment;
            $noteComment->setNote($this);
        }

        return $this;
    }

    public function removeNoteComment(NoteComment $noteComment): self
    {
        if ($this->noteComments->contains($noteComment)) {
            $this->noteComments->removeElement($noteComment);
        }

        return $this;
    }
}
