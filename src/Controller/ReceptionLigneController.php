<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReceptionLigneController extends AbstractController
{
    #[Route('/reception/ligne', name: 'app_reception_ligne')]
    public function index(): Response
    {
        return $this->render('reception_ligne/index.html.twig', [
            'controller_name' => 'ReceptionLigneController',
        ]);
    }
}
