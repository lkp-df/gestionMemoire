<?php

namespace App\Controller;

use App\Entity\Memoire;
use App\Entity\Etudiant;
use App\Form\MemoireType;
use App\Form\EtudiantType;
use App\Entity\EditFiliere;
use App\Entity\EditMemoire;
use App\Entity\EditEncadreur;
use App\Form\EditFiliereType;
use App\Form\EditMemoireType;
use App\Form\EditEtudiantType;
use App\Entity\EtudiantMemoire;
use App\Form\EditEncadreurType;
use App\Entity\EditInfoEtudiant;
use App\Form\FormEtudiantMemoireType;
use App\Repository\FiliereRepository;
use App\Repository\MemoireRepository;
use App\Repository\EtudiantRepository;
use App\Repository\EncadreurRepository;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


class EtudiantController extends AbstractController
{
    /**
     * @Route("/gestetudiant",name="gest_etudiant")
     * @IsGranted("ROLE_SECRETAIRE")
     */
    public function gestionEtudiant(
        EtudiantRepository $repo,
        Request $request,
        ObjectManager $manager,
        FiliereRepository $fil,
        EncadreurRepository $enca,
        MemoireRepository $mem
    ) {
        //tous les etudiants
        $etudiants = $repo->findAll();

        //new entitys
        //$etudiant = new Etudiant();
        //$memoire = new Memoire();
        $etudiantMemoire = new EtudiantMemoire();

        //creation des formulaires
        //$formEtudiant = $this->createForm(EtudiantType::class, $etudiant);
        //$formMemoire = $this->createForm(MemoireType::class, $memoire);

        $formEtudiantMemoire = $this->createForm(FormEtudiantMemoireType::class, $etudiantMemoire);
        $formEtudiantMemoire->handleRequest($request);

        if ($formEtudiantMemoire->isSubmitted() && $formEtudiantMemoire->isValid()) {

            $id_fil = $request->request->get("form_etudiant_memoire")["filiere"];
            $id_enca = $request->request->get("form_etudiant_memoire")["encadreur"];
            $nom = $request->request->get("form_etudiant_memoire")["nom"];
            $prenom = $request->request->get("form_etudiant_memoire")["prenom"];
            $adresse = $request->request->get("form_etudiant_memoire")["adresse"];
            $annee = $request->request->get("form_etudiant_memoire")["annee"];
            $theme = $request->request->get("form_etudiant_memoire")["theme"];
            $option = $request->request->get("form_etudiant_memoire")["options"];

            //inserons les données en base maintenant
            //inserons le memoire
            $memoire = new Memoire();
            $memoire->setTheme($theme);
            $memoire->setAnnee($annee);
            $memoire->setOptions($option);

            $manager->persist($memoire);
            $manager->flush();
            $id_mem = $memoire->getId();


            if (
                $id_fil != null && $id_mem != null
                && $id_enca != null
            ) {
                $filiere = $fil->find($id_fil);
                $encadreur = $enca->find($id_enca);
                $memoire = $mem->find($id_mem);
                //inserons l'etudiant
                $etudiant = new Etudiant();
                $etudiant->setNom($nom);
                $etudiant->setprenom($prenom);
                $etudiant->setAdresse($adresse);
                $etudiant->setFiliere($filiere);
                $etudiant->setEncadreur($encadreur);
                $etudiant->setMemoire($memoire);

                $manager->persist($etudiant);
                $manager->flush();

                return $this->redirectToRoute("gest_etudiant");
            }
        }
        return $this->render(
            'index/etudiant.html.twig',
            [
                "formEtudiantMemoire" => $formEtudiantMemoire->createView(),
                "etudiants" => $etudiants
            ]
        );
    }
    /**
     * @Route("/gestetudiant/edit/{id<\d+>}",name="edit_etud")
     * @IsGranted("ROLE_SECRETAIRE")
     */
    public function edit_etudiant(Etudiant $etudiant, Request $request, ObjectManager $manager, FiliereRepository $f, EncadreurRepository $E)
    {
        //creation des objets
        $etudiant_mod = new EditInfoEtudiant();
        $etudiant_mod->setNom($etudiant->getNom());
        $etudiant_mod->setPrenom($etudiant->getPrenom());
        $etudiant_mod->setAdresse($etudiant->getAdresse());

        $formEtudiant = $this->createForm(EditEtudiantType::class, $etudiant_mod);

        $memoire_mod = new EditMemoire();
        //recuperons le memoire concerné
        $memoire_mod->setTheme($etudiant->getMemoire()->getTheme());
        $memoire_mod->setAnnee($etudiant->getMemoire()->getAnnee());
        $memoire_mod->setOptions($etudiant->getMemoire()->getOptions());

        $formMemoire = $this->createForm(EditMemoireType::class, $memoire_mod);

        //pour la filiere
        $filiere_nom = $etudiant->getFiliere()->getDesignation();

        $filiere_mod = new EditFiliere();
        $formFiliere = $this->createForm(EditFiliereType::class, $filiere_mod);

        //pour l'encadreur
        $encadreur_nom = $etudiant->getEncadreur()->getNom();
        $encadreur_prenom = $etudiant->getEncadreur()->getPrenom();

        $encadeur_mod = new EditEncadreur();

        $formEncadreur = $this->createForm(EditEncadreurType::class, $encadeur_mod);

        //modification etudiant
        $formEtudiant->handleRequest($request);
        if ($formEtudiant->isSubmitted() && $formEtudiant->isValid()) {
            $etudiant->setNom($request->request->get("edit_etudiant")["nom"]);
            $etudiant->setPrenom($request->request->get("edit_etudiant")["prenom"]);
            $etudiant->setAdresse($request->request->get("edit_etudiant")["adresse"]);

            $manager->persist($etudiant);
            $manager->flush();

            return $this->redirectToRoute("gest_etudiant");
        }

        //modification du memoire
        $formMemoire->handleRequest($request);
        if ($formMemoire->isSubmitted() && $formMemoire->isValid()) {
            $m = $etudiant->getMemoire();
            $m->setTheme($request->request->get("edit_memoire")["theme"]);
            $m->setAnnee($request->request->get("edit_memoire")["annee"]);
            $m->setOptions($request->request->get("edit_memoire")["options"]);

            $etudiant->setMemoire($m);

            $manager->persist($m);
            $manager->flush();

            return $this->redirectToRoute("gest_etudiant");
        }

        //modification filiere
        $formFiliere->handleRequest($request);
        if ($formFiliere->isSubmitted() && $formFiliere->isValid()) {
            $id = $request->request->get("edit_filiere")["filiere"];
            $filiere = $f->find($id);
            $etudiant->setFiliere($filiere);
            $manager->persist($etudiant);
            $manager->flush();
            return $this->redirectToRoute("gest_etudiant");
        }

        //modification encadreur de l'etudiant
        $formEncadreur->handleRequest($request);
        if ($formEncadreur->isSubmitted() && $formEncadreur->isValid()) {
            $id = $request->request->get("edit_encadreur")["encadreur"];
            $encadreur = $E->find($id);
            $etudiant->setEncadreur($encadreur);

            $manager->persist($etudiant);
            $manager->flush();

            return $this->redirectToRoute("gest_etudiant");
        }

        return $this->render(
            "index/edit_etudiant.html.twig",
            [
                "formEtudiant" => $formEtudiant->createView(),
                "formMemoire" => $formMemoire->createView(),
                "form_filiere" => $formFiliere->createView(),
                "formEncadreur" => $formEncadreur->createView(),
                "filiere_nom" => $filiere_nom,
                "encadreur_nom" => $encadreur_nom,
                "encadreur_prenom" => $encadreur_prenom,
            ]
        );
    }

    /**
     * @Route("/gestetudiant/del",name="del_etudiant",methods={"POST","GET"})
     * @IsGranted("ROLE_SECRETAIRE")
     */
    public function del_etudiant(Request $request, EtudiantRepository $repo, ObjectManager $manager)
    {
        $id = $request->request->get("id_del");

        if ($id != null) {
            $etudiant = $repo->find($id);
            $memoire = $etudiant->getMemoire();
            $manager->remove($memoire);
            $manager->remove($etudiant);
            $manager->flush();
            $response = ["message" => "Etudiant supprimé correctement"];

            return new JsonResponse($response, 200);
        } else {
            $response = ["message" => "Suppression Impossible"];
            return new JsonResponse($response, 400);
        }
    }
}
