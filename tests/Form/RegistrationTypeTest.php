<?php

namespace App\Tests\Form;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegistrationTypeTest extends WebTestCase
{
	public function testRegistrationForm()
	{
        $client = static::createClient();

        $crawler = $client->request('GET', '/register');
        $form = $crawler->selectButton("Inscription")->form();

        $form["registration_form[email]"] = 'u@user.fr';
        $form["registration_form[username]"] = 'Tim';
        $form["registration_form[password][first]"] = 'useruser';
        $form["registration_form[password][second]"] = 'useruser';

        $crawler = $client->submit($form);
        $this->assertTrue(true);
	}
}