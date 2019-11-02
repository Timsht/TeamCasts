<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeControllerTest extends WebTestCase
{
    /**
     * @dataProvider successUrls
     */
    public function testPageIsSuccessful($url)
    {
        $client = static::createClient();
        $client->request('GET', $url);

        $this->assertTrue($client->getResponse()->isSuccessful());
    }
    
    public function successUrls()
    {
        return [
            ["/"],
            ["/login"],
            ["/register"],
            ["/register"],
        ];
    }
    
    /**
     * @dataProvider redirectUrls
     */
    public function testPageIsRedirect($url)
    {
        $client = static::createClient();
        $client->request('GET', $url);

        $this->assertTrue($client->getResponse()->isRedirect());
    }

    public function redirectUrls()
    {
        return [
            ["/logout"],
            ["/user"],
            ["/group"],
            ["/flash"],
        ];
    }

    /**
     * @dataProvider notFoundUrls
     */
    public function testPageIsNotFound($url)
    {
        $client = static::createClient();
        $client->request('GET', $url);

        $this->assertTrue($client->getResponse()->isNotFound());
    }

    public function notFoundUrls()
    {
        return [
            ["/loadPosts/5"],
        ];
    }

    public function testHomepage()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', "/");
        $this->assertContains('accueil', $crawler->filter('h1')->text());
    }
   
}
