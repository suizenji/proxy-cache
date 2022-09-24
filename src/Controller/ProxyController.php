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
    // TODO headers(encoding)
    #[Route('/proxy', name: 'app_proxy')]
    public function index(
        Request $request,
        HttpClientInterface $client,
        Recorder $recorder,
        CacheModerator $cacheModerator,
    ): Response {
        $uuid = Uuid::v1()->generate();
        $recorder->recordRequest($uuid, $request);

        if ($cacheModerator->shouldCachedResponse($request)) {
            return $cacheModerator->cachedResponse($request);
        }

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
        unset($headers['transfer-encoding']);
        unset($headers['Transfer-encoding']);
        unset($headers['Transfer-Encoding']);
        unset($headers['TRANSFER_ENCODING']);
        $headers['HOST'] = $host;

        return new Response($content, $status, $headers);
    }
}
