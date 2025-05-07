<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegistrationControllerTest extends WebTestCase
{
    private EntityManagerInterface $entityManager;
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = $this->client->getContainer()->get('doctrine')->getManager();
    }

    private function logIn(): void
    {
        $crawler = $this->client->request('GET', '/login');

        $form = $crawler->selectButton('Sign In')->form([
            '_username' => 'admin@net.com',
            '_password' => 'admin1234',
        ]);

        $this->client->submit($form);
        $this->client->followRedirect();
    }

    public function testRegisterUser(): void
    {
        $this->logIn();

        $crawler = $this->client->request('GET', '/register');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form#register-form');

        $form = $crawler->selectButton('Register')->form([
            'registration_form[firstName]' => 'John',
            'registration_form[lastName]' => 'Doe',
            'registration_form[email]' => 'john.doe@example.com',
            'registration_form[plainPassword]' => 'password123',
            'registration_form[roleSelection]' => 'ROLE_USER',
        ]);

        $this->client->submit($form);

        $this->assertResponseStatusCodeSame(302); // Expecting a redirect after successful registration
        $this->client->followRedirect();

        $this->assertSelectorExists('.alert-success'); // Ensure success message is displayed

        // Verify the user was created in the database
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => 'john.doe@example.com']);

        $this->assertNotNull($user);
        $this->assertEquals('John', $user->getFirstName());
        $this->assertEquals('Doe', $user->getLastName());
    }

    public function testEditUser(): void
    {
        $this->logIn();

        // Create a user to edit
        $user = new User();
        $user->setFirstName('John');
        $user->setLastName('Doe');
        $user->setEmail('jane.doe@example.com');
        $user->setPassword('password123');
        $user->setRoles(['ROLE_USER']);
        $user->setActive(true);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $crawler = $this->client->request('GET', '/admin/user/edit/' . $user->getId());

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form#register-form');

        $form = $crawler->selectButton('Save')->form([
            'registration_form[firstName]' => 'Jane',
            'registration_form[lastName]' => 'Smith',
            'registration_form[email]' => 'jane.smith@example.com',
        ]);

        $this->client->submit($form);

        $this->entityManager->flush();
        $this->assertResponseStatusCodeSame(302); // Expecting a redirect after successful edit
        $this->client->followRedirect();

        $this->assertSelectorExists('.alert-success'); // Ensure success message is displayed

        // Verify the user was updated in the database
        $updatedUser = $this->entityManager->getRepository(User::class)->find($user->getId());
        $this->assertNotNull($updatedUser);

        $this->entityManager->refresh($updatedUser);

        $this->assertEquals('Jane', $updatedUser->getFirstName());
        $this->assertEquals('Smith', $updatedUser->getLastName());
        $this->assertEquals('jane.smith@example.com', $updatedUser->getEmail());
    }

    protected function tearDown(): void
    {
        // Clean up the database by removing the test records
        $userRepository = $this->entityManager->getRepository(User::class);

        // Remove the user created in testRegisterUser
        $user = $userRepository->findOneBy(['email' => 'john.doe@example.com']);
        if ($user) {
            $this->entityManager->remove($user);
        }

        // Remove the user created in testEditUser
        $user = $userRepository->findOneBy(['email' => 'jane.smith@example.com']);
        if ($user) {
            $this->entityManager->remove($user);
        }

        $this->entityManager->flush();

        $this->entityManager->close();

        parent::tearDown();
    }
}
