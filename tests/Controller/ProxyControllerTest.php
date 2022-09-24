<?php

namespace App\Tests\Controller;

use App\Controller\ProxyController as Controller;
use App\Service\CacheModerator;
use App\Service\Recorder;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ProxyControllerTest extends KernelTestCase
{
    public function testIndex(): void
    {
        $response = $this->action('https://www.apple.com');
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testIndexCache(): void
    {return; // TODO data fixture
        // $response = $this->action('https://localhost/foo/bar');
        // $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('foo', $response->getContent());
    }

    private function action($uri)
    {
        $container = static::getContainer();
        $controller = $container->get(Controller::class);
        $request = Request::create($uri);
        $client = $container->get(HttpClientInterface::class);
        $recorder = $container->get(Recorder::class);
        $cacheModerator = $container->get(CacheModerator::class);
        return $controller->index($request, $client, $recorder, $cacheModerator);
    }
}
