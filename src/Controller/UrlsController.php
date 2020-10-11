<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UrlsController extends AbstractController
{
    /**
     * @Route("/", name="app_urls_create")
     */
    public function create():Response
    {
        $form = $this->createFormBuilder()
            ->add('original', null,[
                'label' => false,
                'attr'  =>[
                    'placeholder' => 'Enter the URL to shorter here'
                ]
            ])
            ->getForm();

        return $this->render('urls/create.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
