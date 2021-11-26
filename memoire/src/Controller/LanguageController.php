<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LanguageController extends AbstractController
{
    /**
     * @Route("/language/{locale}", name="language")
     */
    public function changeLocaleLanguage($locale, Request $request): Response
    {
        #on stocke d'abord la langue demander dans la session
        $locale = $request->attributes->get('locale');

        $request->getSession()->set('_locale', $locale);

        $request->setLocale($request->getSession()->get('_locale', $locale));

        #on revient sur la page precedente
        return $this->redirect($request->headers->get('referer'));
    }
}
