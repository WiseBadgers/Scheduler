<?php

declare(strict_types=1);

namespace App\Tests;

use App\Test\CustomApiTestCase;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;

class CourseTest extends CustomApiTestCase
{
    use ReloadDatabaseTrait;

    protected function setUp(): void
    {
        $this->client = $this->createClient();
        $this->teacher = $this->createUser('teacher@test.com', 'test', ['ROLE_TEACHER']);
        $this->semester = $this->createSemester('Winter 2022');
        $this->subject = $this->createSubject('history');
        $this->schoolClass = $this->createClass('3A');
    }

    public function testPostCourse(): void
    {
        // tests if anonymous user gets Unauthorized
        $this->client->request('POST', '/api/courses', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'teacher' => '/api/users/'.$this->teacher->getId(),
                'semester' => '/api/semesters/'.$this->semester->getId(),
                'subject' => '/api/subjects/'.$this->subject->getId(),
                'schoolClass' => '/api/classes/'.$this->schoolClass->getId(),
            ],
        ]);
        $this->assertResponseStatusCodeSame(401);

        // tests if logged-in user without ROLE_ADMIN gets Access Denied
        $this->createUserAndLogIn($this->client, 'test@test.com', 'test', ['ROLE_STUDENT']);
        $this->client->request('POST', '/api/courses', [
                'headers' => ['Content-Type' => 'application/json'],
                'json' => [
                    'teacher' => '/api/users/'.$this->teacher->getId(),
                    'semester' => '/api/semesters/'.$this->semester->getId(),
                    'subject' => '/api/subjects/'.$this->subject->getId(),
                    'schoolClass' => '/api/classes/'.$this->schoolClass->getId(),
                ],
            ]
        );
        $this->assertResponseStatusCodeSame(403);

        // tests if user with ROLE_ADMIN gets Success
        $this->createUserAndLogIn($this->client, 'admin@test.com', 'test', ['ROLE_ADMIN']);
        $this->client->request('POST', '/api/courses', [
           'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'teacher' => '/api/users/'.$this->teacher->getId(),
                'semester' => '/api/semesters/'.$this->semester->getId(),
                'subject' => '/api/subjects/'.$this->subject->getId(),
                'schoolClass' => '/api/classes/'.$this->schoolClass->getId(),
            ],
        ]);
        $this->assertResponseIsSuccessful();

        // tests if course can be created without teacher assigned
        $this->client->request('POST', '/api/courses', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'semester' => '/api/semesters/'.$this->semester->getId(),
                'subject' => '/api/subjects/'.$this->subject->getId(),
                'schoolClass' => '/api/classes/'.$this->schoolClass->getId(),
            ],
        ]);
        $this->assertResponseStatusCodeSame(422);

        // tests if course can be created without semester assigned
        $this->client->request('POST', '/api/courses', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'teacher' => '/api/users/'.$this->teacher->getId(),
                'subject' => '/api/subjects/'.$this->subject->getId(),
                'schoolClass' => '/api/classes/'.$this->schoolClass->getId(),
            ],
        ]);
        $this->assertResponseStatusCodeSame(422);

        // tests if course can be created without subject assigned
        $this->client->request('POST', '/api/courses', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'teacher' => '/api/users/'.$this->teacher->getId(),
                'semester' => '/api/semesters/'.$this->semester->getId(),
                'schoolClass' => '/api/classes/'.$this->schoolClass->getId(),
            ],
        ]);
        $this->assertResponseStatusCodeSame(422);

        // tests if course can be created without a class assigned
        $this->client->request('POST', '/api/courses', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'teacher' => '/api/users/'.$this->teacher->getId(),
                'semester' => '/api/semesters/'.$this->semester->getId(),
                'subject' => '/api/subjects/'.$this->subject->getId(),
            ],
        ]);
        $this->assertResponseStatusCodeSame(422);
    }

    public function testGetCourse(): void
    {
        $course = $this->createCourse(
            $this->teacher,
            $this->semester,
            $this->schoolClass,
            $this->subject,
        );

        // tests if anonymous user gets Unauthorized
        $this->client->request('GET', '/api/courses/'.$course->getId());
        $this->assertResponseStatusCodeSame(401);

        // tests if logged-in user gets Success
        $this->createUserAndLogIn($this->client, 'test@test.com', 'test', ['ROLE_STUDENT']);
        $this->client->request('GET', '/api/courses/'.$course->getId());
        $this->assertResponseIsSuccessful();

        // tests if response contains proper data
        $responseArray = $this->client->getResponse()->toArray();
        $this->assertArrayHasKey('teacher', $responseArray);
        $this->assertArrayHasKey('semester', $responseArray);
        $this->assertArrayHasKey('subject', $responseArray);
        $this->assertArrayHasKey('schoolClass', $responseArray);
    }

    public function testDeleteCourse(): void
    {
        $course = $this->createCourse(
            $this->teacher,
            $this->semester,
            $this->schoolClass,
            $this->subject,
        );

        // tests if anonymous user gets Unauthorized
        $this->client->request('DELETE', '/api/courses/'.$course->getId());
        $this->assertResponseStatusCodeSame(401);

        // tests if user without ROLE_ADMIN gets Access Denied
        $this->createUserAndLogIn($this->client, 'test@test.com', 'test', ['ROLE_TEACHER']);
        $this->client->request('DELETE', '/api/courses/'.$course->getId());
        $this->assertResponseStatusCodeSame(403);

        // tests if user with ROLE_ADMIN gets Success
        $this->createUserAndLogIn($this->client, 'admin@test.com', 'test', ['ROLE_ADMIN']);
        $this->client->request('DELETE', '/api/courses/'.$course->getId());
        $this->assertResponseIsSuccessful();

        // tests if a course was properly deleted
        $this->client->request('GET', '/api/courses/'.$course->getId());
        $this->assertResponseStatusCodeSame(404);
    }

    public function testPatchCourse(): void
    {
        $course = $this->createCourse(
            $this->teacher,
            $this->semester,
            $this->schoolClass,
            $this->subject,
        );
        $semester = $this->createSemester('Summer 2025');

        // tests if anonymous user gets Unauthorized
        $this->client->request('PATCH', '/api/courses/'.$course->getId(), [
            'headers' => ['Content-Type' => 'application/merge-patch+json'],
            'json' => ['semester' => '/api/semesters/'.$semester->getId()],
        ]);
        $this->assertResponseStatusCodeSame(401);

        // tests if user without ROLE_ADMIN gets Access Denied
        $this->createUserAndLogIn($this->client, 'test@test.com', 'test', ['ROLE_TEACHER']);
        $this->client->request('PATCH', '/api/courses/'.$course->getId(), [
            'headers' => ['Content-Type' => 'application/merge-patch+json'],
            'json' => ['semester' => '/api/semesters/'.$semester->getId()],
        ]);
        $this->assertResponseStatusCodeSame(403);

        // tests if user with ROLE_ADMIN gets Success
        $this->createUserAndLogIn($this->client, 'admin@test.com', 'test', ['ROLE_ADMIN']);
        $this->client->request('PATCH', '/api/courses/'.$course->getId(), [
            'headers' => ['Content-Type' => 'application/merge-patch+json'],
            'json' => ['semester' => '/api/semesters/'.$semester->getId()],
        ]);
        $this->assertResponseIsSuccessful();
    }
}
