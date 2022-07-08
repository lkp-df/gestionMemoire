<?php

namespace App\Controller;

use App\Entity\Filiere;
use App\Form\FiliereType;
use App\Repository\FiliereRepository;
use Doctrine\Persistence\ObjectManager;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


class FiliereController extends AbstractController
{
    /**
     * @Route("/filiere",name="gest_filiere")
     * @IsGranted("ROLE_SECRETAIRE")
     */
    public function gestionFiliere(Request $request, ObjectManager $manager, FiliereRepository $repo, PaginatorInterface $paginator)
    {
        $filiere = new Filiere();
        $form = $this->createForm(FiliereType::class, $filiere);

        $form->handleRequest($request);

        //verifions si le formulaire est soumis
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($filiere);
            $manager->flush();

            //redirection
            return $this->redirectToRoute('gest_filiere');
        }

        //pour la modification de la filiere


        //pour l'affichage des filieres
        $mesfilieres = $repo->findAll();

        #pagination avec knp paginator
        #filieres est maintenant mes filieres avec pagnation
        $filieres = $paginator->paginate(
            $mesfilieres, #les donnees sur lequelles nous allons paginer
            $request->query->getInt('page', 1), #definie le parametre page en get et si c'est pas defini on prend la page 1
            5, #nombre d'element a afficher par page
        );
        return $this->render('index/filiere.html.twig', [
            "formFiliere" => $form->createView(),
            "filieres" => $filieres
        ]);
    }

    /**
     * @Route("/filiere/edit/{id<\d+>}",name="edit_filiere")
     * @IsGranted("ROLE_SECRETAIRE")
     */
    public function editFiliere(Filiere $filiere, Request $request, ObjectManager $manager)
    {
        $form = $this->createForm(FiliereType::class, $filiere);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($filiere);
            $manager->flush();

            return $this->redirectToRoute('gest_filiere');
        }
        return $this->render(
            "index/edit_filiere.html.twig",
            ["formEdit" => $form->createView()]
        );
    }
    /**
     * @Route("/filiere/del",name="del_filiere",methods={"GET","POST"})
     * @IsGranted("ROLE_SECRETAIRE")
     */
    public function delete_filiere(Request $request, ObjectManager $manager, FiliereRepository $repo)
    {
        $del_id = $request->request->get('del_id');

        //cherchons la filiere en question
        $filiere = $repo->find($del_id);

        if ($filiere) {
            $manager->remove($filiere);
            $manager->flush();
            $reponse = ["message" => "Filière supprimée avec succès"];
            return new JsonResponse($reponse, 200);
        } else {
            $reponse = ["message" => "Filière supprimée avec succès"];
            return new JsonResponse($reponse, 400);
        }
    }

}
