<?php

namespace App\Controller;

use App\Entity\Encadreur;
use App\Form\EncadreurType;
use App\Entity\MonEncadreur;
use App\Form\EditInfoEncadreurType;
use App\Form\EditProfilEncadreurType;
use App\Repository\EncadreurRepository;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


class ProfesseurController extends AbstractController
{
    /**
     * @Route("/gestencadreur",name="gest_encadreur",methods={"POST","GET"})
     * @IsGranted("ROLE_DIRECTEUR")
     */
    public function gestionEncadreur(Request $request, ObjectManager $manager, EncadreurRepository $repo)
    {
        $encadreur = new Encadreur();
        $form = $this->createForm(EncadreurType::class, $encadreur);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $encadreur->getAvatar();
            //creer le dossier de stockage d'image
            $upload_dir = $this->getParameter("uploads_directory");
            //generons un nom unique et recuerons l'extension de l'image
            $filename = md5(uniqid()) . '.' . $file->guessExtension();
            //deplacons le fichier dans le dossier uploads qui est dans public
            $file->move($upload_dir, $filename);

            //inseretion en base
            $encadreur->setAvatar($filename);
            $manager->persist($encadreur);
            $manager->flush();

            return $this->redirectToRoute('gest_encadreur');
        }
        //renvoyer la liste des encadreurs par injection de dependance
        $encadreurs = $repo->findAll();
        return $this->render(
            'index/encadreur.html.twig',
            [
                "formEncadreur" => $form->createView(),
                "encadreurs" => $encadreurs
            ]
        );
    }
    /**
     * @Route("/gestencadreur/edit/{id<\d+>}",name="edit_encadreur")
     * @IsGranted("ROLE_DIRECTEUR")
     */
    public function edit_encadreur(Encadreur $encadreur, Request $request, ObjectManager $manager)
    {
        //afficher l'image de l'encadreur afin de modifier
        $avatar = $encadreur->getAvatar();

        //recuperation des infos du prof courant
        $nom = $encadreur->getNom();
        $prenom = $encadreur->getPrenom();
        $titre = $encadreur->getTitre();

        //j'utilise monEncadreur objet pour remplir le formulaire
        $monEncadreur = new MonEncadreur();

        $monEncadreur->setNom($nom);
        $monEncadreur->setPrenom($prenom);
        $monEncadreur->setTitre($titre);


        //pour les informations personnelles juste
        $form_encad_info = $this->createForm(EditInfoEncadreurType::class, $monEncadreur);

        $form_encad_info->handleRequest($request);

        if ($form_encad_info->isSubmitted() && $form_encad_info->isValid()) {
            $encadreur->setNom($request->request->get('edit_info_encadreur')["nom"]);
            $encadreur->setPrenom($request->request->get('edit_info_encadreur')["prenom"]);
            $encadreur->setTitre($request->request->get('edit_info_encadreur')["titre"]);

            //dump($encadreur);
            $manager->persist($encadreur);
            $manager->flush();
            return $this->redirectToRoute('gest_encadreur');
        }
        //pour le profil de l'encadreur juste
        $form_encad_profil = $this->createForm(EditProfilEncadreurType::class, $encadreur);

        $form_encad_profil->handleRequest($request);

        if ($form_encad_profil->isSubmitted() && $form_encad_profil->isValid()) {
            //pointons sur le repertoire upload qui contient les image uploadees
            $upload_dir = $this->getParameter("uploads_directory");

            //supprimons l'ancienne image avec unlink
            $r = unlink($upload_dir . "/" . $avatar);

            if ($r) {
                $file = $encadreur->getAvatar();
                //un nouveau nom avec md5
                $filename = md5(uniqid()) . "." . $file->guessExtension();
                //deplacons dans le dossier
                $file->move($upload_dir, $filename);
                //modifier l'ancien image par le nouveau nom
                $encadreur->setAvatar($filename);
                $manager->persist($encadreur);
                $manager->flush();
                return $this->redirectToRoute('gest_encadreur');
            }
        }
        return $this->render(
            'index/edit_encadreur.html.twig',
            [
                "avatar" => $avatar,
                "formEnditEncadreur" => $form_encad_info->createView(),
                "formEnditEncad_profil" => $form_encad_profil->createView()
            ]
        );
    }
    /**
     * @Route("/gestencadreur/del",name="del_encadreur",methods={"POST","GET"})
     * @IsGranted("ROLE_DIRECTEUR")
     */
    public function delete_encadreur(Request $request, EncadreurRepository $repo, ObjectManager $manager)
    {
        $id = $request->request->get('id_del');
        $encadreur = $repo->find($id);
        //precisons le repertoire des images
        $images_dir = $this->getParameter("uploads_directory");
        //supprimons son image dans le dossier des images
        $r = unlink($images_dir . "/" . $encadreur->getAvatar());

        if ($r) { //on supprime l'encadreur
            $manager->remove($encadreur);
            $manager->flush();

            //renvoyons le message en ajax sous forme de json
            $reponse = ["message" => "Professeur supprimé avec succès"];
            return new JsonResponse($reponse, 200);
        } else {
            $reponse = ["message" => "Suppresions Impossible"];
            return new JsonResponse($reponse, 400);
        }
    }
}
