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
        $this->assertSelectorExists('input[name="url_form[original]"]');
        $this->assertSelectorExists('input[placeholder="Enter the URL to shorter here"]');
    }

    /**
     * @test
     */
    public function create_should_shorten_url_if_that_url_hasnt_been_shortened_yet()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertResponseIsSuccessful();

        $form = $crawler->filter('form')->form();

        $original = 'https://python.org';

        $client->submit($form, [
            'url_form[original]'=>$original
        ]);

        $em = static::$container->get('doctrine')->getManager();

        $urlRepository = $em->getRepository(Url::class);

        $url = $urlRepository->findOneBy(compact('original'));

        $this->assertResponseRedirects(sprintf('/%s/preview',$url->getShortened()));
    }

    /**
     * @test
     */
    public function create_should_shorten_url_once()
    {
        $client = static::createClient();

        $em = static::$container->get('doctrine')->getManager();

        $url = new Url;
        $url->setOriginal("https://symfony.com");
        $url->setShortened('qwerty');
        $em->persist($url);
        $em->flush();

        $crawler = $client->request('GET', '/');

        $form = $crawler->filter('form')->form();

        $client->submit($form, [
            'url_form[original]'=>'https://symfony.com'
        ]);

        $this->assertResponseRedirects('/qwerty/preview');

        $urlRepository = $em->getRepository(Url::class);

        $this->assertCount(1,$urlRepository->findAll());
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

    /**
     * @test
     */
    public function original_should_not_blank(){
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $form = $crawler->filter('form')->form();


        $client->submit($form, [
            'url_form[original]'=>''
        ]);

        $this->assertSelectorTextContains('.error-message', 'You need to enter a URL');
    }

    /**
     * @test
     */
    public function original_should_be_a_valid_url(){
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $form = $crawler->filter('form')->form();

        $client->submit($form, [
            'url_form[original]'=>'tyht'
        ]);

        $this->assertSelectorTextContains('.error-message', 'The URL entered is invalid');
    }
}
