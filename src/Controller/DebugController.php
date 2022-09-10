<?php

namespace App\Controller;

use App\Util\Dns;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
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
        $contents = [];

        $headers = $request->headers->all();
        foreach ($headers as $key => $value) {
            $contents[$key] = $value[0];
        }

        $schemeDomain = u($request->getSchemeAndHttpHost())
                    ->replaceMatches('/^(.*)(:[0-9]+)$/', function ($match) {
                        return $match[1];
                    });

        $contents += [
            'scheme_domain' => $schemeDomain,
            'path' => $request->getPathInfo(),
        ];

        return $this->render('debug/index.html.twig', ['contents' => $contents]);
    }

    #[Route('/req', name: 'req')]
    public function req(Request $request, HttpClientInterface $client): Response
    {
        $method = $request->getMethod();
        $scheme = 'https://';
#        $scheme = 'http://';
        $host = 'google.com';
#        $host = 'localhost:8081';
        $url = $scheme . $host;

# http client default hreaders
#GET / HTTP/1.1\r
#Connection: close\r
#Accept: */*\r
#Accept-Encoding: gzip\r
#User-Agent: Symfony HttpClient/Native\r
#Host: localhost:8081\r

        $headers = $request->server->getHeaders();
        $headers['HOST'] = $host;
        $headers['Connection'] = 'close';

        /** @var ResponseInterface $response */
        $response = $client->request($method, $url, [
            'headers' => $headers,
        ]);

        return new Response($response->getContent());
    }
}
