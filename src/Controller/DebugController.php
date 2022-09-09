<?php

namespace App\Controller;

use App\Util\Dns;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use function Symfony\Component\String\u;

#[Route('/_debug', name: 'app_debug_')]
class DebugController extends AbstractController
{
    #[Route('/dns', name: 'dns')]
    public function dns(): Response
    {
        $host = 'www.google.com';
        $ip = Dns::getA($host);

        $contents = [
            'host' => $host,
            'ip' => $ip,
        ];

        return $this->render('debug/index.html.twig', ['contents' => $contents]);
    }

    #[Route('/net', name: 'net')]
    public function net(Request $request): Response
    {
        $schemeDomain = u($request->getSchemeAndHttpHost())
                    ->replaceMatches('/^(.*)(:[0-9]+)$/', function ($match) {
                        return $match[1];
                    });

        $contents = [
            'scheme_domain' => $schemeDomain,
            'path' => $request->getPathInfo(),
        ];

        return $this->render('debug/index.html.twig', ['contents' => $contents]);
    }
}
