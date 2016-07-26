<?php

namespace ShopBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminControllerControllerTest extends WebTestCase
{
    public function testShowcommande()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/rackel');
    }

}
