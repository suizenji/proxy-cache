<?php

namespace App\Controller;

use App\Util\Dns;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DnsController extends AbstractController
{
    #[Route('/dns', name: 'app_dns')]
    public function index(): Response
    {
        $host = 'www.google.com';
        $ip = Dns::getA($host);

        return $this->render('dns/index.html.twig', [
            'controller_name' => 'DnsController',
            'host' => $host,
            'ip' => $ip,
        ]);
    }
}
