<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    collectionOperations: ['get', 'post'],
    itemOperations: ['get', 'patch', 'delete']
)]
#[ORM\Entity]
class Note
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column]
    #[Assert\Range(min: 1, max: 5)]
    private int $value;

    #[ORM\ManyToOne(targetEntity: 'User', inversedBy: 'notes')]
    private User $student;

    #[ORM\ManyToOne(targetEntity: 'Course', inversedBy: 'notes')]
    private Course $course;

    public function getId(): int
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

}