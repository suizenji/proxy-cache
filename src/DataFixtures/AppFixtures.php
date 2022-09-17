<?php

namespace App\DataFixtures;

use App\Entity\Request;
use App\Entity\Response;
use App\Entity\Header;
use App\Entity\Body;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $tranId = '123abc';
        $createdAt = new \DateTimeImmutable();
        self::recordRequest($manager, $tranId, createdAt: $createdAt);
        self::recordHeaders($manager, $tranId, [
            'HOST' => 'localhost',
            'User-Agent' => 'Nginx',
        ], createdAt: $createdAt);
    }

    private static function recordRequest($manager, $tranId, $method = 'GET', $uri = '/', $version = 1.1, $createdAt = new \DateTimeImmutable())
    {
        $entity = (new Request)
            ->setTranId($tranId)
            ->setMethod($method)
            ->setUri($uri)
            ->setVersion($version)
            ->setCreatedAt($createdAt)
            ;

        $manager->persist($entity);
        $manager->flush();
    }

    private static function recordHeaders($manager, $tranId, $headers = [], $createdAt = new \DateTimeImmutable())
    {
        foreach ($headers as $name => $value) {
            $entity = (new Header)
                    ->setTranId($tranId)
                    ->setName($name)
                    ->setValue($value)
                    ->setCreatedAt($createdAt)
                    ;

            $manager->persist($entity);
            $manager->flush();
        }
    }
}
