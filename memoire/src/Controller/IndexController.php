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
use App\Entity\PositionJury;
use App\Form\CompletSoutenanceType;
use App\Form\EditInfoEncadreurType;
use App\Form\EditProfilEncadreurType;
use App\Form\FormEtudiantMemoireType;
use App\Form\PositionJuryType;
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
