<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

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

    #[ORM\ManyToOne(targetEntity: 'Note', inversedBy: 'courses')]
    private Note $note;

}