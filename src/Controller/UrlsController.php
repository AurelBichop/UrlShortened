<?php

namespace App\Controller;

use App\Entity\Url;
use App\Form\UrlFormType;
use App\Repository\UrlRepository;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
        $form = $this->createForm(UrlFormType::class);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            //valider les infos
            //on vérif si url entré a déja été raccourcie
            $url = $this->urlRepository->findOneBy(['original'=> $form['original']->getData()]);

            if(!$url){

                $url = $form->getData();
                //$url->setShortened($this->getUniqueShortenedString());
                $em->persist($url);
                $em->flush();
            }

            //on redirige bonne pratique suite a un POST
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
}
