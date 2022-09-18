<?php

namespace App\DataFixtures;

use App\Entity\RequestContext;
use App\Entity\RequestHeader;
use App\Entity\ResponseContext;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $tranId = '123abc';
        $createdAt = new \DateTimeImmutable();
        self::recordRequestContext($manager, $tranId, createdAt: $createdAt);
        self::recordRequestHeaders($manager, $tranId, [
            'HOST' => 'localhost',
            'User-Agent' => 'Nginx',
        ], createdAt: $createdAt);

        self::recordResponseContext($manager, $tranId, createdAt: $createdAt);
    }

    private static function recordRequestContext($manager, $tranId, $method = 'GET', $uri = '/', $version = 1.1, $createdAt = new \DateTimeImmutable())
    {
        $entity = (new RequestContext)
            ->setTranId($tranId)
            ->setMethod($method)
            ->setUri($uri)
            ->setVersion($version)
            ->setCreatedAt($createdAt)
            ;

        $manager->persist($entity);
        $manager->flush();
    }

    private static function recordRequestHeaders($manager, $tranId, $headers = [], $createdAt = new \DateTimeImmutable())
    {
        foreach ($headers as $name => $value) {
            $entity = (new RequestHeader)
                    ->setTranId($tranId)
                    ->setName($name)
                    ->setValue($value)
                    ->setCreatedAt($createdAt)
                    ;

            $manager->persist($entity);
            $manager->flush();
        }
    }

    private static function recordResponseContext($manager, $tranId, $version = 1.1, $status = 200, $message = 'OK', $createdAt = new \DateTimeImmutable())
    {
        $entity = (new ResponseContext)
            ->setTranId($tranId)
            ->setVersion($version)
            ->setStatus($status)
            ->setMessage($message)
            ->setCreatedAt($createdAt)
            ;

        $manager->persist($entity);
        $manager->flush();
    }
}
