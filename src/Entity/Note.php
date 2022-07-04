<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Common\Filter\SearchFilterInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiResource(
        collectionOperations: ['get', 'post'],
        itemOperations: ['get', 'patch', 'delete'],
    ),
    ApiFilter(
        SearchFilter::class,
        properties: [
            'student.id' => SearchFilterInterface::STRATEGY_EXACT,
            'course.subject.id' => SearchFilterInterface::STRATEGY_EXACT,
            'course.teacher.id' => SearchFilterInterface::STRATEGY_EXACT,
        ]
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

    #[ORM\Column]
    #[Assert\Range(min: 1, max: 5)]
    private int $value;

    #[ORM\ManyToOne(inversedBy: 'notes')]
    private User $student;

    #[ORM\ManyToOne(inversedBy: 'notes')]
    private Course $course;

    #[ORM\ManyToOne(inversedBy: 'notes')]
    private NoteType $noteType;

    #[ORM\OneToMany(mappedBy: 'note', targetEntity: NoteComment::class)]
    private Collection $noteComments;

    public function __construct()
    {
        $this->noteComments = new ArrayCollection();
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
}
