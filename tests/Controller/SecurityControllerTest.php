<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    public function testLogin()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Sf Project Starter');
        $this->assertSelectorExists('form#login-form');
    }

    public function testLoginWithError()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $form = $crawler->filter('form#login-form')->form([
            '_username' => 'invalid_user',
            '_password' => 'invalid_password',
        ]);

        $client->submit($form);

        $this->assertResponseStatusCodeSame(302); // Expecting a redirect to the login page
        $client->followRedirect();

        $this->assertSelectorExists('.alert-danger');
    }

    public function testLoginWithCorrectCredentials()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $form = $crawler->filter('form#login-form')->form([
            '_username' => 'admin@net.com', // Use the correct username
            '_password' => 'admin1234', // Use the correct password
        ]);

        $client->submit($form);

        $this->assertResponseStatusCodeSame(302); // Expecting a redirect after successful login
        $client->followRedirect();

        $this->assertSelectorNotExists('.alert-danger'); // Ensure no error message is displayed
        $this->assertSelectorExists('a[href="/logout"]'); // Ensure the logout link is present
    }

    public function testLogout()
    {
        $client = static::createClient();
        $client->request('GET', '/logout');

        $this->assertResponseStatusCodeSame(302); // Assuming logout redirects
    }
}