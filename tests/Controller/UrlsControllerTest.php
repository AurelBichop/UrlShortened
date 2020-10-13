<?php

namespace App\Tests\Controller;

use App\Entity\Url;

use Illuminate\Support\Str;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UrlsControllerTest extends WebTestCase
{
    /**
     * @test
     */
    public function homepage_should_display_url_shortener_form()
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertPageTitleSame('Botly');
        $this->assertSelectorTextContains('h1', 'The Best URL shortener out there!');
        $this->assertSelectorExists('form');
        $this->assertSelectorExists('input[name="form[original]"]');
        $this->assertSelectorExists('input[placeholder="Enter the URL to shorter here"]');
    }

    /**
     * @test
     */
    public function form_should_work_with_valid_data()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertResponseIsSuccessful();

        $form = $crawler->filter('form')->form();

        $client->submit($form, [
            'form[original]'=>'https://python.org'
        ]);

        $this->assertResponseRedirects();
    }

    /**
     * @test
     */
    public function shortened_version_should_redirect_to_original_url_if_shortened_exists(){
        $client = static::createClient();
        $em = self::$container->get('doctrine')->getManager();

        $original = 'https://framasoft.org';
        $shortened = Str::random(6);

        $url = new Url;
        $url->setOriginal($original);
        $url->setShortened($shortened);

        $em->persist($url);
        $em->flush();

        $client->request('GET', '/'.$shortened);
        $this->assertResponseRedirects($original);
    }

    /**
     * @test
     */
    public function show_should_return_404_response_if_shortened_doesnt_exists(){
        $client = static::createClient();

        $client->request('GET', '/qwerty');

        $this->assertResponseStatusCodeSame(404);
    }

    /**
     * @test
     */
    public function preview_shortened_version_should_work_if_shortened_exist(){
        $client = static::createClient();
        $em = self::$container->get('doctrine')->getManager();

        $original = 'https://www.gnu.org/';
        $shortened = Str::random(6);

        $url = new Url;
        $url->setOriginal($original);
        $url->setShortened($shortened);

        $em->persist($url);
        $em->flush();

        $crawler = $client->request('GET', sprintf('/%s/preview',$shortened));
        //dd($client->getResponse());
        $this->assertSelectorTextContains('h1', 'Yay! Here is your shortened URL:');
        $this->assertSelectorTextContains('h1 > a', 'http://localhost/'.$shortened);

        $this->assertSame('http://localhost/'.$shortened,$crawler->filter('h1 > a')->attr('href'));

        $client->clickLink('Go back home');
        $this->assertRouteSame('app_home');
    }

    /**
     * @test
     */
    public function preview_should_return_404_response_if_shortened_doesnt_exists(){
        $client = static::createClient();

        $client->request('GET', '/qwerty/preview');

        $this->assertResponseStatusCodeSame(404);
    }
}
