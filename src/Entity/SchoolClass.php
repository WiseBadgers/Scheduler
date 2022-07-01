<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    collectionOperations: ['get', 'post'],
    itemOperations: ['get', 'patch', 'delete'],
    shortName: 'Class'
)]
#[ORM\Entity]
class SchoolClass
{
    #[Groups(['course.read'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[Groups(['course.read'])]
    #[ORM\Column]
    #[Assert\NotBlank]
    private string $name;

    #[ORM\OneToMany(mappedBy: 'schoolClass', targetEntity: 'User')]
    private iterable $students;

    #[ORM\OneToMany(mappedBy: 'schoolClass', targetEntity: 'Course')]
    private iterable $courses;

    public function getId(): int
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

    public function getStudents(): iterable|ArrayCollection
    {
        return $this->students;
    }

    public function setStudents(iterable|ArrayCollection $students): void
    {
        $this->students = $students;
    }

    public function getCourses(): iterable|ArrayCollection
    {
        return $this->courses;
    }

    public function setCourses(iterable|ArrayCollection $courses): void
    {
        $this->courses = $courses;
    }

}