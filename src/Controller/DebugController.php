<?php

namespace App\Controller;

use App\Entity\Http;
use App\Repository\HttpBodyRepository;
use App\Repository\HttpContextRepository;
use App\Repository\HttpHeaderRepository;
use App\Service\Recorder;
use App\Util\Dns;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

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
//        $scheme = 'http://';
        $host = 'google.com';
//        $host = 'localhost:8081';
        $url = $scheme.$host;

        // http client default hreaders
        // GET / HTTP/1.1\r
        // Connection: close\r
        // Accept: */*\r
        // Accept-Encoding: gzip\r
        // User-Agent: Symfony HttpClient/Native\r
        // Host: localhost:8081\r

        $headers = $request->server->getHeaders();
        $headers['HOST'] = $host;
        $headers['Connection'] = 'close';

        /** @var ResponseInterface $response */
        $response = $client->request($method, $url, [
            'headers' => $headers,
        ]);

        return new Response($response->getContent());
    }

    #[Route('/pro', name: 'pro')]
    public function pro(
        Request $request,
        HttpClientInterface $client,
        Recorder $recorder,
    ): Response {
        $uuid = Uuid::v1()->generate();
        $recorder->recordRequest($uuid, $request);

        $method = $request->getMethod();

//        $host = 'localhost.com:8081';
//        $host = 'www.apple.com';
        $host = 'www.apple.com:443';
        $domain = (string) u($host)->replaceMatches('/^(.*)(:[0-9]+)$/', function ($match) {
            return $match[1];
        });

        $scheme = 'https://';
//        $scheme = 'http://';
        $ip = Dns::getA($domain);
//        $ip = '127.0.0.1';
        $schemeAndHttpHost = $scheme.$host;
        $schemeAndIp = (string) u($schemeAndHttpHost)->replace($domain, $ip);

        $path = '/store';
        $query = $request->getQueryString();
        $uri = $schemeAndIp.$path.'?'.$query;

        $headers = $request->server->getHeaders();
        unset($headers['content-length']);
        unset($headers['Content-length']);
        unset($headers['Content-Length']);
        unset($headers['CONTENT_LENGTH']);
        unset($headers['host']);
        $headers['HOST'] = $host;
        unset($headers['connection']);
        $headers['Connection'] = 'close';

        /** @var ResponseInterface $response */
        $response = $client->request($method, $uri, [
            'headers' => $headers,
            'body' => $request->getContent(),
            'verify_host' => false,
        ]);

        $recorder->recordResponse($uuid, $response);

        $content = $response->getContent();
        $status = $response->getStatusCode();
        $headers = $response->getHeaders();

        unset($headers['content-encoding']);
        unset($headers['Content-encoding']);
        unset($headers['Content-Encoding']);
        unset($headers['CONTENT_ENCODING']);
        unset($headers['content-length']);
        unset($headers['Content-length']);
        unset($headers['Lontent-Length']);
        unset($headers['LONTENT_LENGTH']);
        unset($headers['connection']);
        unset($headers['Connection']);
        $headers['HOST'] = $host;

        return new Response($content, $status, $headers);
    }

    #[Route('/view', name: 'view')]
    public function view(
        Request $request,
        HttpContextRepository $repoContext,
        HttpHeaderRepository $repoHeader,
        HttpBodyRepository $repoBody,
    ): Response {
        // TODO offset, order
        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $repoContext->getPaginator($offset);
        $pages = $paginator->count() / HttpContextRepository::PAGINATOR_PER_PAGE;
#        var_dump($paginator->count());
#        var_dump($pages);
#        foreach ($paginator as $foo) {}

        // $requestContexts = $repoContext->findBy([
        //     'type' => 'send',
        // ]);

        $tranList = [];
        // foreach ($requestContexts as $requestContext) {
        foreach ($paginator as $requestContext) {
            $tranId = $requestContext->getTranId();

            $requestHeaders = $repoHeader->findBy(
                ['tranId' => $tranId, 'type' => Http::TYPE_SEND]
            );

            $requestBody = $repoBody->findOneBy(
                ['tranId' => $tranId, 'type' => Http::TYPE_SEND]
            );

            $responseContext = $repoContext->findOneBy(
                ['tranId' => $tranId, 'type' => Http::TYPE_RECV]
            );

            $responseHeaders = $repoHeader->findBy(
                ['tranId' => $tranId, 'type' => Http::TYPE_RECV]
            );

            $responseBody = $repoBody->findOneBy(
                ['tranId' => $tranId, 'type' => Http::TYPE_RECV]
            );

            $tranList[] = [
                'id' => $tranId,
                'time' => $requestContext->getCreatedAt(),
                'req_cont' => $requestContext,
                'req_heads' => $requestHeaders,
                'req_body' => $requestBody,
                'res_cont' => $responseContext,
                'res_heads' => $responseHeaders,
                'res_body' => $responseBody,
            ];
        }

        return $this->render('debug/view.html.twig', [
            'tran_list' => $tranList,
        ]);
    }
}
