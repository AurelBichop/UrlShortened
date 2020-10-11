<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class UrlsController extends AbstractController
{
    /**
     * @Route("/", name="app_urls_create")
     */
    public function index()
    {
        return $this->render('urls/create.html.twig');
    }
}
