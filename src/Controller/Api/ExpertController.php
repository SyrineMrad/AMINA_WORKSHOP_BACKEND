<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ExpertController extends AbstractController
{
    /**
     * @Route("/expert", name="expert")
     */
    public function index()
    {
        return $this->render('expert/index.html.twig', [
            'controller_name' => 'ExpertController',
        ]);
    }
}
