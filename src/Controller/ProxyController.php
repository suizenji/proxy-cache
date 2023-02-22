<?php

namespace App\Controller;

use function Symfony\Component\String\u;
use App\Util\Dns;
use App\Service\CacheModerator;
use App\Service\Recorder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

class ProxyController extends AbstractController
{
    #[Route('/proxy', name: 'app_proxy')]
    public function index(
        Request $request,
        HttpClientInterface $client,
        Recorder $recorder,
        CacheModerator $cacheModerator,
    ): Response {
        $uuid = Uuid::v1()->generate();

        if ($cacheRule = $cacheModerator->suggestCache($request)) {
            return $cacheModerator->createResponse($cacheRule, $request);
        }

        $recorder->recordRequest($uuid, $request);

        $method = $request->getMethod();

        $host = $request->getHost();
        $domain = (string) u($host)->replaceMatches('/^(.*)(:[0-9]+)$/', function ($match) {
            return $match[1];
        });

        $ip = Dns::getA($domain);
        $schemeAndHttpHost = $request->getSchemeAndHttpHost();
        $schemeAndIp = (string) u($schemeAndHttpHost)->replace($domain, $ip);

        $path = $request->getPathInfo();
        $query = $request->getQueryString();
        $uri = $schemeAndIp . $path . '?' . $query;

        $headers = $request->server->getHeaders();
        unset($headers['content-length']);
        $headers['host'] = $host;
        $headers['connection'] = 'close';

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
        unset($headers['content-length']);
        unset($headers['transfer-encoding']);
        $headers['host'] = $host;

        return new Response($content, $status, $headers);
    }
}
