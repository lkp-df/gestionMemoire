<?php

namespace App\Controller;

use App\Entity\EtudiantSoutenances;
use App\Entity\Soutenance;
use App\Entity\PositionJury;
use App\Form\PositionJuryType;
use App\Repository\EncadreurRepository;
use App\Repository\EtudiantSoutenancesRepository;
use App\Repository\JuryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @IsGranted("ROLE_DIRECTEUR")
 */
class ModalController extends AbstractController
{
    private $em;
    public function __construct(EntityManagerInterface $entityManagerInterface)
    {
        $this->em = $entityManagerInterface;
    }

    /**
     * @Route("/gestsoutenance/edit/{id<\d+>}/position/{id_jury<\d+>}",name="edit_soutenance_show_position_jury",methods={"POST","GET"})
     */
    public function show_position_jury(
        Request $request,
        Soutenance $soutenance,
        JuryRepository $jury,
        $id_jury
    ) {
        //modale edition position jurry
        if (
            $request->request->get('modal')
            && $request->request->get('modal') == 'position_jury'
            && $request->request->get('id_jury')

        ) {
            //allons chercher le jury selectionné
            $jury = $jury->find($request->request->get('id_jury'));

            return new JsonResponse(
                [
                    'reponse' => true,
                    'content' => $this->render(
                        'modals/_titre_jury.html.twig',
                        [
                            'jury' => $jury,
                            'soutenance' => $soutenance,
                            //'formJuryType'=>$formJuryType->createView()
                        ]
                    )->getContent()
                ]
            );
        } else {
            return new JsonResponse(
                ['reponse' => false]
            );
        }
    }

    /**
     * @Route("/gestsoutenance/edit/{id<\d+>}/up-jury/{id_jury<\d+>}",name="edit_soutenance_update_position_jury",methods={"POST","GET"})
     */
    public function update_jury_titre(
        Request $request,
        Soutenance $soutenance,
        JuryRepository $juryRepository,
        $id_jury
    ) {
        //modification du titre du jury
        if (
            $request->request->get('jury')
            && $request->request->get('jury') == 'modif'
            && $request->request->get('valJury')
        ) {
            $jury = $juryRepository->find($id_jury);
            if ($request->request->get('valJury') == "pres") {
                $jury->setTitre("president");
                $reponse = 'success';
            } else {
                $jury->setTitre("membre");
                $reponse = 'success';
            }

            $this->em->persist($jury);
            $this->em->flush();
            return new JsonResponse($reponse, 200);
        }
    }

    /**
     * @Route("/gestsoutenance/edit/{id<\d+>}/info-jury/{id_jury<\d+>}",name="edit_soutenance_show_jury_in_soutenance",methods={"POST","GET"})
     */
    public function show_jury_in_soutenance(
        Request $request,
        Soutenance $soutenance,
        JuryRepository $jury,
        $id_jury
    ) {
        //modale edition position jurry
        if (
            $request->request->get('modal')
            && $request->request->get('modal') == 'infoJury'
            && $request->request->get('id_jury')

        ) {
            //allons chercher le jury selectionné
            $jury = $jury->find($request->request->get('id_jury'));

            return new JsonResponse(
                [
                    'reponse' => true,
                    'content' => $this->render(
                        'modals/_show_jury_in_soutenance.html.twig',
                        [
                            'jury' => $jury,
                            'soutenance' => $soutenance,
                        ]
                    )->getContent()
                ]
            );
        } else {
            return new JsonResponse(
                ['reponse' => false]
            );
        }
    }

    /**
     * @Route("/gestsoutenance/edit/{id<\d+>}/info-etu/{id_etu<\d+>}",name="edit_soutenance_show_eleve_in_soutenance",methods={"POST","GET"})
     */
    public function show_eleve_in_soutenance(
        Request $request,
        Soutenance $soutenance,
        //EtudiantSoutenances $etudiantSoutenance,
        EtudiantSoutenancesRepository $etudiantSoutenancesRepository
    ) {
        if (
            $request->request->get('modal') &&
            $request->request->get('modal') == 'infoEleve' &&
            $request->request->get('id_Eleve')
        ) {
            $etudiantSoutenance = $etudiantSoutenancesRepository->find($request->request->get('id_Eleve'));

            //renvoyons la modal remplie des infos
            return new JsonResponse(
                [
                    'reponse' => true,
                    'content' => $this->render(
                        'modals/_show_eleve_info.html.twig',
                        [
                            'soutenance' => $soutenance,
                            'etudiantSoutenance' => $etudiantSoutenance
                        ]
                    )->getContent(),
                ]
            );
        } else {
            return new JsonResponse(['reponse' => false]);
        }
    }

    /**
     * @Route("/gestsoutenance/edit/{id<\d+>}/mod-etu/{id_etu<\d+>}",name="edit_soutenance_show_eleve_in_soutenance_mod",methods={"POST","GET"})
     */
    public function show_note_jury_soutenance(
        Request $request,
        Soutenance $soutenance,
        EtudiantSoutenancesRepository $etudiantSoutenancesRepository,
        EncadreurRepository $encadreurRepository
    ) {
        if (
            $request->request->get('modal') &&
            $request->request->get('modal') == 'modifEleve' &&
            $request->request->get('idEleveMod')
        ) {
            $etudiantSoutenance = $etudiantSoutenancesRepository->find($request->request->get('idEleveMod'));

            return new JsonResponse(
                [
                    'reponse' => true,
                    'content' => $this->render(
                        'modals/_edit_eleve.html.twig',
                        [
                            'soutenance' => $soutenance,
                            'etudiantSoutenance' => $etudiantSoutenance,
                            'encadreurs' => $encadreurRepository->findAll(),
                            'noteActuelle' => $etudiantSoutenance->getNoteSoutenance(),
                            'profActuel' => $etudiantSoutenance->getEtudiant()->getEncadreur()
                        ]
                    )->getContent()
                ]
            );
        }
    }

    /**
     * @Route("/gestsoutenance/edit/{id<\d+>}/up-etu/{id_etu<\d+>}",name="edit_soutenance_update_eleve_note_jury_soutenance",methods={"POST","GET"})
     */
    public function update_eleve_note_jury(
        Request $request,
        Soutenance $soutenance,
        EncadreurRepository $encadreurRepository,
        EtudiantSoutenancesRepository $etudiantSoutenancesRepository
    ) {
        if (
            $request->request->get('modification') &&
            $request->request->get('modification') == 'editNoteSoutenance' &&
            $request->request->get('idEtuEdit') &&
            $request->request->get('noteEtu')
        ) {
            //trouvons l'etudiant à modifier
            $etudiantSoutenance = $etudiantSoutenancesRepository->find($request->request->get('idEtuEdit'));

            //pour la modification de la note d'un etudiant
            if ($etudiantSoutenance) {
                $etudiantSoutenance->setNoteSoutenance($request->request->get('noteEtu'));
                $reponse = 'success';
                $this->em->persist($etudiantSoutenance);
                $this->em->flush();
            } else {
                $reponse = 'failed';
            }

            return new JsonResponse($reponse, 200);

            return $this->redirectToRoute('edit_soutenance', ['id' => $soutenance->getId()]);
        }

        //pour la modification du prof de memoire d'un etudiant
        if (
            $request->request->get('modification') &&
            $request->request->get('modification') == 'editProfSoutenance' &&
            $request->request->get('idEtuEdit') &&
            $request->request->get('encadreurId')
        ) {
            //trouvons l'etudiant à modifier
            $etudiantSoutenance = $etudiantSoutenancesRepository->find($request->request->get('idEtuEdit'));

            if ($etudiantSoutenance) {
                //recuperons l'etudiant, car c'est l'etudiant qu'on a son encadreur
                $etudiantSoutenance->getEtudiant()->setEncadreur($encadreurRepository->find($request->request->get('encadreurId')));
                $reponse = 'success';
                $this->em->persist($etudiantSoutenance);
                $this->em->flush();
            } else {
                $reponse = 'failed';
            }

            return new JsonResponse($reponse, 200);
            return $this->redirectToRoute('edit_soutenance', ['id' => $soutenance->getId()]);
            
        }
    }
}
