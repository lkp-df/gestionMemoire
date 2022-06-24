<?php

namespace App\Controller;

use DateTime;
use App\Entity\Jury;
use App\Entity\User;
use App\Form\UserType;
use App\Entity\Filiere;
use App\Entity\Memoire;
use App\Entity\Reponse;
use App\Entity\EditUser;
use App\Entity\Etudiant;
use App\Entity\Encadreur;
use App\Form\FiliereType;
use App\Form\ReponseType;
use App\Entity\Soutenance;
use App\Entity\EditFiliere;
use App\Entity\EditMemoire;
use App\Form\EncadreurType;
use App\Entity\MonEncadreur;
use App\Entity\EditEncadreur;
use App\Form\EditFiliereType;
use App\Form\EditMemoireType;
use App\Form\EditEtudiantType;
use App\Entity\EtudiantMemoire;
use App\Form\EditEncadreurType;
use App\Entity\EditInfoEtudiant;
use App\Entity\CompletSoutenance;
use App\Entity\EtudiantSoutenance;
use App\Repository\JuryRepository;
use App\Repository\UserRepository;
use App\Entity\EtudiantSoutenances;
use App\Form\CompletSoutenanceType;
use App\Form\EditInfoEncadreurType;
use App\Form\EditProfilEncadreurType;
use App\Form\FormEtudiantMemoireType;
use App\Repository\FiliereRepository;
use App\Repository\MemoireRepository;
use App\Repository\EtudiantRepository;
use App\Repository\EncadreurRepository;
use Doctrine\Persistence\ObjectManager;
use App\Repository\SoutenanceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\EtudiantSoutenancesRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Translation\TranslatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class IndexController extends AbstractController
{
    private $em;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }
    /**
     * @Route("/", name="")
     * @Route("/index", name="index")
     */
    public function index(TranslatorInterface $translator): Response
    {
        $message = $translator->trans("Welcome in the management of the memoires, made by PMD Developper to help you understand symfony 4");
        return $this->render('index/index.html.twig', [
            'message' => $message,
        ]);
    }
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
     * @Route("/gestsoutenance/edit/{id<\d+>}",name="edit_soutenance",methods={"POST","GET"})
     * @IsGranted("ROLE_DIRECTEUR")
     */
    public function edit_soutenance(
        Request $request,
        Soutenance $soutenance,
        EtudiantSoutenancesRepository $repo_sout,
        JuryRepository $jury,
        EncadreurRepository $en
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
                      <a href="#" class="btn btn-danger" id="remove_jury">-</a>
                    </td>
             </tr>';
            return new JsonResponse($ligne_jury, 200);
        }

        //formulaire pour ajouter un element manqué durant la modification
        $reponse = new Reponse();
        $formReponse = $this->createForm(ReponseType::class, $reponse);
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
        }

        //formulaire de modification soutenance
        $completSoutenance = new CompletSoutenance();
        $completSoutenance->setSalle($soutenance->getSalle())
            ->setDate(($soutenance->getDate()))
            ->setTypeSoutenance($soutenance->getType());
        $formSoutenance = $this->createForm(CompletSoutenanceType::class, $completSoutenance);
        $formSoutenance->handleRequest($request);

        if ($formSoutenance->isSubmitted() && $formSoutenance->isValid()) {
            /*             dd($formSoutenance->getData()->getSalle());  //recuperation en get
 */           // dd($request->request->get("complet_soutenance")["salle"]); //pour la recupeation en post
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
        return $this->render(
            "index/edit_soutenance.html.twig",
            [
                'soutenance' => $soutenance,
                'etudiants_soutenance' => $etudiants_soutenance,
                'jurys' => $jurys,
                'formSoutenance' => $formSoutenance->createView(),
                'formReponse' => $formReponse->createView()
            ]
        );
    }

    /**
     * @Route("/utilisateurs",name="gestion_utulisateur")
     * @IsGranted("ROLE_ADMIN")
     */
    public function gest_utilisateur(UserRepository $users)
    {
        $users = $users->findAll();
        return $this->render(
            "index/utilisateur.html.twig",
            ["users" => $users]
        );
    }

    /**
     * @Route("/utilisateurs/edit/{id<\d+>}", name="edit_utilisateur")
     * @IsGranted("ROLE_ADMIN")
     */
    public function edit_users(User $user, Request $request, ObjectManager $manager)
    {
        $user_edit = new EditUser();
        $user_edit->setUsername($user->getUsername());
        $user_edit->setRoles($user->getRoles());

        $formRoles = $this->createForm(UserType::class, $user_edit);
        $formRoles->handleRequest($request);

        if ($formRoles->isSubmitted() && $formRoles->isValid()) {
            $user->setRoles($request->request->get("user")["roles"]);
            $user->setUsername($request->request->get("user")["username"]);

            $manager->persist($user);
            $manager->flush();

            return $this->redirectToRoute('gestion_utulisateur');
        }
        return $this->render(
            "index/edit_utilisateur.html.twig",
            ["formRoles" => $formRoles->createView()]
        );
    }
}
