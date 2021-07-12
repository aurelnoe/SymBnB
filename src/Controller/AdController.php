<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AdType;
use App\Entity\Image;
use App\Service\AdService;
use App\Service\ImageService;
use App\Repository\AdRepository;
use App\Repository\ImageRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/ad")
 */
class AdController extends AbstractController
{
    private $adRepository;
    private $imageRepository;
    private $serviceAd;
    private $serviceImage;

    public function __construct(AdRepository $adRepository,ImageRepository $imageRepository, AdService $serviceAd, ImageService $serviceImage){
        $this->adRepository = $adRepository;
        $this->imageRepository = $imageRepository;
        $this->serviceAd = $serviceAd;
        $this->serviceImage = $serviceImage;
    }

    /**
     * @Route("/", name="ad_index", methods={"GET","POST"})
     */
    public function index(): Response
    {
        return $this->render('ad/index.html.twig', [
            'ads' => $this->adRepository->findAll(),
        ]);
    }

    /**
     * Permet de créer une annonce
     * 
     * @Route("/new", name="ad_new", methods={"GET","POST"})
     * @IsGranted("ROLE_USER")
     */
    public function new(Request $request): Response
    {
        $ad = new Ad();

        $image = new Image();

        
        $form = $this->createForm(AdType::class, $ad);
        $form->handleRequest($request);
        
        $ad->addImage($image);
        
        if ($form->isSubmitted() && $form->isValid()) {
            
            foreach ($ad->getImages() as $image) {
                $image->setAd($ad);
                $this->serviceImage->persist($image);
            }

            //$this->getUser() est l'utilisateur connecté
            $ad->setAuthor($this->getUser());

            $this->serviceAd->persist($ad);

            $this->addFlash(
                'success',
                "L'annonce {$ad->getTitle()} a bien été créé "
            );
            $this->addFlash(
                'danger',
                "L'annonce n'a pas été créé "
            );

            return $this->redirectToRoute('ad_index');
        }

        return $this->render('ad/new.html.twig', [
            'ad' => $ad,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/show/{id}", name="ad_show", methods={"GET","POST"}, requirements={"id":"\d+"})
     * 
     * @return Response
     */
    public function show(int $id): Response
    {
        $ad = $this->serviceAd->searchById(+$id);

        return $this->render('ad/show.html.twig', [
            'ad' => $ad,
        ]);
    }


    /**
     * @Route("/{id}/edit", name="ad_edit", methods={"GET","POST", "PUT"}, requirements={"id":"\d+"})
     * 
     * @Security("is_granted('ROLE_USER')", message="Cette annonce ne vous appartient pas, vous ne pouvez pas la modifier !")
     */
    public function edit(Request $request, int $id): Response
    {
        $ad = $this->serviceAd->searchById(+$id);

        $form = $this->createForm(AdType::class, $ad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $this->serviceAd->persist($ad);

            return $this->redirectToRoute('ad_index');
        }

        return $this->render('ad/edit.html.twig', [
            'ad' => $ad,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Permet de supprimer une annonce
     * 
     * @Route("/{id}/delete", name="ad_delete", requirements={"id":"\d+"}, methods={"POST","DELETE"})
     * @Security("is_granted('ROLE_USER')", message = "Vous n'avez pas le droit d'accéder à cette ressource")
     * 
     * @return Response
     */
    public function delete(Request $request,Ad $ad, int $id=null): Response
    {
        if ($this->isCsrfTokenValid('delete'.$ad->getId(), $request->request->get('_token'))) {
            $this->serviceAd->delete($id);
        }

        return $this->redirectToRoute('ad_index');
    }

}
