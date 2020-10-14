<?php

namespace App\Tests\Entity;

use App\Entity\Url;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UrlTest extends KernelTestCase
{
    /**
     * @test
     */
    public function shortened_should_be_generated_if_empty()
    {
        static::bootKernel();
        $em = static::$container->get('doctrine')->getManager();

        $url = new Url();
        $url->setOriginal('https://symfony.com');

        $em->persist($url);
        $em->flush();

        $this->assertNotNull($url->getShortened());
        $this->assertSame(6, mb_strlen($url->getShortened()));
    }

    /**
     * @test
     */
    public function shortened_should_not_be_overridden_if_not_empty()
    {
        static::bootKernel();
        $em = static::$container->get('doctrine')->getManager();

        $url = new Url();
        $url->setOriginal('https://symfony.com');
        $url->setShortened('qwerty');

        $em->persist($url);
        $em->flush();

        $this->assertSame('qwerty', $url->getShortened());
    }
}
