<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testLogin(): void
    {
        $crawler = $this->client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Sf Project Starter');
        $this->assertSelectorExists('form#login-form');
    }

    public function testLoginWithError(): void
    {
        $crawler = $this->client->request('GET', '/login');

        $form = $crawler->filter('form#login-form')->form([
            '_username' => 'invalid_user',
            '_password' => 'invalid_password',
        ]);

        $this->client->submit($form);

        $this->assertResponseStatusCodeSame(302); // Expecting a redirect to the login page
        $this->client->followRedirect();

        $this->assertSelectorExists('.alert-danger');
    }

    public function testLoginWithCorrectCredentials(): void
    {
        $crawler = $this->client->request('GET', '/login');

        $form = $crawler->filter('form#login-form')->form([
            '_username' => 'admin@net.com', // Use the correct username
            '_password' => 'admin1234', // Use the correct password
        ]);

        $this->client->submit($form);

        $this->assertResponseStatusCodeSame(302); // Expecting a redirect after successful login
        $this->client->followRedirect();

        $this->assertSelectorNotExists('.alert-danger'); // Ensure no error message is displayed
        $this->assertSelectorExists('a[href="/logout"]'); // Ensure the logout link is present
    }

    public function testLogout(): void
    {
        $this->client->request('GET', '/logout');

        $this->assertResponseStatusCodeSame(302); // Assuming logout redirects
    }
}
