<?php

declare(strict_types=1);

namespace App\Tests;

use App\Test\CustomApiTestCase;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;

class NoteTypeTest extends CustomApiTestCase
{
    use ReloadDatabaseTrait;

    public function testPostNoteType(): void
    {
        $client = self::createClient();

        // tests if unauthorized user gets Unauthorized
        $client->request('POST', '/api/note_types', [
            'json' => [
                'name' => 'Exam',
                'weight' => 3,
            ],
        ]);
        $this->assertResponseStatusCodeSame(401);

        // tests if user without ROLE_ADMIN gets Access Denied
        $this->createUserAndLogIn($client, 'not-admin@test.com', 'test', []);
        $client->request('POST', '/api/note_types', [
            'json' => [
                'name' => 'Exam',
                'weight' => 3,
            ],
        ]);
        $this->assertResponseStatusCodeSame(403);

        // tests if user with ROLE_ADMIN gets Success
        $this->createUserAndLogIn($client, 'admin@test.com', 'test', ['ROLE_ADMIN']);
        $client->request('POST', '/api/note_types', [
            'json' => [
                'name' => 'Exam',
                'weight' => 3,
            ],
        ]);
        $this->assertResponseIsSuccessful();

        // tests if name that contains more than 30 chars can be added
        $client->request('POST', '/api/note_types', [
            'json' => [
                'name' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
                'weight' => 3,
            ],
        ]);
        $this->assertResponseStatusCodeSame(422);

        // tests if weight can be bigger than 3
        $client->request('POST', '/api/note_types', [
            'json' => [
                'name' => 'Exam',
                'weight' => 4,
            ],
        ]);
        $this->assertResponseStatusCodeSame(422);
    }

    public function testGetNoteType(): void
    {
        $client = self::createClient();
        $noteType = $this->createNoteType('Exam', 3);

        // tests if anonymous user gets Unauthorized
        $client->request('GET', '/api/note_types/'.$noteType->getId());
        $this->assertResponseStatusCodeSame(401);

        // tests if logged-in user gets Success
        $this->createUserAndLogIn($client, 'test@test.com', 'test', []);
        $client->request('GET', '/api/note_types/'.$noteType->getId());
        $this->assertResponseIsSuccessful();
    }

    public function testDeleteNoteType(): void
    {
        $client = $this->createClient();
        $noteType = $this->createNoteType('Exam', 3);

        // tests if anonymous user gets Unauthorized
        $client->request('DELETE', '/api/note_types/'.$noteType->getId());
        $this->assertResponseStatusCodeSame(401);

        // tests if user without ROLE_ADMIN gets Access Denied
        $this->createUserAndLogIn($client, 'test@test.com', 'test', []);
        $client->request('DELETE', '/api/note_types/'.$noteType->getId());
        $this->assertResponseStatusCodeSame(403);

        // tests if user with ROLE_ADMIN gets Success
        $this->createUserAndLogIn($client, 'admin@test.com', 'test', ['ROLE_ADMIN']);
        $client->request('DELETE', '/api/note_types/'.$noteType->getId());
        $this->assertResponseIsSuccessful();
    }

    public function testPatchNoteType(): void
    {
        $client = $this->createClient();
        $noteType = $this->createNoteType('Exam', 3);

        // tests if anonymous user gets Unauthorized
        $client->request('PATCH', '/api/note_types/'.$noteType->getId(), [
            'headers' => ['Content-Type' => 'application/merge-patch+json'],
            'json' => [
                'name' => 'Test',
                'weight' => 1,
            ],
        ]);
        $this->assertResponseStatusCodeSame(401);

        // tests if user without ROLE_ADMIN gets Access Denied
        $this->createUserAndLogIn($client, 'test@test.com', 'test', []);
        $client->request('PATCH', '/api/note_types/'.$noteType->getId(), [
            'headers' => ['Content-Type' => 'application/merge-patch+json'],
            'json' => [
                'name' => 'Test',
                'weight' => 1,
            ],
        ]);
        $this->assertResponseStatusCodeSame(403);

        // tests if user with ROLE_ADMIN gets Success
        $this->createUserAndLogIn($client, 'admin@test.com', 'test', ['ROLE_ADMIN']);
        $client->request('PATCH', '/api/note_types/'.$noteType->getId(), [
            'headers' => ['Content-Type' => 'application/merge-patch+json'],
            'json' => [
                'name' => 'Test',
                'weight' => 1,
            ],
        ]);
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['name' => 'Test']);
        $this->assertJsonContains(['weight' => 1]);
    }
}