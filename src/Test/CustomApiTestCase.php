<?php

declare(strict_types=1);

namespace App\Test;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client;
use App\Entity\Note;
use App\Entity\NoteType;
use App\Entity\SchoolClass;
use App\Entity\Semester;
use App\Entity\Subject;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

class CustomApiTestCase extends ApiTestCase
{
    protected function createUser(string $email, string $password, array $roles): User
    {
        $user = new User();
        $user->setEmail($email);
        $user->setFirstName(substr($email, 0, strpos($email, '@')));
        $user->setLastName('test');
        $user->setRoles($roles);
        $hashedPassword = $this->getPasswordHasher()->hashPassword($user, $password);
        $user->setPassword($hashedPassword);
        $em = $this->getEntityManager();
        $em->persist($user);
        $em->flush();

        return $user;
    }

    protected function logIn(Client $client, string $email, string $password): void
    {
        $client->request('POST', 'login', [
                'headers' => ['Content-Type' => 'application/json'],
                'json' => [
                    'email' => $email,
                    'password' => $password,
                ],
            ]
        );
        $this->assertResponseStatusCodeSame(204);
    }

    protected function createUserAndLogIn(Client $client, string $email, string $password, array $roles): User
    {
        $user = $this->createUser($email, $password, $roles);
        $this->logIn($client, $email, $password);

        return $user;
    }

    protected function createNote($student, $teacher): Note
    {
        $note = new Note();
        $note->setValue(3);
        $note->setStudent($student);
        $note->setTeacher($teacher);
//        $note->setCourse($this->course);
//        $note->setNoteType($this->noteType);
        $em = $this->getEntityManager();
        $em->persist($note);
        $em->flush();

        return $note;
    }

    protected function createSubject(string $name): Subject
    {
        $subject = new Subject();
        $subject->setName($name);
        $em = $this->getEntityManager();
        $em->persist($subject);
        $em->flush();

        return $subject;
    }

    protected function createClass(string $name): SchoolClass
    {
        $class = new SchoolClass();
        $class->setName($name);
        $em = $this->getEntityManager();
        $em->persist($class);
        $em->flush();

        return $class;
    }

    protected function createSemester(string $name): Semester
    {
        $semester = new Semester();
        $semester->setName($name);
        $em = $this->getEntityManager();
        $em->persist($semester);
        $em->flush();

        return $semester;
    }

    protected function createNoteType($name, $weight): NoteType
    {
        $noteType = new NoteType();
        $noteType->setName($name);
        $noteType->setWeight($weight);
        $em = $this->getEntityManager();
        $em->persist($noteType);
        $em->flush();

        return $noteType;
    }

    protected function getPasswordHasher(): UserPasswordHasherInterface
    {
        $passwordHasherFactory = new PasswordHasherFactory([
           PasswordAuthenticatedUserInterface::class => ['algorithm' => 'auto'],
        ]);

        return new UserPasswordHasher($passwordHasherFactory);
    }

    protected function getEntityManager(): ObjectManager
    {
        return self::getContainer()->get('doctrine')->getManager();
    }
}
