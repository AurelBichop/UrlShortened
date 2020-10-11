<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Url;

class UrlsController extends AbstractController
{
    /**
     * @Route("/", name="app_urls_create")
     * @param Request $request
     * @return Response
     */
    public function create(Request $request):Response
    {
        $form = $this->createFormBuilder()
            ->add('original', null,[
                'label' => false,
                'attr'  =>[
                    'placeholder' => 'Enter the URL to shorter here'
                ],
                'constraints' => [
                    new NotBlank(['message'=>'You need to enter a URL']),
                    new Url(['message'=>'The URL entered is invalid'])
                ]
            ])
            ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            //valider les infos

            //on vérif si url entré a déja été raccourcie
            //si oui
            //on retourne la version preview

            //si l'url n'a pas déja été raccourcie
        }

        return $this->render('urls/create.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
