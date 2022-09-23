<?php

namespace App\DataFixtures;

use App\Entity\Http;
use App\Entity\HttpContext;
use App\Entity\HttpHeader;
use App\Entity\HttpBody;
use App\Service\Recorder;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // normal
        $tranId = 'A001';
        $createdAt = new \DateTimeImmutable();

        Recorder::recordContext($manager, $tranId, Http::TYPE_SEND, 'GET', 'http://localhost/', 'HTTP/1.1', $createdAt);
        Recorder::recordHeaders($manager, $tranId, Http::TYPE_SEND, [
            'HOST' => ['localhost'],
            'User-Agent' => ['Nginx', 'iPhone'],
        ]);
        Recorder::recordBody($manager, $tranId, Http::TYPE_SEND);

        Recorder::recordContext($manager, $tranId, Http::TYPE_RECV, 'HTTP/1.1', '200', 'OK', $createdAt);
        Recorder::recordHeaders($manager, $tranId, Http::TYPE_RECV, [
            'Content-Length' => ['6'],
        ]);
        Recorder::recordBody($manager, $tranId, Http::TYPE_RECV, 'foo');

        // POST
        $tranId = 'A002';
        sleep(1);
        $createdAt = new \DateTimeImmutable();

        Recorder::recordContext($manager, $tranId, Http::TYPE_SEND, 'POST', 'https://localhost/data/post', 'HTTP/1.1', $createdAt);
        Recorder::recordHeaders($manager, $tranId, Http::TYPE_SEND, [
            'HOST' => ['localhost'],
        ]);
        Recorder::recordBody($manager, $tranId, Http::TYPE_SEND, file_get_contents(__FILE__));

        Recorder::recordContext($manager, $tranId, Http::TYPE_RECV, 'HTTP/1.1', '404', 'Not Found', $createdAt);
        Recorder::recordHeaders($manager, $tranId, Http::TYPE_RECV, []);
        Recorder::recordBody($manager, $tranId, Http::TYPE_RECV);

    }
}
