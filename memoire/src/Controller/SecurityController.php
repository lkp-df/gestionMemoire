<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/registration", name="registration")
     */
    public function inscription(Request $request,UserPasswordEncoderInterface $encoder,ObjectManager $manager): Response
    {   
        $user = new User();
        $formUser = $this->createForm(RegistrationType::class,$user);

        $formUser->handleRequest($request);

        if($formUser->isSubmitted()&&$formUser->isValid()){
            //hachons les mot de passe 
            $user->setPassword($encoder->encodePassword($user,$user->getPassword()));
            $manager->persist($user);
            $manager->flush();
            return $this->redirectToRoute("login");
        }
        return $this->render('security/index.html.twig', [
            'formUser' => $formUser->createView(),
        ]);
    }

    /**
     * @Route("/security_login",name="login")
     */
    public function login()
    {
        return $this->render("security/login.html.twig");
    }
    /**
     * @Route("/security_logout",name="logout")
     */
    public function logout()
    {
    }
}
