<?php

namespace App\Tests\Form;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SearchUserTypeTest extends WebTestCase
{
	public function testSearcUserForm()
	{
        $client = static::createClient();

        $crawler = $client->request('GET', '/');
        $form = $crawler->selectButton(".form-search button")->form();

        $form["search_user[Username]"] = 'us';

        $crawler = $client->submit($form);
        $this->assertTrue(true);
	}
}