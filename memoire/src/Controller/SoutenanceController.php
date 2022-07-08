<?php

namespace App\Controller;

use App\Entity\Jury;
use App\Entity\Reponse;
use App\Form\ReponseType;
use App\Entity\Soutenance;
use App\Entity\CompletSoutenance;
use App\Entity\Etudiant;
use App\Repository\JuryRepository;
use App\Entity\EtudiantSoutenances;
use App\Form\CompletSoutenanceType;
use App\Form\ReponseEtudiantType;
use App\Form\ReponseJuryType;
use App\Repository\EtudiantRepository;
use App\Repository\EncadreurRepository;
use Doctrine\Persistence\ObjectManager;
use App\Repository\SoutenanceRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\EtudiantSoutenancesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class SoutenanceController extends AbstractController
{
    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/gestsoutenance",name="gest_soutenance",methods={"POST","GET"})
     * @IsGranted("ROLE_DIRECTEUR")
     */
    public function gestionSoutenance(Request $request, EncadreurRepository $en, EtudiantRepository $et, ObjectManager $manager, SoutenanceRepository $allSoutenance)
    {
        $soutenance = new CompletSoutenance();
        $formSoutenance = $this->createForm(CompletSoutenanceType::class, $soutenance);

        //renvoie une ligne pour les jury
        $afficheJury = $request->request->get("afficheJury");
        if ($afficheJury) {
            //recuperation des tous les encadreurs en base
            $encadreurs = $en->findAll();
            $ligne_jury = '<tr>
            <th scope="row" class="numero"></th>
            
                <td>
                    <select class="form-control" name="select_jury[]" id="select_jury[]">
                    <option>Choisir l\'encadreur</option>
                    ';
            foreach ($encadreurs as $encadreur) {
                $ligne_jury .= '<option value="' . $encadreur->getId() . '">' . $encadreur->getPrenom() . ' &nbsp; ' . $encadreur->getNom() . ' </option>';
            }
            $ligne_jury .= '
            </select>
             </td>
                 
            <td>
                <select class="form-control" id="select_post[]" name="select_post[]">
                    <option>choisir poste</option>
                    <option value="pres">Président du Jury</option>
                    <option value="memb">Membre du Jury</option>
                </select>
            </td>
        </tr>';
            return new JsonResponse($ligne_jury, 200);
        }

        //renvoiue une ligne pour les eleves
        $affiche_eleve = $request->request->get("affiche_eleve");
        if ($affiche_eleve) {
            $eleves = $et->findAll();
            $ligne_eleve = '<tr>
                                <th scope="row" class="numero_eleve">1</th>
                                <td>
                                    <select class="form-control id" id="select_eleve[]" name="select_eleve[]">
                                        <option>choisir etudiant</option>';
            foreach ($eleves as  $eleve) {
                $ligne_eleve .= '
                                                <option value="' . $eleve->getId() . '">' . $eleve->getPrenom() . '&nbsp;' . $eleve->getNom() . '</option>
                                            ';
            }
            $ligne_eleve .= '
                                    </select>
                                </td>
                                <td>
                                    <input type="text" class="form-control encadreur" name="encadreur[]" id="encadreur[]" readonly>
                                </td>
                                <td>
                                    <input type="text" class="form-control theme" name="theme[]" id="theme[]" readonly>
                                </td>
                                <td>
                                    <input type="number" id="note[]" name="note[]" class="form-control note" required>
                                </td>
                            </tr>';
            return new JsonResponse($ligne_eleve, 200);
        }

        //remplissage des champs de l'etudiant: son encadreur son theme
        $showEl = $request->request->get("showEl");

        if ($showEl != null) {
            $id = $request->request->get("idEl");
            $etudiant = $et->find($id);
            //creons un tableau pour stocker le nom de l'encadreur et le theme de memoire
            $response = [
                "pren_encadreur" => $etudiant->getEncadreur()->getPrenom(),
                "nom_encadreur" => $etudiant->getEncadreur()->getNom(),
                "theme" => $etudiant->getMemoire()->getTheme()
            ];
            return new JsonResponse($response, 200);
        }

        //partie enregistrement soutenance
        $formSoutenance->handleRequest($request);

        if ($formSoutenance->isSubmitted() && $formSoutenance->isValid()) {
            dump($request);
            //insertion de la soutenance
            $newSoutenance = new Soutenance();
            $newSoutenance->setSalle($request->request->get("complet_soutenance")["salle"]);
            $newSoutenance->setType($request->request->get("complet_soutenance")["typeSoutenance"]);
            $newSoutenance->setDate($request->request->get("complet_soutenance")["date"]);
            $manager->persist($newSoutenance);
            $manager->flush();
            $id_soutenance = $newSoutenance->getId();

            if ($id_soutenance != null) {
                //recuperons le nombre des jurys
                $t_jury = $request->request->get("select_jury");
                $t_post = $request->request->get("select_post");

                //inserons les membres du jury, il faut un tableau
                if (!empty($t_jury)) {
                    for ($i = 0; $i < count($t_jury); $i++) {
                        $jury = new Jury();
                        $jury->setSoutenances($newSoutenance);
                        $jury->setEncadreur($en->find($t_jury[$i]));

                        if ($t_post[$i] == "pres") {
                            $jury->setTitre("president");
                        } else {
                            $jury->setTitre("membre");
                        }
                        $manager->persist($jury);
                        $manager->flush();
                    }
                }

                //mettons a jour la note de l'etudiant apres avoir soutenu
                //on a un tableau d'eleve et pour chaque eleve il faut modifier sa note
                $t_eleve = $request->request->get("select_eleve");
                $t_note = $request->request->get("note");
                if (!empty($t_eleve)) {
                    for ($j = 0; $j < count($t_eleve); $j++) {
                        $etu_soutenance = new EtudiantSoutenances();
                        $etu_soutenance->setEtudiant($et->find($t_eleve[$j]));
                        $etu_soutenance->setSoutenance($newSoutenance);

                        //modifier la note de l'etudiant
                        $etu_soutenance->setNoteSoutenance($t_note[$j]);
                        //on persist les donnees et on modifie les notes des etudiants
                        $manager->persist($etu_soutenance);
                        $manager->flush();
                    }
                    //inserons l'id de l'etudiant et la soutenace qu'il a participe

                }
            }
        }

        //get all soutenance
        $all_soutenances = $allSoutenance->findAll();
        return $this->render(
            'index/soutenance.html.twig',
            [
                "formSoutenance" => $formSoutenance->createView(),
                "soutenances" => $all_soutenances
            ]
        );
    }

    /**
     * @Route("/gestsoutenance/view/{id<\d+>}",name="view_soutenance")
     * @IsGranted("ROLE_DIRECTEUR")
     */
    public function view_soutenance(Soutenance $soutenance, SoutenanceRepository $sou)
    {
        //affichons les jury
        $jurys = $sou->findJuryToSoutenance($soutenance->getId());

        $etudiants = $sou->listElevesToSoutenance($soutenance->getId());
        return $this->render(
            "index/view_soutenance.html.twig",
            [
                "soutenance" => $soutenance,
                "jurys" => $jurys,
                "etudiants" => $etudiants
            ]
        );
    }
    /**
     * @Route("/gestsoutenance/{id<\d+>}/edit",name="edit_soutenance",methods={"POST","GET"})
     * @IsGranted("ROLE_DIRECTEUR")
     */
    public function edit_soutenance(
        Request $request,
        Soutenance $soutenance,
        EtudiantSoutenancesRepository $repo_sout,
        JuryRepository $jury,
        EncadreurRepository $en,
        EtudiantRepository $etudiantRepository
    ) {
        //tous les etudiants presents à cette soutenance
        $etudiants_soutenance =  $repo_sout->findBy(['soutenance' => $soutenance->getId()]);

        //les jurys présents à la soutencance ainsi que que les titres qu'ils occupents
        $jurys = $jury->findBy(['soutenances' => $soutenance->getId()]);

        //ajouter une ligne de jury
        $afficheJury = $request->request->get("afficheJury");
        // dd($afficheJury); c'est null mais ça marche
        if ($afficheJury) {
            //recuperation des tous les encadreurs en base
            $encadreurs = $en->findAll();
            $ligne_jury =
                '<tr>
                        <td>
                            <select class="form-control" name="select_jury[]" id="select_jury[]">
                            <option>Choisir l\'encadreur</option>
                            ';
            foreach ($encadreurs as $encadreur) {
                $ligne_jury .= '<option value="' . $encadreur->getId() . '">' . $encadreur->getPrenom() . ' &nbsp; ' . $encadreur->getNom() . ' </option>';
            }
            $ligne_jury .= '
                    </select>
                    </td>
                        
                    <td>
                        <select class="form-control" id="select_post[]" name="select_post[]">
                            <option>choisir poste</option>
                            <option value="pres">Président du Jury</option>
                            <option value="memb">Membre du Jury</option>
                        </select>
                    </td>
                    <td>
                      <a href="#" class="btn btn-danger remove_jury">-</a>
                    </td>
             </tr>';
            return new JsonResponse($ligne_jury, 200);
        }

        //formulaire pour ajouter un element manqué durant la modification
        $reponse = new Reponse();
        $formReponse = $this->createForm(ReponseJuryType::class, $reponse);
        $formReponse->handleRequest($request);
        //on va recuperer les elements dans un tableau
        if ($formReponse->isSubmitted() && $formReponse->isValid()) {
            //dd($request);
            //recuperons nos deux tableaux
            $t_jury = $request->request->get("select_jury");
            $t_poste = $request->request->get("select_post");
            //pour l'ajout d'un jury a une soutenance
            for ($i = 0; $i < count($t_jury); $i++) {
                $jury = new Jury();
                $jury->setSoutenances($soutenance)
                    ->setEncadreur($en->find($t_jury[$i]));
                if ($t_poste[$i] == "pres") {
                    $jury->setTitre("president");
                } else {
                    $jury->setTitre("membre");
                }
                $this->em->persist($jury);
                $this->em->flush();
            }
            return $this->redirectToRoute('edit_soutenance', ['id' => $soutenance->getId()]);
        }

        //formulaire de modification soutenance : salle, date et type de soutenance
        $completSoutenance = new CompletSoutenance();
        $completSoutenance->setSalle($soutenance->getSalle())
            ->setDate(($soutenance->getDate()))
            ->setTypeSoutenance($soutenance->getType());
        $formSoutenance = $this->createForm(CompletSoutenanceType::class, $completSoutenance);
        $formSoutenance->handleRequest($request);

        if ($formSoutenance->isSubmitted() && $formSoutenance->isValid()) {
            /*dd($formSoutenance->getData()->getSalle()); 
            //recuperation en get
            // dd($request->request->get("complet_soutenance")["salle"]); //pour la recupeation en post
             */

            //on va pre remplir l'objet soutenance pour la mise a jour
            $soutenance->setSalle($formSoutenance->getData()->getSalle())
                ->setType($formSoutenance->getData()->getTypeSoutenance())
                ->setDate($formSoutenance->getData()->getDate());
            $this->em->persist($soutenance);
            $this->em->flush();
            $this->addFlash(
                'success',
                'Soutenance Modifiée avec succès'
            );
            return $this->redirectToRoute('gest_soutenance');
        }

        //renvoyons les etudiants afin de les ajouter lors de la modification
        if (
            $request->request->get("afficheEleve")
            // && $request->request->get("afficheEleve") == 'etudiant'
        ) {
            $eleves = $etudiantRepository->findAll();
            $ligne_eleve = '<tr>
                                <td>
                                    <select class="form-control idEtu" id="select_eleve[]" name="select_eleve[]">
                                        <option>choisir etudiant</option>';
            foreach ($eleves as  $eleve) {
                $ligne_eleve .= '
                   <option value="' . $eleve->getId() . '">' . $eleve->getPrenom() . '&nbsp;' . $eleve->getNom() . '</option>
                                            ';
            }
            $ligne_eleve .= '
                                    </select>
                                </td>
                                <td>
                                    <input type="text" class="form-control encadreur" name="encadreur[]" id="encadreur[]" readonly>
                                </td>
                                <td>
                                    <input type="text" class="form-control theme" name="theme[]" id="theme[]" readonly>
                                </td>
                                <td>
                                    <input type="number" id="note[]" name="note[]" class="form-control note" required>
                                </td>
                                <td>
                                     <a href="#" class="btn btn-danger remove_etudiant">-</a>
                                 </td>
                            </tr>';
            return new JsonResponse($ligne_eleve, 200);
        }

        //remplissons les champs en fonction de l'etudiant selectionne
        if (
            !empty($request->request->get('idEtuShow'))
            && $request->request->get('idEtuShow') != null
        ) {

            $id = (int)$request->request->get('idEtuShow');
            $etudiant = $etudiantRepository->find($id);
            //creons un tableau pour stocker le nom de l'encadreur et le theme de memoire
            $response = [
                "pren_encadreur" => $etudiant->getEncadreur()->getPrenom(),
                "nom_encadreur" => $etudiant->getEncadreur()->getNom(),
                "theme" => $etudiant->getMemoire()->getTheme()
            ];
            return new JsonResponse($response, 200);
        }

        //Ajout d'un etudiant manquant à la soutenance durant la modification de la soutenance
        $reponse = new Reponse();
        $formReponseAjoutEtudiant = $this->createForm(ReponseEtudiantType::class, $reponse);
        $formReponseAjoutEtudiant->handleRequest($request);

        if (
            $formReponseAjoutEtudiant->isSubmitted() &&
            $formReponseAjoutEtudiant->isValid()
        ) {
            //dd($request);

            if (
                !empty($request->request->get("reponse_etudiant")["reponse"]) &&
                $request->request->get("reponse_etudiant")["reponse"] != null
                &&
                $request->request->get("reponse_etudiant")["reponse"] == "oui"
            ) {
                //mettons a jour la note de l'etudiant apres avoir soutenu
                //on a un tableau d'eleve et pour chaque eleve il faut modifier sa note
                $t_eleve = $request->request->get("select_eleve");
                $t_note = $request->request->get("note");

                if (!empty($t_eleve)) {
                    for ($j = 0; $j < count($t_eleve); $j++) {
                        $etu_soutenance = new EtudiantSoutenances();

                        $etu_soutenance->setEtudiant($etudiantRepository->find($t_eleve[$j]));
                        $etu_soutenance->setSoutenance($soutenance);
                        //modifier la note de l'etudiant
                        $etu_soutenance->setNoteSoutenance($t_note[$j]);
                        //on persist les donnees et on modifie les notes des etudiants
                        $this->em->persist($etu_soutenance);
                        $this->em->flush();
                    }
                }
                return $this->redirectToRoute('edit_soutenance', ['id' => $soutenance->getId()]);
            }
        }


        return $this->render(
            "index/edit_soutenance.html.twig",
            [
                'soutenance' => $soutenance,
                'etudiants_soutenance' => $etudiants_soutenance,
                'jurys' => $jurys,
                'formSoutenance' => $formSoutenance->createView(),
                'formReponse' => $formReponse->createView(),
                'formReponseAjoutEtudiant' => $formReponseAjoutEtudiant->createView()
            ]
        );
    }

    /**
     * @Route("/gestsoutenance/del/{id<\d+>}/jury",name="del_jury",methods={"POST","GET"})
     * @IsGranted("ROLE_DIRECTEUR")
     */
    public function delete_jury_in_soutenance(Request $request, Jury $jury, ObjectManager $manager)
    {
        $id = $request->request->get('idJury');
        if (!empty($id)) {
            $manager->remove($jury);
            $manager->flush();

            //renvoyons le message en ajax sous forme de json
            $reponse = 'success';
            return new JsonResponse($reponse, 200);
        } else {
            $reponse = 'failed';
            return new JsonResponse($reponse, 400);
        }
    }

     /**
     * @Route("/gestsoutenance/del/{id<\d+>}/etu",name="del_eleve",methods={"POST","GET"})
     * @IsGranted("ROLE_DIRECTEUR")
     */
    public function delete_eleve_in_soutenance(Request $request, EtudiantSoutenances $etudiant)
    {
        $id = $request->request->get('idEleve');
        if (!empty($id)) {
             $this->em->remove($etudiant);
             $this->em->flush();

            //renvoyons le message en ajax sous forme de json
            $reponse = 'success';
            return new JsonResponse($reponse, 200);
        } else {
            $reponse = 'failed';
            return new JsonResponse($reponse, 400);
        }
    }
}
