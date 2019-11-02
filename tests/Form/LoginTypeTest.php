<?php

namespace App\Tests\Form;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoginTypeTest extends WebTestCase
{
	public function testLoginForm()
	{
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton("Connexion")->form();

        $form["email"] = 'u@user.fr';
        $form["password"] = 'useruser';

        $crawler = $client->submit($form);
        $this->assertTrue(true);
	}
}