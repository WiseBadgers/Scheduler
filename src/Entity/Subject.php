<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints AS Assert;

#[ApiResource(
    collectionOperations: ['get', 'post'],
    itemOperations: ['get', 'patch', 'delete']
)]
#[ORM\Entity]
class Subject
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column]
    #[Assert\NotBlank]
    private string $name;

    #[ORM\OneToMany(mappedBy: 'subject', targetEntity: 'Course')]
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

    public function getCourses(): iterable|ArrayCollection
    {
        return $this->courses;
    }

    public function setCourses(iterable|ArrayCollection $courses): void
    {
        $this->courses = $courses;
    }

}