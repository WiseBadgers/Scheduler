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
use Symfony\Component\Serializer\Annotation\Groups;

#[
    ApiResource(
        collectionOperations: ['get', 'post'],
        itemOperations: ['get', 'patch', 'delete'],
        normalizationContext: ['groups' => 'course.read']
    ),
    ApiFilter(
        SearchFilter::class,
        properties: [
            'teacher.id' => SearchFilterInterface::STRATEGY_EXACT,
            'semester.id' => SearchFilterInterface::STRATEGY_EXACT,
            'subject.id' => SearchFilterInterface::STRATEGY_EXACT,
            'schoolClass.id' => SearchFilterInterface::STRATEGY_EXACT,
        ]
    )
]
#[ORM\Entity]
class Course
{
    #[Groups(['course.read'])]
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface $id;

    #[Groups(['course.read'])]
    #[ORM\ManyToOne(inversedBy: 'courses')]
    private User $teacher;

    #[Groups(['course.read'])]
    #[ORM\ManyToOne(inversedBy: 'courses')]
    private Semester $semester;

    #[Groups(['course.read'])]
    #[ORM\ManyToOne(inversedBy: 'courses')]
    private Subject $subject;

    #[Groups(['course.read'])]
    #[ORM\ManyToOne(inversedBy: 'courses')]
    private SchoolClass $schoolClass;

    #[ORM\OneToMany(mappedBy: 'course', targetEntity: Note::class)]
    private Collection $notes;

    public function __construct()
    {
        $this->notes = new ArrayCollection();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getTeacher(): User
    {
        return $this->teacher;
    }

    public function setTeacher(User $teacher): void
    {
        $this->teacher = $teacher;
    }

    public function getSemester(): Semester
    {
        return $this->semester;
    }

    public function setSemester(Semester $semester): void
    {
        $this->semester = $semester;
    }

    public function getSubject(): Subject
    {
        return $this->subject;
    }

    public function setSubject(Subject $subject): void
    {
        $this->subject = $subject;
    }

    public function getSchoolClass(): SchoolClass
    {
        return $this->schoolClass;
    }

    public function setSchoolClass(SchoolClass $schoolClass): void
    {
        $this->schoolClass = $schoolClass;
    }

    public function getNotes(): iterable|ArrayCollection
    {
        return $this->notes;
    }
}
