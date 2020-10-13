<?php

namespace App\Controller;

use App\Entity\Url;
use App\Repository\UrlRepository;

use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Support\Str;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Url as UrlConstraint;

class UrlsController extends AbstractController
{
    private $urlRepository;

    public function __construct(UrlRepository $urlRepository){
        $this->urlRepository = $urlRepository;
    }
    /**
     * @Route("/", name="app_home", methods="GET|POST")
     *
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function create(Request $request, EntityManagerInterface $em):Response
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
            $url = $this->urlRepository->findOneBy(['original'=> $form['original']->getData()]);
            //si oui
            if($url){
                //on redirige bonne pratique suite a POST
                return $this->redirectToRoute('app_urls_preview',['shortened'=>$url->getShortened()]);
            }

            //si l'url n'a pas déja été raccourcie
            //alors on va la raccourcir
            // et retournezr la version preview raccourcie
            $url = new Url;
            $url->setOriginal($form['original']->getData());
            $url->setShortened($this->getUniqueShortenedString());

            $em->persist($url);
            $em->flush();

            return $this->redirectToRoute('app_urls_preview',['shortened'=>$url->getShortened()]);
        }

        return $this->render('urls/create.html.twig', [
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route ("/{shortened}/preview",name="app_urls_preview", methods="GET")
     *
     * @param Url $url
     * @return Response
     */
    public function preview(Url $url):Response{
        return $this->render('urls/preview.html.twig',compact('url'));
    }

    /**
     * @Route ("/{shortened}",name="app_urls_show", methods="GET")
     *
     * @param Url $url
     * @return Response
     */
    public function show(Url $url):Response{
        return $this->redirect($url->getOriginal());
    }

    private function getUniqueShortenedString():string{
         $shortened = Str::random(6);

         if($this->urlRepository->findOneBy(compact('shortened'))){
            return $this->getUniqueShortenedString();
         }

         return $shortened;
    }
}
