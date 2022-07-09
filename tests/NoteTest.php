<?php

declare(strict_types=1);

namespace App\Tests;

use App\Entity\Note;
use App\Test\CustomApiTestCase;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;

class NoteTest extends CustomApiTestCase
{
    use ReloadDatabaseTrait;

    protected function setUp(): void
    {
        $this->client = $this->createClient();
        $this->student1 = $this->createUser('student1@test.com', 'test', ['ROLE_STUDENT']);
        $this->student2 = $this->createUser('student2@test.com', 'test', ['ROLE_STUDENT']);
        $this->teacher1 = $this->createUser('teacher1@test.com', 'test', ['ROLE_TEACHER']);
        $this->teacher2 = $this->createUser('teacher2@test.com', 'test', ['ROLE_TEACHER']);
    }

    public function testCreateNote()
    {
        // tests if unauthorized user gets Unauthorized
        $this->client->request('POST', '/api/notes', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'value' => 2,
                'student' => '/api/users/'.$this->student1->getId(),
                'teacher' => '/api/users/'.$this->teacher1->getId(),
//                'course' => '/api/courses/'.$this->course->getId(),
//                'noteType' => 'api/note_types/'.$this->noteType->getId(),
            ],
        ]);
        $this->assertResponseStatusCodeSame(401);

        // tests if student gets Access Denied
        $this->logIn($this->client, 'student1@test.com', 'test');
        $this->client->request('POST', '/api/notes', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'value' => 2,
                'student' => '/api/users/'.$this->student1->getId(),
                'teacher' => '/api/users/'.$this->teacher1->getId(),
//                'course' => '/api/courses/'.$this->course->getId(),
//                'noteType' => 'api/note_types/'.$this->noteType->getId(),
            ],
        ]);
        $this->assertResponseStatusCodeSame(403);

        // tests if teacher can create a note with Success
        $this->logIn($this->client, 'teacher1@test.com', 'test');
        $this->client->request('POST', '/api/notes', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'value' => 2,
                'student' => '/api/users/'.$this->student1->getId(),
                'teacher' => '/api/users/'.$this->teacher1->getId(),
//                'course' => '/api/courses/'.$this->course->getId(),
//                'noteType' => 'api/note_types/'.$this->noteType->getId(),
            ],
        ]);
        $this->assertResponseStatusCodeSame(201);
        $this->assertMatchesResourceItemJsonSchema(Note::class);

        // tests if Bad Request is thrown when value is not allowed
        $this->client->request('POST', '/api/notes', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'value' => null,
                'student' => '/api/users/'.$this->student1->getId(),
                'teacher' => '/api/users/'.$this->teacher1->getId(),
//                'course' => '/api/courses/'.$this->course->getId(),
//                'noteType' => 'api/note_types/'.$this->noteType->getId(),
            ],
        ]);
        $this->assertResponseStatusCodeSame(400);
    }

    public function testGetNote(): void
    {
        $note = $this->createNote($this->student1, $this->teacher1);

        // tests if an unauthorized user gets Unauthorized
        $this->client->request('GET', '/api/notes/'.$note->getId());
        $this->assertResponseStatusCodeSame(401);

        // tests if student who does not own the note gets Access Denied
        $this->logIn($this->client, 'student2@test.com', 'test');
        $this->client->request('GET', '/api/notes/'.$note->getId());
        $this->assertResponseStatusCodeSame(403);

        // tests if student who owns the note can fetch it with Success
        $this->logIn($this->client, 'student1@test.com', 'test');
        $this->client->request('GET', '/api/notes/'.$note->getId());
        $this->assertResponseStatusCodeSame(200);

        // tests if teacher who does not own the note gets Access Denied
        $this->logIn($this->client, 'teacher2@test.com', 'test');
        $this->client->request('GET', '/api/notes/'.$note->getId());
        $this->assertResponseStatusCodeSame(403);

        // tests if teacher who owns the note can fetch it with Success
        $this->logIn($this->client, 'teacher1@test.com', 'test');
        $this->client->request('GET', '/api/notes/'.$note->getId());
        $this->assertResponseStatusCodeSame(200);
    }

    public function testDeleteNote(): void
    {
        $note = $this->createNote($this->student2, $this->teacher2);

        // tests if unauthorized user gets Unauthorized
        $this->client->request('DELETE', '/api/notes/'.$note->getId());
        $this->assertResponseStatusCodeSame(401);

        // tests if student gets Access Denied
        $this->logIn($this->client, 'student1@test.com', 'test');
        $this->client->request('DELETE', '/api/notes/'.$note->getId());
        $this->assertResponseStatusCodeSame(403);

        // tests if teacher that does not own the note gets Access Denied
        $this->logIn($this->client, 'teacher1@test.com', 'test');
        $this->client->request('DELETE', '/api/notes/'.$note->getId());
        $this->assertResponseStatusCodeSame(403);

        // tests if teacher that owns the note can delete it with Success
        $this->logIn($this->client, 'teacher2@test.com', 'test');
        $this->client->request('DELETE', '/api/notes/'.$note->getId());
        $this->assertResponseStatusCodeSame(204);
    }

    public function testPatchNote(): void
    {
        $note = $this->createNote($this->student1, $this->teacher1);

        // tests if an unauthorized user gets Unauthorized while trying to patch a note
        $this->client->request('PATCH', '/api/notes/'.$note->getId(), [
            'headers' => ['Content-Type' => 'application/merge-patch+json'],
            'json' => ['value' => 4],
        ]);
        $this->assertResponseStatusCodeSame(401);

        // tests if a student gets Access Denied while trying to patch a note
        $this->logIn($this->client, 'student1@test.com', 'test');
        $this->client->request('PATCH', '/api/notes/'.$note->getId(), [
            'headers' => ['Content-Type' => 'application/merge-patch+json'],
            'json' => ['value' => 4],
        ]);
        $this->assertResponseStatusCodeSame(403);

        // tests if a teacher that does not own the note gets Access Denied while trying to patch a note
        $this->logIn($this->client, 'teacher2@test.com', 'test');
        $this->client->request('PATCH', '/api/notes/'.$note->getId(), [
            'headers' => ['Content-Type' => 'application/merge-patch+json'],
            'json' => ['value' => 4],
        ]);
        $this->assertResponseStatusCodeSame(403);

        // tests if a teacher that owns the note gets Success
        $this->logIn($this->client, 'teacher1@test.com', 'test');
        $this->client->request('PATCH', '/api/notes/'.$note->getId(), [
            'headers' => ['Content-Type' => 'application/merge-patch+json'],
            'json' => ['value' => 4],
        ]);
        $this->assertResponseStatusCodeSame(200);
    }
}
