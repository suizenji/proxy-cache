<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DnsController extends AbstractController
{
    #[Route('/dns', name: 'app_dns')]
    public function index(): Response
    {
        return $this->render('dns/index.html.twig', [
            'controller_name' => 'DnsController',
        ]);
    }
}
