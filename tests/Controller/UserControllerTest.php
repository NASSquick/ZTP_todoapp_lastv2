<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    private function createAdminUser(): User
    {
        $email = 'admin_' . uniqid() . '@example.com';

        $user = new User();
        $user->setEmail($email);
        $user->setRoles([User::ROLE_ADMIN]);
        $user->setPassword('dummy'); // we can hash if needed later

        $em = static::getContainer()->get('doctrine')->getManager();
        $em->persist($user);
        $em->flush();

        return $user;
    }

    public function testEditFormDisplayed(): void
    {
        $client = static::createClient();

        $admin = $this->createAdminUser();
        $client->loginUser($admin);

        $crawler = $client->request('GET', '/User/admin/user/edit');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form[name="user"]');
    }

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
}
