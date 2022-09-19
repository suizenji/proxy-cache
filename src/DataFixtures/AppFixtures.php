<?php

namespace App\DataFixtures;

use App\Entity\Http;
use App\Entity\HttpContext;
use App\Entity\HttpHeader;
use App\Entity\HttpBody;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // normal
        $tranId = 'A001';
        $createdAt = new \DateTimeImmutable();

        self::recordContext($manager, $tranId, Http::TYPE_SEND, 'GET', '/', 'HTTP/1.1', $createdAt);
        self::recordHeaders($manager, $tranId, Http::TYPE_SEND, [
            'HOST' => 'localhost',
            'User-Agent' => 'Nginx',
        ]);
        self::recordBody($manager, $tranId, Http::TYPE_SEND);

        self::recordContext($manager, $tranId, Http::TYPE_RECV, 'HTTP/1.1', '200', 'OK', $createdAt);
        self::recordHeaders($manager, $tranId, Http::TYPE_RECV, [
            'Content-Length' => '6',
        ]);
        self::recordBody($manager, $tranId, Http::TYPE_RECV, 'foo');

        // POST
        $tranId = 'A002';
        sleep(1);
        $createdAt = new \DateTimeImmutable();

        self::recordContext($manager, $tranId, Http::TYPE_SEND, 'POST', '/data/post', 'HTTP/1.1', $createdAt);
        self::recordHeaders($manager, $tranId, Http::TYPE_SEND, [
            'HOST' => 'localhost',
            'Content-Type' => 'application/json',
        ]);
        self::recordBody($manager, $tranId, Http::TYPE_SEND, '{"key": "value"}');

        self::recordContext($manager, $tranId, Http::TYPE_RECV, 'HTTP/1.1', '404', 'Not Found', $createdAt);
        self::recordHeaders($manager, $tranId, Http::TYPE_RECV, []);
        self::recordBody($manager, $tranId, Http::TYPE_RECV);

    }

    private static function recordContext($manager, $tranId, $type, $f1, $f2, $f3, $createdAt)
    {
        $entity = (new HttpContext)
            ->setTranId($tranId)
            ->setType($type)
            ->setF1($f1)
            ->setF2($f2)
            ->setF3($f3)
            ->setCreatedAt($createdAt)
            ;

        $manager->persist($entity);
        $manager->flush();
    }

    private static function recordHeaders($manager, $tranId, $type, $headers = [])
    {
        foreach ($headers as $name => $value) {
            $entity = (new HttpHeader)
                    ->setTranId($tranId)
                    ->setType($type)
                    ->setName($name)
                    ->setValue($value)
                    ;

            $manager->persist($entity);
            $manager->flush();
        }
    }

    private static function recordBody($manager, $tranId, $type, $content = '')
    {
        $entity = (new HttpBody)
                ->setTranId($tranId)
                ->setType($type)
                ->setContent($content);
        ;

        $manager->persist($entity);
        $manager->flush();
    }
}
