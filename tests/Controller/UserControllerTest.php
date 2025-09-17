<?php

/**
 * This file is part of the MyProject package.
 *
 * (c) Your Name <your.email@example.com>
 *
 * Functional tests for the UserController.
 * These tests cover the edit user form and submission workflow.
 */

namespace App\Tests\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class UserControllerTest.
 *
 * Provides functional tests for user management actions in UserController.
 */
class UserControllerTest extends WebTestCase
{
    /**
     * Test that the edit form for the admin user is displayed successfully.
     *
     * @return void No value is returned, assertions validate response and form presence
     */
    public function testEditFormDisplayed(): void
    {
        $client = static::createClient();

        $admin = $this->createAdminUser();
        $client->loginUser($admin);

        $client->request('GET', '/User/admin/user/edit');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form[name="user"]');
    }

    /**
     * Test that submitting the edit form updates user data and shows a success alert.
     *
     * @return void No value is returned, assertions validate redirect and success message
     */
    public function testEditFormSubmit(): void
    {
        $client = static::createClient();

        $admin = $this->createAdminUser();
        $client->loginUser($admin);

        $crawler = $client->request('GET', '/User/admin/user/edit');
        $form = $crawler->selectButton('Zapisz')->form();

        $form['user[firstName]'] = 'John';
        $form['user[lastName]'] = 'Doe';
        $form['user[newPassword][first]'] = 'newpassword';
        $form['user[newPassword][second]'] = 'newpassword';

        $client->submit($form);

        $this->assertResponseRedirects('/User/admin/user/edit');

        $client->followRedirect();
        $this->assertSelectorExists('.alert-success');
    }

    /**
     * Helper method to create an admin user entity for testing.
     *
     * @return User The created User entity with admin role
     */
    private function createAdminUser(): User
    {
        $email = 'admin_'.uniqid().'@example.com';

        $user = new User();
        $user->setEmail($email);
        $user->setRoles([User::ROLE_ADMIN]);
        $user->setPassword('dummy'); // can be hashed if needed

        $em = static::getContainer()->get('doctrine')->getManager();
        $em->persist($user);
        $em->flush();

        return $user;
    }
}
