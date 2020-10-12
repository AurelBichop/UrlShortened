<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UrlsControllerTest extends WebTestCase
{
    /**
     * @test
     */
    public function homepage_should_display_url_shortener_form()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertPageTitleSame('Botly');
        $this->assertSelectorTextContains('h1', 'The Best URL shortener out there!');
        $this->assertSelectorExists('form');
        $this->assertSelectorExists('input[name="form[original]"]');
        $this->assertSelectorExists('input[placeholder="Enter the URL to shorter here"]');

    }
}
