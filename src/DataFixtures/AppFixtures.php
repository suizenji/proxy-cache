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
        $tranId = '123abc';
        $createdAt = new \DateTimeImmutable();

        self::recordRequestContext($manager, $tranId, createdAt: $createdAt);
        self::recordHeaders($manager, $tranId, Http::TYPE_SEND, [
            'HOST' => 'localhost',
            'User-Agent' => 'Nginx',
        ]);
        self::recordBody($manager, $tranId, Http::TYPE_SEND);

        self::recordResponseContext($manager, $tranId, createdAt: $createdAt);
        self::recordHeaders($manager, $tranId, Http::TYPE_RECV, [
            'Content-Length' => '6',
        ]);
        self::recordBody($manager, $tranId, Http::TYPE_RECV, 'foo');
    }

    private static function recordRequestContext($manager, $tranId, $method = 'GET', $uri = '/', $version = 'HTTP/1.1', $createdAt = new \DateTimeImmutable())
    {
        $entity = (new HttpContext)
            ->setTranId($tranId)
            ->setType(Http::TYPE_SEND)
            ->setF1($method)
            ->setF2($uri)
            ->setF3($version)
            ->setCreatedAt($createdAt)
            ;

        $manager->persist($entity);
        $manager->flush();
    }

    private static function recordResponseContext($manager, $tranId, $version = 'HTTP/1.1', $status = 200, $message = 'OK', $createdAt = new \DateTimeImmutable())
    {
        $entity = (new HttpContext)
            ->setTranId($tranId)
            ->setType(Http::TYPE_RECV)
            ->setF1($version)
            ->setF2($status)
            ->setF3($message)
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
