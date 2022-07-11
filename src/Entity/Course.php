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
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiResource(
        collectionOperations: [
            'get' => [
                'security' => "is_granted('IS_AUTHENTICATED_FULLY')",
                'security_message' => 'Only authenticated users can get collection of subjects.',
            ],
            'post' => [
                'validation_groups' => ['Default', 'create'],
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
        denormalizationContext: ['groups' => ['course:write']],
        normalizationContext: ['groups' => ['course:read']]
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
    #[Groups(['course:read'])]
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface $id;

    #[Groups(['course:read', 'course:write'])]
    #[ORM\ManyToOne(inversedBy: 'courses')]
    #[Assert\NotBlank(groups: ['create'])]
    private User $teacher;

    #[Groups(['course:read', 'course:write', 'note:read'])]
    #[ORM\ManyToOne(inversedBy: 'courses')]
    #[Assert\NotBlank(groups: ['create'])]
    private Semester $semester;

    #[Groups(['course:read', 'course:write', 'note:read'])]
    #[ORM\ManyToOne(inversedBy: 'courses')]
    #[Assert\NotBlank(groups: ['create'])]
    private Subject $subject;

    #[Groups(['course:read', 'course:write'])]
    #[ORM\ManyToOne(inversedBy: 'courses')]
    #[Assert\NotBlank(groups: ['create'])]
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
