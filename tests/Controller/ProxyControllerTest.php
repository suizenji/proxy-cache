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
        $container = static::getContainer();
        $controller = $container->get(Controller::class);
        $request = Request::create('https://www.apple.com/');
        $client = $container->get(HttpClientInterface::class);
        $recorder = $container->get(Recorder::class);
        $cacheModerator = $container->get(CacheModerator::class);
        $response = $controller->index($request, $client, $recorder, $cacheModerator);

        $this->assertEquals(200, $response->getStatusCode());
    }
}
