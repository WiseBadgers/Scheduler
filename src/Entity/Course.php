<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Common\Filter\SearchFilterInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

#[
    ApiResource(
        collectionOperations: ['get', 'post'],
        itemOperations: ['get', 'patch', 'delete']
    ),
    ApiFilter(
        SearchFilter::class,
        properties: [
            'teacher.id' => SearchFilterInterface::STRATEGY_EXACT,
            'semester.id' => SearchFilterInterface::STRATEGY_EXACT,
            'subject.id' => SearchFilterInterface::STRATEGY_EXACT,
            'schoolClass.id' => SearchFilterInterface::STRATEGY_EXACT
        ]
    )
]
#[ORM\Entity]
class Course
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\ManyToOne(targetEntity: 'User', inversedBy: 'courses')]
    private User $teacher;

    #[ORM\ManyToOne(targetEntity: 'Semester', inversedBy: 'courses')]
    private Semester $semester;

    #[ORM\ManyToOne(targetEntity: 'Subject', inversedBy: 'courses')]
    private Subject $subject;

    #[ORM\ManyToOne(targetEntity: 'SchoolClass', inversedBy: 'courses')]
    private SchoolClass $schoolClass;

    #[ORM\OneToMany(mappedBy: 'course', targetEntity: 'Note')]
    private iterable $notes;

    public function getId(): int
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

    public function setNotes(iterable|ArrayCollection $notes): void
    {
        $this->notes = $notes;
    }

}