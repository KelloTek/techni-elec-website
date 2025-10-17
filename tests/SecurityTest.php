<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityTest extends WebTestCase
{
    // Test that the login page loads successfully
    public function testLoginPageLoadsSuccessfully(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form input[name="_username"]');
        $this->assertSelectorExists('form input[name="_password"]');
        $this->assertSelectorExists('form button[name="_submit"]');
    }

    // Test login with invalid credentials
    public function testLoginWithInvalidCredentials(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $form = $crawler->selectButton('_submit')->form([
            '_username' => 'invalid_user',
            '_password' => 'wrong_password',
        ]);

        $client->submit($form);

        $this->assertResponseRedirects('/login');
        $client->followRedirect();

        $this->assertSelectorExists('#error');
    }

    // Test login with valid credentials
    public function testLoginWithValidCredentials(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $form = $crawler->selectButton('_submit')->form([
            '_username' => 'admin@dev.com',
            '_password' => 'admin',
        ]);

        $client->submit($form);

        $this->assertResponseRedirects('/');
        $client->followRedirect();
    }

    // Test logout functionality
    public function testLogout(): void
    {
        $client = static::createClient();

        $userRepository = static::getContainer()->get('doctrine')->getRepository(\App\Entity\User::class);
        $user = $userRepository->findOneBy(['email' => 'admin@dev.com']);

        $client->loginUser($user);

        $client->request('GET', '/logout');
        $this->assertResponseRedirects('/profile');
    }

    // Test that the registration page loads successfully
    public function testRegisterPageLoadsSuccessfully(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/register');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form input[name="registration_form[name]"]');
        $this->assertSelectorExists('form input[name="registration_form[email]"]');
        $this->assertSelectorExists('form input[name="registration_form[phone]"]');
        $this->assertSelectorExists('form input[name="registration_form[address][line]"]');
        $this->assertSelectorExists('form input[name="registration_form[address][zipCode]"]');
        $this->assertSelectorExists('form input[name="registration_form[address][city]"]');
        $this->assertSelectorExists('form input[name="registration_form[plainPassword]"]');
        $this->assertSelectorExists('form input[name="registration_form[plainPassword]"]');
        $this->assertSelectorExists('form button[type="submit"]');
    }
}
