<?php

namespace App\Controller;

use App\Entity\Url;
use App\Repository\UrlRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Url as UrlConstraint;

class UrlsController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     * @Route("/", name="app_urls_create")
     *
     * @param Request $request
     * @param UrlRepository $urlRepository
     * @return Response
     */
    public function create(Request $request, UrlRepository $urlRepository):Response
    {
        $form = $this->createFormBuilder()
            ->add('original', null,[
                'label' => false,
                'attr'  =>[
                    'placeholder' => 'Enter the URL to shorter here'
                ],
                'constraints' => [
                    new NotBlank(['message'=>'You need to enter a URL']),
                    new UrlConstraint(['message'=>'The URL entered is invalid'])
                ]
            ])
            ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            //valider les infos
            //on vérif si url entré a déja été raccourcie
            $url = $urlRepository->findOneBy(['original'=> $form['original']->getData()]);
            //si oui
            if($url){
                //on retourne la version preview
                return $this->render('urls/preview.html.twig',compact('url'));
            }


            //si l'url n'a pas déja été raccourcie
        }

        return $this->render('urls/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route ("/{shortened}",name="app_urls_show")
     *
     * @param Url $url
     * @return Response
     */
    public function show(Url $url):Response{
        return $this->redirect($url->getOriginal());
    }
}
