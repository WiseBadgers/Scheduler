<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    collectionOperations: ['get', 'post'],
    itemOperations: ['get', 'patch']
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
    private User $user;

    #[ORM\OneToMany(mappedBy: 'note', targetEntity: 'Course')]
    private iterable $courses;

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

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function getCourses(): iterable
    {
        return $this->courses;
    }

    public function setCourses(iterable $courses): void
    {
        $this->courses = $courses;
    }

}